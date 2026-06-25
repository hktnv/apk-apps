<x-layouts.admin title="Release v{{ $release->versionCode }}">
    <section class="card">
        <p><strong>Uygulama:</strong> {{ $application->name }}</p>
        <p><strong>Version:</strong> {{ $release->versionCode }} · {{ $release->versionName }}</p>
        <p><strong>SHA-256:</strong> <code>{{ $release->sha256 }}</code></p>
        <p><strong>Notlar:</strong> {{ $release->releaseNotes }}</p>
        <div class="actions">
            <a class="button" href="{{ route('admin.publications.publish-form', ['applicationId' => $application->id, 'releaseId' => $release->id]) }}">Publish</a>
            <a class="button secondary" href="{{ route('api.v1.artifacts.download', ['releaseId' => $release->id]) }}">Public download URL</a>
        </div>
        <p class="muted">Download URL yalnızca release yayınlandıktan sonra çalışır.</p>
    </section>
</x-layouts.admin>
