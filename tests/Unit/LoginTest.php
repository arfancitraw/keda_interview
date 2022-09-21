<?php

namespace Tests\Unit;

use Tests\TestCase;

class LoginTest extends TestCase
{

    public function testValidationError() {
        $response = $this->postJson('/api/auth/login', [
            "email" => "thisisnotemail",
            "password" => "password"
        ]);

        $response->assertStatus(422)
                ->assertJson([
                     
                ]);
    }

    public function testUserNotFound() {
        $response = $this->postJson('/api/auth/login', [
            "email" => "test@gmail.com",
            "password" => "dummydummy"
        ]);

        $response->assertStatus(422)
            ->assertJson([
            ]);
    }

    public function testLoggedInSuccessfully() {
        $response = $this->postJson('/api/auth/login', [
            "email" => "arfan@gmail.com",
            "password" => "dummydummy"
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                "access_token",
                "token_type",
                "expires_in",
                "user" => [
                    "id",
                    "user_type_id",
                    "created_at",
                    "updated_at",
                    "deleted_at"
                ]
            ]);
    }
}