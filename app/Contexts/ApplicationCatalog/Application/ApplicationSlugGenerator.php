<?php

declare(strict_types=1);

namespace App\Contexts\ApplicationCatalog\Application;

use Illuminate\Support\Str;

final class ApplicationSlugGenerator
{
    public function __construct(private readonly ManagedApplicationRepository $applications) {}

    public function uniqueForName(string $name): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'uygulama';
        }

        $base = Str::limit($base, 80, '');
        $slug = $base;
        $suffix = 2;

        while ($this->applications->existsBySlug($slug)) {
            $candidateSuffix = '-'.$suffix;
            $slug = Str::limit($base, 80 - strlen($candidateSuffix), '').$candidateSuffix;
            $suffix++;
        }

        return $slug;
    }
}
