<x-layouts.admin title="Yeni Agent">
    <section class="card">
        <div class="guide">
            <strong>Agent erişimi</strong>
            <p class="muted">Agent oluşturulduğunda sistem bir agent_id ve agent_secret üretir. Secret veritabanında açık tutulmaz ve sadece oluşturma anında gösterilir.</p>
        </div>

        <form method="post" action="{{ route('admin.agents.store') }}">
            @csrf

            <label>Agent adı</label>
            <input name="name" value="{{ old('name') }}" required>

            <button type="submit">Agent oluştur</button>
        </form>
    </section>
</x-layouts.admin>
