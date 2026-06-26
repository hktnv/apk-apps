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
        <div class="guide">
            <strong>İstatistikler neyi gösterir?</strong>
            <p class="muted">Bu özet yalnızca güncelleme sorgularını ve APK indirmelerini sayar. IP, cihaz kimliği veya kullanıcı takibi tutulmaz.</p>
        </div>
        <div class="grid stats-grid">
            <div class="stat-card">
                <span class="stat-label">Update sorguları</span>
                <strong>{{ number_format($statistics->updateCheckCount, 0, ',', '.') }}</strong>
                <small>Toplam public update-check isteği</small>
            </div>
            <div class="stat-card">
                <span class="stat-label">Güncelleme var</span>
                <strong>{{ number_format($statistics->updateAvailableCount, 0, ',', '.') }}</strong>
                <small>UPDATE_AVAILABLE dönen sorgular</small>
            </div>
            <div class="stat-card">
                <span class="stat-label">Güncel</span>
                <strong>{{ number_format($statistics->upToDateCount, 0, ',', '.') }}</strong>
                <small>UP_TO_DATE dönen sorgular</small>
            </div>
            <div class="stat-card">
                <span class="stat-label">APK indirme</span>
                <strong>{{ number_format($statistics->apkDownloadCount, 0, ',', '.') }}</strong>
                <small>Başarılı artifact indirme isteği</small>
            </div>
            <div class="stat-card">
                <span class="stat-label">Son sorgulama</span>
                <strong>{{ $statistics->lastCheckedAt ?? 'Henüz yok' }}</strong>
                <small>Son public update-check zamanı</small>
            </div>
            <div class="stat-card">
                <span class="stat-label">Son indirme</span>
                <strong>{{ $statistics->lastDownloadedAt ?? 'Henüz yok' }}</strong>
                <small>Son başarılı APK indirme zamanı</small>
            </div>
        </div>
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
