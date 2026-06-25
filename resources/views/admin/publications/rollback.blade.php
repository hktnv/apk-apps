<x-layouts.admin title="Rollback">
    <section class="card">
        <p class="error">Android, cihazda daha yüksek version_code yüklüyse otomatik downgrade yapamaz. Rollback yalnızca daha düşük sürümü yeni kuracak veya uygun cihazlara sunar.</p>
        <form method="post" action="{{ route('admin.publications.rollback', ['applicationId' => $application->id]) }}">
            @csrf
            <label>Hedef release</label>
            <select name="release_id" required>
                @foreach ($releases as $release)
                    <option value="{{ $release->id }}">v{{ $release->versionCode }} · {{ $release->versionName }}</option>
                @endforeach
            </select>
            @include('admin.publications.policy-fields')
            <button type="submit">Rollback kaydı oluştur</button>
        </form>
    </section>
</x-layouts.admin>
