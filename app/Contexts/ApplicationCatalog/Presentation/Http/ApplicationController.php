<?php

declare(strict_types=1);

namespace App\Contexts\ApplicationCatalog\Presentation\Http;

use App\Contexts\ApplicationCatalog\Application\CreateApplication;
use App\Contexts\ApplicationCatalog\Application\CreateApplicationCommand;
use App\Contexts\ApplicationCatalog\Application\GetApplicationDetails;
use App\Contexts\ApplicationCatalog\Application\ListApplications;
use App\Contexts\ApplicationCatalog\Application\SetApplicationActiveStatus;
use App\Contexts\ApplicationCatalog\Application\UpdateApplicationDetails;
use App\Contexts\ApplicationCatalog\Application\UpdateApplicationDetailsCommand;
use App\Contexts\ReleaseDistribution\Application\PublicationRepository;
use App\Contexts\ReleaseDistribution\Application\ReleaseRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ApplicationController
{
    public function index(ListApplications $listApplications): View
    {
        return view('admin.applications.index', [
            'applications' => $listApplications->execute(),
        ]);
    }

    public function create(): View
    {
        return view('admin.applications.form', [
            'application' => null,
        ]);
    }

    public function store(Request $request, CreateApplication $createApplication): RedirectResponse
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
            return back()->withInput()->withErrors(['form' => $result->message]);
        }

        return redirect()->route('admin.applications.show', ['applicationId' => $result->value->id])
            ->with('status', 'Uygulama oluşturuldu.');
    }

    public function show(
        string $applicationId,
        GetApplicationDetails $details,
        ReleaseRepository $releases,
        PublicationRepository $publications,
    ): View {
        $application = $details->execute($applicationId);
        abort_if($application === null, 404);

        return view('admin.applications.show', [
            'application' => $application,
            'releases' => $releases->listForApplication($applicationId),
            'publications' => $publications->historyForApplication($applicationId),
        ]);
    }

    public function edit(string $applicationId, GetApplicationDetails $details): View
    {
        $application = $details->execute($applicationId);
        abort_if($application === null, 404);

        return view('admin.applications.form', [
            'application' => $application,
        ]);
    }

    public function update(
        string $applicationId,
        Request $request,
        UpdateApplicationDetails $updateApplicationDetails,
    ): RedirectResponse {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $result = $updateApplicationDetails->execute(new UpdateApplicationDetailsCommand(
            $applicationId,
            $data['name'],
            $data['description'] ?? null,
        ));

        if (! $result->ok) {
            return back()->withInput()->withErrors(['form' => $result->message]);
        }

        return redirect()->route('admin.applications.show', ['applicationId' => $applicationId])
            ->with('status', 'Uygulama güncellendi.');
    }

    public function activate(string $applicationId, SetApplicationActiveStatus $status): RedirectResponse
    {
        $status->execute($applicationId, true);

        return back()->with('status', 'Uygulama aktifleştirildi.');
    }

    public function deactivate(string $applicationId, SetApplicationActiveStatus $status): RedirectResponse
    {
        $status->execute($applicationId, false);

        return back()->with('status', 'Uygulama pasifleştirildi.');
    }
}
