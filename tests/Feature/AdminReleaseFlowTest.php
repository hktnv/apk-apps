<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Contexts\IdentityAccess\Infrastructure\Persistence\Eloquent\AdminUserRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

final class AdminReleaseFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_upload_publish_update_check_and_download_flow(): void
    {
        $csrfToken = 'test-token';

        Storage::fake('apks');
        $admin = $this->createAdmin();

        $this->withSession(['_token' => $csrfToken])->post('/login', [
            '_token' => $csrfToken,
            'email' => 'admin@example.com',
            'password' => 'secret-password',
        ])->assertRedirect('/admin');

        $this->actingAs($admin)
            ->withSession(['_token' => $csrfToken])
            ->post('/admin/applications', [
                '_token' => $csrfToken,
                'name' => 'Example App',
                'package_name' => 'com.habersoft.example',
                'description' => 'Demo',
            ])
            ->assertRedirect();

        $applicationId = (string) DB::table('managed_applications')->value('id');
        $this->assertDatabaseHas('managed_applications', [
            'id' => $applicationId,
            'slug' => 'example-app',
        ]);
        $apk = $this->makeApkUpload();

        $this->actingAs($admin)
            ->withSession(['_token' => $csrfToken])
            ->post('/admin/applications/'.$applicationId.'/releases', [
                '_token' => $csrfToken,
                'version_code' => 13,
                'version_name' => '1.3.0',
                'release_notes' => 'Hata düzeltmeleri',
                'apk' => $apk,
            ])
            ->assertRedirect();

        $releaseId = (string) DB::table('apk_releases')->value('id');

        $this->get('/api/v1/artifacts/'.$releaseId.'/download')
            ->assertNotFound();

        $this->actingAs($admin)
            ->withSession(['_token' => $csrfToken])
            ->post('/admin/applications/'.$applicationId.'/releases/'.$releaseId.'/publish', [
                '_token' => $csrfToken,
                'channel' => 'stable',
                'force_update' => '1',
                'minimum_supported_version_code' => 10,
                'comment' => 'İlk stable yayın',
            ])
            ->assertRedirect();

        $this->getJson('/api/v1/applications/com.habersoft.example/channels/stable/update-check?current_version_code=12')
            ->assertOk()
            ->assertJsonPath('data.status', 'UPDATE_AVAILABLE')
            ->assertJsonPath('data.required', true)
            ->assertJsonPath('data.release.version_code', 13);

        $proxiedResponse = $this->withServerVariables([
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ])->getJson('/api/v1/applications/com.habersoft.example/channels/stable/update-check?current_version_code=12');

        $proxiedResponse->assertOk();
        self::assertStringStartsWith('https://', (string) $proxiedResponse->json('data.release.download_url'));

        $this->getJson('/api/v1/applications/com.habersoft.example/channels/stable/update-check?current_version_code=0')
            ->assertOk()
            ->assertJsonPath('data.status', 'UPDATE_AVAILABLE')
            ->assertJsonPath('data.required', true)
            ->assertJsonPath('data.current_version_code', 0)
            ->assertJsonPath('data.release.version_code', 13);

        $this->getJson('/api/v1/applications/com.habersoft.example/channels/stable/update-check?current_version_code=13')
            ->assertOk()
            ->assertJsonPath('data.status', 'UP_TO_DATE');

        $this->getJson('/api/v1/applications/com.habersoft.example/channels/stable/update-check?current_version_code=14')
            ->assertOk()
            ->assertJsonPath('data.status', 'CLIENT_AHEAD');

        $this->get('/api/v1/artifacts/'.$releaseId.'/download')
            ->assertOk()
            ->assertHeader('X-APK-SHA256');

        $this->assertDatabaseHas('application_statistics', [
            'application_id' => $applicationId,
            'update_check_count' => 5,
            'update_available_count' => 3,
            'up_to_date_count' => 1,
            'apk_download_count' => 1,
        ]);
    }

    public function test_application_slug_is_generated_and_made_unique(): void
    {
        $csrfToken = 'test-token';
        $admin = $this->createAdmin();

        foreach (['com.habersoft.first', 'com.habersoft.second'] as $packageName) {
            $this->actingAs($admin)
                ->withSession(['_token' => $csrfToken])
                ->post('/admin/applications', [
                    '_token' => $csrfToken,
                    'name' => 'Haber Soft',
                    'package_name' => $packageName,
                ])
                ->assertRedirect();
        }

        $this->assertDatabaseHas('managed_applications', ['package_name' => 'com.habersoft.first', 'slug' => 'haber-soft']);
        $this->assertDatabaseHas('managed_applications', ['package_name' => 'com.habersoft.second', 'slug' => 'haber-soft-2']);
    }

    public function test_zero_version_update_check_returns_controlled_error_when_application_is_missing(): void
    {
        $this->getJson('/api/v1/applications/com.habersoft.missing/channels/stable/update-check?current_version_code=0')
            ->assertNotFound()
            ->assertJsonPath('error.code', 'APPLICATION_NOT_FOUND');

        $this->getJson('/api/v1/applications/com.habersoft.missing/channels/stable/update-check?current_version_code=-1')
            ->assertUnprocessable()
            ->assertJsonPath('error.code', 'VALIDATION_FAILED');
    }

    public function test_active_agent_can_use_api_and_inactive_agent_is_rejected(): void
    {
        $csrfToken = 'test-token';

        Storage::fake('apks');
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->withSession(['_token' => $csrfToken])
            ->post('/admin/agents', [
                '_token' => $csrfToken,
                'name' => 'CI Agent',
            ])
            ->assertRedirect('/admin/agents')
            ->assertSessionHas('created_agent_id')
            ->assertSessionHas('created_agent_secret');

        $agentId = (string) session('created_agent_id');
        $agentSecret = (string) session('created_agent_secret');
        $secretHash = (string) DB::table('agents')->where('agent_id', $agentId)->value('secret_hash');

        self::assertNotSame($agentSecret, $secretHash);
        self::assertTrue(Hash::check($agentSecret, $secretHash));

        $headers = [
            'X-Agent-Id' => $agentId,
            'X-Agent-Secret' => $agentSecret,
        ];

        $createResponse = $this->withHeaders($headers)->postJson('/api/v1/agent/applications', [
            'name' => 'Agent App',
            'package_name' => 'com.habersoft.agent',
            'description' => 'Agent ile oluşturuldu',
        ]);

        $createResponse
            ->assertCreated()
            ->assertJsonPath('data.slug', 'agent-app');

        $applicationId = (string) $createResponse->json('data.id');

        $uploadResponse = $this->withHeaders($headers)->post('/api/v1/agent/applications/'.$applicationId.'/releases', [
            'version_code' => 21,
            'version_name' => '2.1.0',
            'release_notes' => 'Agent yayını',
            'apk' => $this->makeApkUpload(),
        ]);

        $uploadResponse
            ->assertCreated()
            ->assertJsonPath('data.version_code', 21);

        $releaseId = (string) $uploadResponse->json('data.id');

        $this->withHeaders($headers)->postJson('/api/v1/agent/applications/'.$applicationId.'/releases/'.$releaseId.'/publish', [
            'channel' => 'stable',
            'force_update' => false,
            'minimum_supported_version_code' => 0,
            'comment' => 'Agent stable yayını',
        ])
            ->assertCreated()
            ->assertJsonPath('data.action', 'publish');

        $dbAgentId = (string) DB::table('agents')->where('agent_id', $agentId)->value('id');

        $this->actingAs($admin)
            ->withSession(['_token' => $csrfToken])
            ->post('/admin/agents/'.$dbAgentId.'/rotate-secret', ['_token' => $csrfToken])
            ->assertRedirect('/admin/agents')
            ->assertSessionHas('created_agent_id', $agentId)
            ->assertSessionHas('created_agent_secret');

        $newSecret = (string) session('created_agent_secret');
        $newSecretHash = (string) DB::table('agents')->where('agent_id', $agentId)->value('secret_hash');

        self::assertNotSame($agentSecret, $newSecret);
        self::assertFalse(Hash::check($agentSecret, $newSecretHash));
        self::assertTrue(Hash::check($newSecret, $newSecretHash));

        $this->withHeaders($headers)->getJson('/api/v1/agent/applications')
            ->assertUnauthorized()
            ->assertJsonPath('error.code', 'AGENT_AUTH_FAILED');

        $headers['X-Agent-Secret'] = $newSecret;

        $this->withHeaders($headers)->getJson('/api/v1/agent/applications')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->getJson('/api/v1/applications/com.habersoft.agent/channels/stable/update-check?current_version_code=20')
            ->assertOk()
            ->assertJsonPath('data.status', 'UPDATE_AVAILABLE');

        $this->withHeaders($headers)->getJson('/api/v1/agent/applications/'.$applicationId.'/statistics')
            ->assertOk()
            ->assertJsonPath('data.application_id', $applicationId)
            ->assertJsonPath('data.update_check_count', 1)
            ->assertJsonPath('data.update_available_count', 1)
            ->assertJsonPath('data.up_to_date_count', 0)
            ->assertJsonPath('data.apk_download_count', 0);

        DB::table('agents')->where('agent_id', $agentId)->update(['is_active' => false]);

        $this->withHeaders($headers)->getJson('/api/v1/agent/applications')
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AGENT_INACTIVE');
    }

    private function createAdmin(): AdminUserRecord
    {
        return AdminUserRecord::query()->create([
            'id' => '01J00000000000000000000000',
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('secret-password'),
        ]);
    }

    private function makeApkUpload(): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'apk-apps-test');
        self::assertIsString($path);

        $zip = new ZipArchive;
        self::assertTrue($zip->open($path, ZipArchive::OVERWRITE));
        $zip->addFromString('AndroidManifest.xml', '<manifest package="com.habersoft.example" />');
        $zip->addFromString('classes.dex', 'dex');
        $zip->close();

        return new UploadedFile($path, 'example.apk', 'application/vnd.android.package-archive', null, true);
    }
}
