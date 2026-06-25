<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Presentation\Http;

use App\Contexts\ApplicationCatalog\Application\GetApplicationDetails;
use App\Contexts\ReleaseDistribution\Application\ReleaseRepository;
use App\Contexts\ReleaseDistribution\Application\UploadRelease;
use App\Contexts\ReleaseDistribution\Application\UploadReleaseCommand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class AdminReleaseController
{
    public function index(string $applicationId, GetApplicationDetails $details, ReleaseRepository $releases): View
    {
        $application = $details->execute($applicationId);
        abort_if($application === null, 404);

        return view('admin.releases.index', [
            'application' => $application,
            'releases' => $releases->listForApplication($applicationId),
        ]);
    }

    public function create(string $applicationId, GetApplicationDetails $details): View
    {
        $application = $details->execute($applicationId);
        abort_if($application === null, 404);

        return view('admin.releases.form', [
            'application' => $application,
        ]);
    }

    public function store(string $applicationId, Request $request, UploadRelease $uploadRelease): RedirectResponse
    {
        $data = $request->validate([
            'version_code' => ['required', 'integer', 'min:1'],
            'version_name' => ['required', 'string', 'max:255'],
            'release_notes' => ['required', 'string'],
            'apk' => ['required', 'file', 'max:'.((int) config('apk.max_upload_mb') * 1024)],
        ]);

        $file = $request->file('apk');
        abort_if($file === null, 422);

        $result = $uploadRelease->execute(new UploadReleaseCommand(
            $applicationId,
            (int) $data['version_code'],
            $data['version_name'],
            $data['release_notes'],
            (string) $file->getClientOriginalName(),
            (string) $file->getRealPath(),
            (string) Auth::id(),
        ));

        if (! $result->ok) {
            return back()->withInput()->withErrors(['form' => $result->message]);
        }

        return redirect()->route('admin.releases.show', [
            'applicationId' => $applicationId,
            'releaseId' => $result->value->id,
        ])->with('status', 'APK yüklendi. Yayınlamak için ayrı publish işlemini kullanın.');
    }

    public function show(
        string $applicationId,
        string $releaseId,
        GetApplicationDetails $details,
        ReleaseRepository $releases,
    ): View {
        $application = $details->execute($applicationId);
        $release = $releases->find($releaseId);
        abort_if($application === null || $release === null || $release->applicationId !== $applicationId, 404);

        return view('admin.releases.show', [
            'application' => $application,
            'release' => $release,
        ]);
    }
}
