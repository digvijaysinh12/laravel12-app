<?php

use App\Models\User;

test('customer can access customer pages but not admin pages', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk();

    $this->actingAs($user)
        ->get('/profile')
        ->assertOk();

    $this->actingAs($user)
        ->get('/admin/dashboard')
        ->assertForbidden();

    $this->actingAs($user)
        ->get('/admin/products')
        ->assertForbidden();
});

test('admin can access admin pages but not customer pages', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this->actingAs($admin)
        ->get('/admin/dashboard')
        ->assertOk();

    $this->actingAs($admin)
        ->get('/admin/products')
        ->assertOk();

    $this->actingAs($admin)
        ->get('/dashboard')
        ->assertForbidden();

    $this->actingAs($admin)
        ->get('/profile')
        ->assertForbidden();
});
