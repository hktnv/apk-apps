<x-layouts.admin title="APK Yükle">
    <section class="card">
        <form method="post" enctype="multipart/form-data" action="{{ route('admin.releases.store', ['applicationId' => $application->id]) }}">
            @csrf
            <label>Version code</label>
            <input type="number" min="1" name="version_code" value="{{ old('version_code') }}" required>

            <label>Version name</label>
            <input name="version_name" value="{{ old('version_name') }}" required>

            <label>Release notes</label>
            <textarea name="release_notes" required>{{ old('release_notes') }}</textarea>

            <label>İmzalı APK</label>
            <input type="file" name="apk" accept=".apk" required>

            <button type="submit">Yükle</button>
        </form>
    </section>
</x-layouts.admin>
