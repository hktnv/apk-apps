<?php

declare(strict_types=1);

namespace App\Contexts\IdentityAccess\Infrastructure\Http;

use App\Contexts\IdentityAccess\Application\AgentView;
use App\Contexts\IdentityAccess\Application\AuthenticateAgent;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AgentAuthenticationMiddleware
{
    public function __construct(private readonly AuthenticateAgent $authenticateAgent) {}

    public function handle(Request $request, Closure $next): Response
    {
        $agentId = trim((string) $request->headers->get('X-Agent-Id', ''));
        $secret = (string) $request->headers->get('X-Agent-Secret', '');

        if ($agentId === '' || $secret === '') {
            return $this->error($request, 'AGENT_CREDENTIALS_REQUIRED', 'Agent kimlik bilgileri gerekli.', 401);
        }

        $result = $this->authenticateAgent->execute($agentId, $secret);
        if (! $result->ok) {
            $status = $result->errorCode === 'AGENT_INACTIVE' ? 403 : 401;

            return $this->error($request, $result->errorCode ?? 'AGENT_AUTH_FAILED', $result->message ?? 'Agent doğrulanamadı.', $status);
        }

        /** @var AgentView $agent */
        $agent = $result->value;
        $request->attributes->set('agent', $agent);
        $request->attributes->set('agent_admin_actor_id', $agent->createdByAdminId);

        /** @var Response $response */
        $response = $next($request);

        return $response;
    }

    private function error(Request $request, string $code, string $message, int $status): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
            'meta' => [
                'request_id' => (string) $request->attributes->get('request_id'),
            ],
        ], $status)->header('Cache-Control', 'no-store');
    }
}
