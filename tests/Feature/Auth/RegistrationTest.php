<?php

use Illuminate\Support\Facades\Mail;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    Mail::fake();

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '081234567890',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('otp.create', absolute: false));
    $response->assertSessionHas('otp_user_id');
});

test('merchant registration saves the merchant WhatsApp number', function () {
    Mail::fake();

    $response = $this->post('/register', [
        'name' => 'Kantin Baru',
        'email' => 'merchant@example.com',
        'phone' => '081234567891',
        'role' => 'merchant',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('otp.create', absolute: false));
    $this->assertDatabaseHas('merchants', [
        'name' => 'Kantin Baru',
        'whatsapp' => '6281234567891',
    ]);
});
