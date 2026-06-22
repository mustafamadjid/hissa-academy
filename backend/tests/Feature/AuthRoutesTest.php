<?php

it('routes user login requests to validation instead of returning not found', function () {
    $response = $this->postJson('/api/v1/auth/login');

    $response->assertStatus(422);
});
