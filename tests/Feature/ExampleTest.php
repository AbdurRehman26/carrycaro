<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

it('serves swagger documentation', function () {
    $this->get('/docs')
        ->assertStatus(200)
        ->assertSee('SwaggerUIBundle', false);

    $this->get('/docs/openapi.yaml')
        ->assertStatus(200)
        ->assertSee('openapi: 3.0.0', false)
        ->assertSee('/auth/register', false);
});
