<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Contexts\ApplicationCatalog\Domain\PackageName;
use App\Contexts\ReleaseDistribution\Domain\Channel;
use App\Contexts\ReleaseDistribution\Domain\Sha256Hash;
use App\Contexts\ReleaseDistribution\Domain\UpdateDecisionPolicy;
use App\Contexts\ReleaseDistribution\Domain\UpdateStatus;
use App\Contexts\ReleaseDistribution\Domain\VersionCode;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DomainValueObjectsTest extends TestCase
{
    public function test_package_name_validation(): void
    {
        self::assertTrue(PackageName::isValid('com.habersoft.example'));
        self::assertFalse(PackageName::isValid('com.1bad.example'));
        self::assertFalse(PackageName::isValid('single'));
    }

    public function test_version_code_must_be_positive(): void
    {
        self::assertSame(12, (new VersionCode(12))->value);
        $this->expectException(InvalidArgumentException::class);
        new VersionCode(0);
    }

    public function test_sha256_hash_format(): void
    {
        self::assertSame(str_repeat('a', 64), (new Sha256Hash(str_repeat('a', 64)))->value);
        $this->expectException(InvalidArgumentException::class);
        new Sha256Hash('not-a-hash');
    }

    public function test_channel_enum(): void
    {
        self::assertSame('stable', Channel::Stable->value);
        self::assertSame('beta', Channel::Beta->value);
        self::assertSame('internal', Channel::Internal->value);
    }

    public function test_update_decision_table(): void
    {
        $policy = new UpdateDecisionPolicy;

        self::assertSame(UpdateStatus::NoPublishedRelease, $policy->decide(10, null, false, 0)->status);
        self::assertSame(UpdateStatus::UpdateAvailable, $policy->decide(10, 11, false, 0)->status);
        self::assertFalse($policy->decide(10, 11, false, 0)->required);
        self::assertTrue($policy->decide(10, 11, true, 0)->required);
        self::assertTrue($policy->decide(8, 11, false, 10)->required);
        self::assertSame(UpdateStatus::UpToDate, $policy->decide(11, 11, false, 0)->status);
        self::assertSame(UpdateStatus::ClientAhead, $policy->decide(12, 11, false, 0)->status);
    }
}
