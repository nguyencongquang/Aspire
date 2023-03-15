<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test login with correct username password.
     *
     * @return void
     */
    public function test_login_successfully()
    {
        $this->seed();
        $response = $this->post('/api/auth/login', ['email' => 'customer@aspire.com', 'password' => 'customer@#$%']);
        $response->assertStatus(200);
    }

    /**
     * Test login with incorrect username password.
     *
     * @return void
     */
    public function test_login_unsuccessfully()
    {
        $response = $this->post('/api/auth/login', ['email' => 'not_correct_email@aspire.com', 'password' => '@#$%']);
        $response->assertStatus(401);
        $response->assertExactJson(['status' => false, 'message' => 'Email & Password does not match with our record.']);
    }
}
