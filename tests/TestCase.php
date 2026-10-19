<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Pages render without the built Vite assets
        $this->withoutVite();
    }

    /** Real JSON returned by a NexoraEDU institute (tests/Fixtures/institute). */
    protected function fixture(string $name): array
    {
        return json_decode(file_get_contents(__DIR__ . "/Fixtures/institute/{$name}.json"), true);
    }
}
