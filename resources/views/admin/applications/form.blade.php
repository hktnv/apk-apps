<x-layouts.admin title="{{ $application ? 'Uygulama Düzenle' : 'Yeni Uygulama' }}">
    <section class="card">
        <div class="guide">
            <strong>Uygulama adı yeterli.</strong>
            <p class="muted">Slug sistem tarafından uygulama adına göre otomatik oluşturulur. Aynı ad tekrar kullanılırsa sistem güvenli şekilde benzersiz bir adres üretir.</p>
        </div>

        <form method="post" action="{{ $application ? route('admin.applications.update', ['applicationId' => $application->id]) : route('admin.applications.store') }}">
            @csrf
            @if ($application) @method('put') @endif

            <label>Ad</label>
            <input name="name" value="{{ old('name', $application?->name) }}" required>

            @if($application)
                <p class="muted">Adres: <span class="badge">{{ $application->slug }}</span></p>
            @endif

            <label>Package name</label>
            <input name="package_name" value="{{ old('package_name', $application?->packageName) }}" @if($application) readonly @endif required>
            @if($application)<p class="muted">Package name oluşturulduktan sonra değiştirilemez.</p>@endif

            <label>Açıklama</label>
            <textarea name="description">{{ old('description', $application?->description) }}</textarea>

            <button type="submit">Kaydet</button>
        </form>
    </section>
</x-layouts.admin>
