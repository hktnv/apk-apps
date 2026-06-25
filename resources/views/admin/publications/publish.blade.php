<x-layouts.admin title="Release Yayınla">
    <section class="card">
        <div class="guide">
            <strong>Yayınlama ne yapar?</strong>
            <p class="muted">Seçilen APK sürümünü belirlediğiniz kanalda kullanıcılara görünür hale getirir. Zorunlu güncelleme seçilirse eski sürüm kullanan cihazlar yeni sürüme yönlendirilir.</p>
        </div>
        <p>{{ $application->name }} için v{{ $release->versionCode }} yayınlanacak.</p>
        <form method="post" action="{{ route('admin.publications.publish', ['applicationId' => $application->id, 'releaseId' => $release->id]) }}">
            @csrf
            @include('admin.publications.policy-fields')
            <button type="submit">Yayınla</button>
        </form>
    </section>
</x-layouts.admin>
