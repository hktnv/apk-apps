<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Presentation\Http;

use App\Contexts\ApplicationCatalog\Application\GetApplicationDetails;
use App\Contexts\ReleaseDistribution\Application\PublishRelease;
use App\Contexts\ReleaseDistribution\Application\PublishReleaseCommand;
use App\Contexts\ReleaseDistribution\Application\ReleaseRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class PublicationController
{
    public function publishForm(string $applicationId, string $releaseId, GetApplicationDetails $details, ReleaseRepository $releases): View
    {
        $application = $details->execute($applicationId);
        $release = $releases->find($releaseId);
        abort_if($application === null || $release === null || $release->applicationId !== $applicationId, 404);

        return view('admin.publications.publish', compact('application', 'release'));
    }

    public function publish(string $applicationId, string $releaseId, Request $request, PublishRelease $publishRelease): RedirectResponse
    {
        return $this->write($applicationId, $releaseId, $request, $publishRelease, false);
    }

    public function rollbackForm(string $applicationId, GetApplicationDetails $details, ReleaseRepository $releases): View
    {
        $application = $details->execute($applicationId);
        abort_if($application === null, 404);

        return view('admin.publications.rollback', [
            'application' => $application,
            'releases' => $releases->listForApplication($applicationId),
        ]);
    }

    public function rollback(string $applicationId, Request $request, PublishRelease $publishRelease): RedirectResponse
    {
        $data = $request->validate(['release_id' => ['required', 'string']]);

        return $this->write($applicationId, $data['release_id'], $request, $publishRelease, true);
    }

    private function write(
        string $applicationId,
        string $releaseId,
        Request $request,
        PublishRelease $publishRelease,
        bool $rollback,
    ): RedirectResponse {
        $data = $request->validate([
            'channel' => ['required', 'in:stable,beta,internal'],
            'force_update' => ['nullable', 'boolean'],
            'minimum_supported_version_code' => ['required', 'integer', 'min:0'],
            'comment' => ['required', 'string', 'min:3'],
        ]);

        $command = new PublishReleaseCommand(
            $applicationId,
            $releaseId,
            $data['channel'],
            $request->boolean('force_update'),
            (int) $data['minimum_supported_version_code'],
            $data['comment'],
            (string) Auth::id(),
        );

        $result = $rollback ? $publishRelease->rollback($command) : $publishRelease->execute($command);

        if (! $result->ok) {
            return back()->withInput()->withErrors(['form' => $result->message]);
        }

        return redirect()->route('admin.applications.show', ['applicationId' => $applicationId])
            ->with('status', $rollback ? 'Rollback kaydı oluşturuldu.' : 'Release yayınlandı.');
    }
}
