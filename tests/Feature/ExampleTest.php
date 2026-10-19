<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_load(): void
    {
        $this->get('/')->assertOk()->assertSee(route('login'), false);
        $this->get('/login')->assertOk()->assertSee('Student ID or email');
        $this->get('/privacy-policy')->assertOk();
    }
}
