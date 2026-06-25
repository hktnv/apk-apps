<x-layouts.admin title="Dashboard">
    <section class="grid">
        <div class="card"><h2>Aktif uygulama</h2><strong>{{ $activeApplicationCount }}</strong></div>
        <div class="card"><h2>Toplam release</h2><strong>{{ $releaseCount }}</strong></div>
        <div class="card"><h2>Güncel yayın</h2><strong>{{ $currentPublications->count() }}</strong></div>
    </section>

    <section class="card">
        <h2>Kanal bazında güncel yayınlar</h2>
        @forelse ($currentPublications as $publication)
            <p><span class="badge">{{ $publication->channel }}</span> v{{ $publication->releaseVersionCode }} · {{ $publication->action }}</p>
        @empty
            <p class="muted">Henüz yayın yok.</p>
        @endforelse
    </section>

    <section class="card">
        <h2>Son yüklenen release'ler</h2>
        @forelse ($latestReleases as $release)
            <p>v{{ $release->versionCode }} · {{ $release->versionName }} · {{ $release->originalFilename }}</p>
        @empty
            <p class="muted">Henüz release yok.</p>
        @endforelse
    </section>
</x-layouts.admin>
