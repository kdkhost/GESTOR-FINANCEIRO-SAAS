<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        if (File::exists(storage_path('installed'))) {
            File::delete(storage_path('installed'));
        }

        $response = $this->get('/');

        $response->assertRedirect('/instalar');
    }
}
