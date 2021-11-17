<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ModelCountTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertDatabaseCount('survivors', 5);
        $this->assertDatabaseCount('items', 4);
        $this->assertDatabaseCount('survivor_items', 10);
    }
}
