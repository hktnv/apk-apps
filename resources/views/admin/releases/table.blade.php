<table>
    <thead><tr><th>Version</th><th>Dosya</th><th>SHA-256</th><th></th></tr></thead>
    <tbody>
    @forelse ($releases as $release)
        <tr>
            <td>{{ $release->versionCode }}<br><span class="muted">{{ $release->versionName }}</span></td>
            <td>{{ $release->originalFilename }}<br><span class="muted">{{ number_format($release->sizeBytes) }} bytes</span></td>
            <td><code>{{ $release->sha256 }}</code></td>
            <td><a href="{{ route('admin.releases.show', ['applicationId' => $application->id, 'releaseId' => $release->id]) }}">Detay</a></td>
        </tr>
    @empty
        <tr><td colspan="4" class="muted">Henüz release yok.</td></tr>
    @endforelse
    </tbody>
</table>
