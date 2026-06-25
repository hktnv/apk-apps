<x-layouts.admin title="{{ $application ? 'Uygulama Düzenle' : 'Yeni Uygulama' }}">
    <section class="card">
        <form method="post" action="{{ $application ? route('admin.applications.update', ['applicationId' => $application->id]) : route('admin.applications.store') }}">
            @csrf
            @if ($application) @method('put') @endif

            <label>Ad</label>
            <input name="name" value="{{ old('name', $application?->name) }}" required>

            <label>Slug</label>
            <input name="slug" value="{{ old('slug', $application?->slug) }}" required>

            <label>Package name</label>
            <input name="package_name" value="{{ old('package_name', $application?->packageName) }}" @if($application) readonly @endif required>
            @if($application)<p class="muted">Package name oluşturulduktan sonra değiştirilemez.</p>@endif

            <label>Açıklama</label>
            <textarea name="description">{{ old('description', $application?->description) }}</textarea>

            <button type="submit">Kaydet</button>
        </form>
    </section>
</x-layouts.admin>
