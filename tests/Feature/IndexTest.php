<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function testIndex()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
