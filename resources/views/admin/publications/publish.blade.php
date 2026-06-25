<x-layouts.admin title="Release Yayınla">
    <section class="card">
        <p>{{ $application->name }} için v{{ $release->versionCode }} yayınlanacak.</p>
        <form method="post" action="{{ route('admin.publications.publish', ['applicationId' => $application->id, 'releaseId' => $release->id]) }}">
            @csrf
            @include('admin.publications.policy-fields')
            <button type="submit">Yayınla</button>
        </form>
    </section>
</x-layouts.admin>
