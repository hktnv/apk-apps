<?php

declare(strict_types=1);

namespace App\Contexts\IdentityAccess\Presentation\Http;

use App\Contexts\IdentityAccess\Application\CreateAgent;
use App\Contexts\IdentityAccess\Application\CreateAgentCommand;
use App\Contexts\IdentityAccess\Application\CreatedAgentView;
use App\Contexts\IdentityAccess\Application\ListAgents;
use App\Contexts\IdentityAccess\Application\SetAgentActiveStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class AgentController
{
    public function index(ListAgents $agents): View
    {
        return view('admin.agents.index', [
            'agents' => $agents->execute(),
        ]);
    }

    public function create(): View
    {
        return view('admin.agents.create');
    }

    public function store(Request $request, CreateAgent $createAgent): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $result = $createAgent->execute(new CreateAgentCommand(
            $data['name'],
            (string) Auth::id(),
        ));

        if (! $result->ok) {
            return back()->withInput()->withErrors(['form' => $result->message]);
        }

        /** @var CreatedAgentView $created */
        $created = $result->value;

        return redirect()->route('admin.agents.index')
            ->with('status', 'Agent oluşturuldu. Secret yalnızca bu ekranda gösterilir.')
            ->with('created_agent_id', $created->agent->agentId)
            ->with('created_agent_secret', $created->secret);
    }

    public function activate(string $agentId, SetAgentActiveStatus $status): RedirectResponse
    {
        $result = $status->execute($agentId, true);

        return $result->ok
            ? back()->with('status', 'Agent aktifleştirildi.')
            : back()->withErrors(['form' => $result->message]);
    }

    public function deactivate(string $agentId, SetAgentActiveStatus $status): RedirectResponse
    {
        $result = $status->execute($agentId, false);

        return $result->ok
            ? back()->with('status', 'Agent pasifleştirildi.')
            : back()->withErrors(['form' => $result->message]);
    }
}
