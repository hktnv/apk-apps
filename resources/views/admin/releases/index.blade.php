<x-layouts.admin title="{{ $application->name }} Release'leri">
    <p><a class="button" href="{{ route('admin.releases.create', ['applicationId' => $application->id]) }}">APK yükle</a></p>
    <section class="card">
        @include('admin.releases.table', ['releases' => $releases, 'application' => $application])
    </section>
</x-layouts.admin>
