<?php

declare(strict_types=1);

namespace App\Contexts\ReleaseDistribution\Presentation\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class HealthController
{
    public function live(): JsonResponse
    {
        return response()->json(['status' => 'ok']);
    }

    public function ready(): JsonResponse
    {
        $components = [
            'database' => false,
            'apk_storage' => false,
        ];

        try {
            DB::select('select 1');
            $components['database'] = true;
        } catch (Throwable) {
            $components['database'] = false;
        }

        try {
            Storage::disk((string) config('apk.storage_disk'))->exists('.healthcheck');
            $components['apk_storage'] = true;
        } catch (Throwable) {
            $components['apk_storage'] = false;
        }

        $ready = ! in_array(false, $components, true);

        return response()->json([
            'status' => $ready ? 'ready' : 'not_ready',
            'components' => $components,
        ], $ready ? 200 : 503);
    }
}
