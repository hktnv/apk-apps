<x-layouts.admin title="Agent Erişimi">
    @if (session('created_agent_id') && session('created_agent_secret'))
        <section class="card">
            <div class="guide">
                <strong>Secret yalnızca şimdi gösterilir.</strong>
                <p class="muted">Bu bilgiyi güvenli bir yere alın. Sayfadan ayrıldıktan sonra secret tekrar görüntülenemez.</p>
            </div>
            <p><strong>Agent ID:</strong> <code>{{ session('created_agent_id') }}</code></p>
            <p><strong>Agent Secret:</strong> <code>{{ session('created_agent_secret') }}</code></p>
        </section>
    @endif

    <p><a class="button" href="{{ route('admin.agents.create') }}">Yeni agent</a></p>

    <section class="card">
        <div class="guide">
            <strong>Agent nedir?</strong>
            <p class="muted">Agent, dış sistemlerin API üzerinden uygulama, APK ve yayın işlemlerini güvenli şekilde yapabilmesi için kullanılır.</p>
        </div>

        <table>
            <thead><tr><th>Ad</th><th>Agent ID</th><th>Durum</th><th></th></tr></thead>
            <tbody>
            @forelse ($agents as $agent)
                <tr>
                    <td>{{ $agent->name }}<br><span class="muted">{{ $agent->createdAt }}</span></td>
                    <td><code>{{ $agent->agentId }}</code></td>
                    <td>{{ $agent->isActive ? 'Aktif' : 'Pasif' }}</td>
                    <td>
                        <form method="post" action="{{ route($agent->isActive ? 'admin.agents.deactivate' : 'admin.agents.activate', ['agentId' => $agent->id]) }}">
                            @csrf
                            <button class="secondary" type="submit">{{ $agent->isActive ? 'Pasifleştir' : 'Aktifleştir' }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="muted">Henüz agent oluşturulmadı.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
</x-layouts.admin>
