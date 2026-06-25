<x-layouts.admin title="{{ $application->name }}">
    <section class="card">
        <p><strong>Package:</strong> {{ $application->packageName }}</p>
        <p><strong>Slug:</strong> {{ $application->slug }}</p>
        <p><strong>Durum:</strong> {{ $application->isActive ? 'Aktif' : 'Pasif' }}</p>
        <div class="guide">
            <strong>Bu sayfada ne yapılır?</strong>
            <p class="muted">Yeni APK yükleyebilir, yüklenen sürümü bir kanalda yayınlayabilir veya sorun olduğunda rollback akışını başlatabilirsiniz.</p>
        </div>
        <p><strong>Update endpoint:</strong><br><code>{{ url('/api/v1/applications/'.$application->packageName.'/channels/stable/update-check?current_version_code=1') }}</code></p>
        <div class="actions">
            <a class="button secondary" href="{{ route('admin.applications.edit', ['applicationId' => $application->id]) }}">Düzenle</a>
            <a class="button" href="{{ route('admin.releases.create', ['applicationId' => $application->id]) }}">APK yükle</a>
            <a class="button secondary" href="{{ route('admin.publications.rollback-form', ['applicationId' => $application->id]) }}">Rollback</a>
            <form method="post" action="{{ route($application->isActive ? 'admin.applications.deactivate' : 'admin.applications.activate', ['applicationId' => $application->id]) }}">
                @csrf
                <button class="secondary" type="submit">{{ $application->isActive ? 'Pasifleştir' : 'Aktifleştir' }}</button>
            </form>
        </div>
    </section>

    <section class="card">
        <h2>Release'ler</h2>
        @include('admin.releases.table', ['releases' => $releases, 'application' => $application])
    </section>

    <section class="card">
        <h2>Publication history</h2>
        @forelse ($publications as $publication)
            <p><span class="badge">{{ $publication->channel }}</span> {{ $publication->action }} · v{{ $publication->releaseVersionCode }} · {{ $publication->comment }}</p>
        @empty
            <p class="muted">Yayın geçmişi yok.</p>
        @endforelse
    </section>
</x-layouts.admin>
