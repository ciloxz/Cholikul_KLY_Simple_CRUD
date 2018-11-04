<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    /**
     * Routing Test.
     *
     * @return void
     */
    public function test_routing_get_method()
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $response = $this->get('/create');
        $response->assertStatus(200);
    }
}
