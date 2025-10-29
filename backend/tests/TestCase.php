<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $compiledPath = storage_path('framework/views');

        if (! is_dir($compiledPath)) {
            mkdir($compiledPath, 0755, true);
        }
    }
}
