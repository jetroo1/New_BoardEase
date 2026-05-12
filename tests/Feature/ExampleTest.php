<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_landing_page_loads_for_guests(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Find Your Perfect Boarding House in Tagum');
    }
}
