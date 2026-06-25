<x-layouts.admin title="Uygulamalar">
    <p><a class="button" href="{{ route('admin.applications.create') }}">Yeni uygulama</a></p>
    <section class="card">
        <table>
            <thead><tr><th>Ad</th><th>Package</th><th>Durum</th><th></th></tr></thead>
            <tbody>
            @forelse ($applications as $application)
                <tr>
                    <td>{{ $application->name }}<br><span class="muted">{{ $application->slug }}</span></td>
                    <td>{{ $application->packageName }}</td>
                    <td>{{ $application->isActive ? 'Aktif' : 'Pasif' }}</td>
                    <td><a href="{{ route('admin.applications.show', ['applicationId' => $application->id]) }}">Detay</a></td>
                </tr>
            @empty
                <tr><td colspan="4" class="muted">Henüz uygulama yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
</x-layouts.admin>
