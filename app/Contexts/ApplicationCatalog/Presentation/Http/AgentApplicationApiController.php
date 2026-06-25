<?php

declare(strict_types=1);

namespace App\Contexts\ApplicationCatalog\Presentation\Http;

use App\Contexts\ApplicationCatalog\Application\CreateApplication;
use App\Contexts\ApplicationCatalog\Application\CreateApplicationCommand;
use App\Contexts\ApplicationCatalog\Application\GetApplicationDetails;
use App\Contexts\ApplicationCatalog\Application\ListApplications;
use App\Contexts\ApplicationCatalog\Application\ManagedApplicationView;
use App\Contexts\ApplicationCatalog\Application\SetApplicationActiveStatus;
use App\Contexts\ReleaseDistribution\Application\ReleaseRepository;
use App\Contexts\ReleaseDistribution\Application\ReleaseView;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AgentApplicationApiController
{
    public function index(Request $request, ListApplications $applications): JsonResponse
    {
        return $this->ok($request, $applications->execute()
            ->map(fn (ManagedApplicationView $application): array => $this->applicationData($application))
            ->values()
            ->all());
    }

    public function store(Request $request, CreateApplication $createApplication): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'package_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $result = $createApplication->execute(new CreateApplicationCommand(
            $data['name'],
            $data['package_name'],
            $data['description'] ?? null,
        ));

        if (! $result->ok) {
            return $this->operationError($request, $result->errorCode ?? 'APPLICATION_CREATE_FAILED', $result->message ?? 'Uygulama oluşturulamadı.');
        }

        /** @var ManagedApplicationView $application */
        $application = $result->value;

        return $this->ok($request, $this->applicationData($application), 201);
    }

    public function show(
        string $applicationId,
        Request $request,
        GetApplicationDetails $details,
        ReleaseRepository $releases,
    ): JsonResponse {
        $application = $details->execute($applicationId);
        if ($application === null) {
            return $this->operationError($request, 'APPLICATION_NOT_FOUND', 'Uygulama bulunamadı.', 404);
        }

        return $this->ok($request, [
            ...$this->applicationData($application),
            'releases' => $releases->listForApplication($applicationId)
                ->map(fn (ReleaseView $release): array => [
                    'id' => $release->id,
                    'version_code' => $release->versionCode,
                    'version_name' => $release->versionName,
                    'sha256' => $release->sha256,
                    'size_bytes' => $release->sizeBytes,
                ])
                ->values()
                ->all(),
        ]);
    }

    public function activate(string $applicationId, Request $request, SetApplicationActiveStatus $status): JsonResponse
    {
        $result = $status->execute($applicationId, true);
        if (! $result->ok) {
            return $this->operationError($request, $result->errorCode ?? 'APPLICATION_NOT_FOUND', $result->message ?? 'Uygulama bulunamadı.', 404);
        }

        return $this->ok($request, ['id' => $applicationId, 'is_active' => true]);
    }

    public function deactivate(string $applicationId, Request $request, SetApplicationActiveStatus $status): JsonResponse
    {
        $result = $status->execute($applicationId, false);
        if (! $result->ok) {
            return $this->operationError($request, $result->errorCode ?? 'APPLICATION_NOT_FOUND', $result->message ?? 'Uygulama bulunamadı.', 404);
        }

        return $this->ok($request, ['id' => $applicationId, 'is_active' => false]);
    }

    /** @return array<string, mixed> */
    private function applicationData(ManagedApplicationView $application): array
    {
        return [
            'id' => $application->id,
            'name' => $application->name,
            'slug' => $application->slug,
            'package_name' => $application->packageName,
            'description' => $application->description,
            'is_active' => $application->isActive,
        ];
    }

    private function ok(Request $request, mixed $data, int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'meta' => ['request_id' => (string) $request->attributes->get('request_id')],
        ], $status)->header('Cache-Control', 'no-store');
    }

    private function operationError(Request $request, string $code, string $message, int $status = 422): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
            'meta' => ['request_id' => (string) $request->attributes->get('request_id')],
        ], $status)->header('Cache-Control', 'no-store');
    }
}
