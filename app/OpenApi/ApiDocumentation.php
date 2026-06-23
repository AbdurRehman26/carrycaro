<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'CarryCaro API',
    description: 'API documentation for CarryCaro authentication, profile, locations, and trips.'
)]
#[OA\Server(url: '/', description: 'Current application')]
#[OA\SecurityScheme(securityScheme: 'sanctum', type: 'http', scheme: 'bearer', bearerFormat: 'Sanctum token')]
#[OA\Schema(
    schema: 'MessageResponse',
    required: ['message'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Operation completed successfully.'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'ValidationErrorResponse',
    required: ['message', 'errors'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
        new OA\Property(
            property: 'errors',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(type: 'array', items: new OA\Items(type: 'string'))
        ),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'User',
    required: ['id', 'name', 'email'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Jane Doe'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'jane@example.com'),
        new OA\Property(property: 'phone_number', type: 'string', nullable: true, example: '+491234567890'),
        new OA\Property(property: 'facebook_profile', type: 'string', nullable: true, example: 'https://facebook.com/jane'),
        new OA\Property(property: 'profile_image', type: 'string', nullable: true, example: 'https://example.com/avatar.jpg'),
        new OA\Property(property: 'timezone', type: 'string', nullable: true, example: 'Europe/Berlin'),
        new OA\Property(property: 'email_verified_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'Country',
    required: ['id', 'name', 'code'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Germany'),
        new OA\Property(property: 'code', type: 'string', nullable: true, example: 'DE'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'City',
    required: ['id', 'name', 'country'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Berlin'),
        new OA\Property(property: 'city_type', type: 'string', nullable: true, example: 'capital'),
        new OA\Property(property: 'country', ref: '#/components/schemas/Country'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'Airline',
    required: ['id', 'name', 'is_active'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Emirates'),
        new OA\Property(property: 'iata_code', type: 'string', nullable: true, example: 'EK'),
        new OA\Property(property: 'icao_code', type: 'string', nullable: true, example: 'UAE'),
        new OA\Property(property: 'country', type: 'string', nullable: true, example: 'United Arab Emirates'),
        new OA\Property(property: 'is_active', type: 'boolean', example: true),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'Trip',
    required: ['id', 'from_city', 'to_city', 'departure_date', 'arrival_date', 'weight_available', 'weight_price'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 10),
        new OA\Property(property: 'from_city', ref: '#/components/schemas/City'),
        new OA\Property(property: 'to_city', ref: '#/components/schemas/City'),
        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
        new OA\Property(property: 'departure_date', type: 'string', format: 'date', example: '2026-06-15'),
        new OA\Property(property: 'arrival_date', type: 'string', format: 'date', example: '2026-06-16'),
        new OA\Property(property: 'airline_id', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'airline', type: 'string', nullable: true, example: 'Lufthansa'),
        new OA\Property(property: 'airline_details', ref: '#/components/schemas/Airline', nullable: true),
        new OA\Property(property: 'notes', type: 'string', nullable: true, example: 'Can carry fragile items with proper packaging.'),
        new OA\Property(property: 'weight_available', type: 'number', example: 5),
        new OA\Property(property: 'weight_price', type: 'string', example: 'EUR 12/kg'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'TripWriteRequest',
    required: ['from_city_id', 'to_city_id', 'departure_date', 'arrival_date', 'weight_available', 'weight_price'],
    properties: [
        new OA\Property(property: 'from_city_id', type: 'integer', example: 1),
        new OA\Property(property: 'to_city_id', type: 'integer', example: 2),
        new OA\Property(property: 'departure_date', type: 'string', format: 'date', example: '2026-06-15'),
        new OA\Property(property: 'arrival_date', type: 'string', format: 'date', example: '2026-06-16'),
        new OA\Property(property: 'weight_available', type: 'number', minimum: 0.1, example: 5),
        new OA\Property(property: 'weight_price', type: 'string', maxLength: 255, example: 'EUR 12/kg'),
        new OA\Property(property: 'airline_id', type: 'integer', nullable: true, description: 'Existing airline id. When provided, the trip airline name is copied from this airline.', example: 1),
        new OA\Property(property: 'airline', type: 'string', nullable: true, maxLength: 255, example: 'Lufthansa'),
        new OA\Property(property: 'notes', type: 'string', nullable: true, maxLength: 1000, example: 'Can carry fragile items with proper packaging.'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'GoogleSignInRequest',
    required: ['provider', 'id_token'],
    properties: [
        new OA\Property(property: 'provider', type: 'string', enum: ['google'], example: 'google'),
        new OA\Property(
            property: 'id_token',
            type: 'string',
            description: 'Google ID token returned by the Android or iOS Google Sign-In SDK.',
            example: 'eyJhbGciOiJSUzI1NiIsImtpZCI6Ij...'
        ),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'ChatMessage',
    required: ['id', 'conversation_id', 'sender', 'body', 'created_at'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'conversation_id', type: 'integer', example: 1),
        new OA\Property(property: 'sender', ref: '#/components/schemas/User'),
        new OA\Property(property: 'body', type: 'string', example: 'Can you carry a small package?'),
        new OA\Property(property: 'read_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'ChatConversation',
    required: ['id', 'other_user', 'unread_count'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'other_user', ref: '#/components/schemas/User'),
        new OA\Property(property: 'trip', ref: '#/components/schemas/Trip', nullable: true),
        new OA\Property(property: 'last_message', ref: '#/components/schemas/ChatMessage', nullable: true),
        new OA\Property(property: 'unread_count', type: 'integer', example: 2),
        new OA\Property(property: 'last_message_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'StartConversationRequest',
    required: ['user_id'],
    properties: [
        new OA\Property(property: 'user_id', type: 'integer', description: 'The other participant user id.', example: 2),
        new OA\Property(property: 'trip_id', type: 'integer', nullable: true, description: 'Optional trip context for this conversation.', example: 10),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'StoreChatMessageRequest',
    required: ['body'],
    properties: [
        new OA\Property(property: 'body', type: 'string', maxLength: 5000, example: 'Can you carry a small package?'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'PaginatedTrips',
    required: ['data', 'links', 'meta'],
    properties: [
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Trip')),
        new OA\Property(property: 'links', type: 'object'),
        new OA\Property(property: 'meta', type: 'object'),
    ],
    type: 'object'
)]
final class ApiDocumentation
{
    #[OA\Post(
        path: '/api/auth/register',
        summary: 'Register a new user',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['name', 'email', 'password', 'password_confirmation'],
            properties: [
                new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Jane Doe'),
                new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'jane@example.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8, example: 'password123'),
                new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'password123'),
                new OA\Property(property: 'phone_number', type: 'string', nullable: true, maxLength: 20, example: '+491234567890'),
                new OA\Property(property: 'facebook_profile', type: 'string', nullable: true, maxLength: 255),
            ],
            type: 'object'
        )),
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 201, description: 'Registration successful', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'message', type: 'string'),
                new OA\Property(property: 'user', ref: '#/components/schemas/User'),
                new OA\Property(property: 'token', type: 'string'),
            ], type: 'object')),
            new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function register(): void {}

    #[OA\Post(
        path: '/api/auth/login',
        summary: 'Log in with email and password',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'jane@example.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123'),
            ],
            type: 'object'
        )),
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 200, description: 'Login successful', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'user', ref: '#/components/schemas/User'),
                new OA\Property(property: 'token', type: 'string'),
            ], type: 'object')),
            new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function login(): void {}

    #[OA\Post(
        path: '/api/auth/social-login',
        summary: 'Log in or register with a Google ID token',
        description: 'Mobile-only Google sign-in. The API verifies the supplied Google ID token with Google before creating or updating the user.',
        operationId: 'googleSignIn',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/GoogleSignInRequest')),
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 200, description: 'Social login successful', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'user', ref: '#/components/schemas/User'),
                new OA\Property(property: 'token', type: 'string'),
            ], type: 'object')),
            new OA\Response(response: 422, description: 'Validation failed, invalid Google token, unverified email, or disallowed Google OAuth audience', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function socialLogin(): void {}

    #[OA\Post(
        path: '/api/auth/logout',
        summary: 'Revoke the current access token',
        security: [['sanctum' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 200, description: 'Logout successful', content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function logout(): void {}

    #[OA\Get(
        path: '/api/cities',
        summary: 'Search cities',
        tags: ['Locations'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'q', description: 'Alias for search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'country_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'City list', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'cities', type: 'array', items: new OA\Items(ref: '#/components/schemas/City')),
            ], type: 'object')),
        ]
    )]
    public function cities(): void {}

    #[OA\Get(
        path: '/api/countries',
        summary: 'List countries',
        tags: ['Locations'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'q', description: 'Alias for search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Country list', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'countries', type: 'array', items: new OA\Items(ref: '#/components/schemas/Country')),
            ], type: 'object')),
        ]
    )]
    public function countries(): void {}

    #[OA\Get(
        path: '/api/airlines',
        summary: 'List active airlines',
        tags: ['Locations'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'q', description: 'Alias for search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Airline list', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'airlines', type: 'array', items: new OA\Items(ref: '#/components/schemas/Airline')),
            ], type: 'object')),
        ]
    )]
    public function airlines(): void {}

    #[OA\Get(
        path: '/api/profile',
        summary: "Get the authenticated user's profile",
        security: [['sanctum' => []]],
        tags: ['Profile'],
        responses: [
            new OA\Response(response: 200, description: 'Authenticated profile', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'user', ref: '#/components/schemas/User'),
            ], type: 'object')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function profileShow(): void {}

    #[OA\Put(
        path: '/api/profile',
        summary: "Update the authenticated user's profile",
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
            new OA\Property(property: 'name', type: 'string', maxLength: 255),
            new OA\Property(property: 'phone_number', type: 'string', nullable: true, maxLength: 20),
            new OA\Property(property: 'facebook_profile', type: 'string', nullable: true, maxLength: 255),
            new OA\Property(property: 'timezone', type: 'string', nullable: true, maxLength: 50),
            new OA\Property(property: 'password', type: 'string', format: 'password', nullable: true, minLength: 8),
            new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', nullable: true),
        ], type: 'object')),
        tags: ['Profile'],
        responses: [
            new OA\Response(response: 200, description: 'Profile updated', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'message', type: 'string'),
                new OA\Property(property: 'user', ref: '#/components/schemas/User'),
            ], type: 'object')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function profileUpdate(): void {}

    #[OA\Delete(
        path: '/api/profile',
        summary: "Delete the authenticated user's account",
        security: [['sanctum' => []]],
        tags: ['Profile'],
        responses: [
            new OA\Response(response: 200, description: 'Account deleted', content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function profileDelete(): void {}

    #[OA\Get(
        path: '/api/trips',
        summary: 'Browse public trips',
        tags: ['Trips'],
        parameters: [
            new OA\Parameter(name: 'from_city_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'to_city_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'departure_from', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'departure_to', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'include_past', in: 'query', required: false, schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 15, minimum: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated trip list', content: new OA\JsonContent(ref: '#/components/schemas/PaginatedTrips')),
        ]
    )]
    public function tripsIndex(): void {}

    #[OA\Get(
        path: '/api/my-trips',
        summary: "List authenticated user's trips",
        security: [['sanctum' => []]],
        tags: ['Trips'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 15, minimum: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated trip list', content: new OA\JsonContent(ref: '#/components/schemas/PaginatedTrips')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function myTrips(): void {}

    #[OA\Post(
        path: '/api/trips',
        summary: 'Create a trip',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/TripWriteRequest')),
        tags: ['Trips'],
        responses: [
            new OA\Response(response: 201, description: 'Trip created', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'message', type: 'string'),
                new OA\Property(property: 'trip', ref: '#/components/schemas/Trip'),
            ], type: 'object')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function tripsStore(): void {}

    #[OA\Get(
        path: '/api/trips/{trip}',
        summary: 'Show a trip',
        tags: ['Trips'],
        parameters: [
            new OA\Parameter(name: 'trip', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Trip details', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'trip', ref: '#/components/schemas/Trip'),
            ], type: 'object')),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function tripsShow(): void {}

    #[OA\Put(
        path: '/api/trips/{trip}',
        summary: "Update one of the authenticated user's trips",
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/TripWriteRequest')),
        tags: ['Trips'],
        parameters: [
            new OA\Parameter(name: 'trip', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Trip updated', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'message', type: 'string'),
                new OA\Property(property: 'trip', ref: '#/components/schemas/Trip'),
            ], type: 'object')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function tripsUpdate(): void {}

    #[OA\Delete(
        path: '/api/trips/{trip}',
        summary: "Delete one of the authenticated user's trips",
        security: [['sanctum' => []]],
        tags: ['Trips'],
        parameters: [
            new OA\Parameter(name: 'trip', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Trip deleted', content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function tripsDelete(): void {}

    #[OA\Get(
        path: '/api/conversations',
        summary: 'List authenticated user conversations',
        security: [['sanctum' => []]],
        tags: ['Chat'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 15, minimum: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated conversation list', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/ChatConversation')),
                new OA\Property(property: 'links', type: 'object'),
                new OA\Property(property: 'meta', type: 'object'),
            ], type: 'object')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function conversationsIndex(): void {}

    #[OA\Post(
        path: '/api/conversations',
        summary: 'Start or reuse a conversation',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/StartConversationRequest')),
        tags: ['Chat'],
        responses: [
            new OA\Response(response: 200, description: 'Existing conversation', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'conversation', ref: '#/components/schemas/ChatConversation'),
            ], type: 'object')),
            new OA\Response(response: 201, description: 'Conversation created', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'conversation', ref: '#/components/schemas/ChatConversation'),
            ], type: 'object')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function conversationsStore(): void {}

    #[OA\Get(
        path: '/api/conversations/{conversation}',
        summary: 'Show a conversation',
        security: [['sanctum' => []]],
        tags: ['Chat'],
        parameters: [
            new OA\Parameter(name: 'conversation', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Conversation details', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'conversation', ref: '#/components/schemas/ChatConversation'),
            ], type: 'object')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function conversationsShow(): void {}

    #[OA\Get(
        path: '/api/conversations/{conversation}/messages',
        summary: 'List conversation messages',
        security: [['sanctum' => []]],
        tags: ['Chat'],
        parameters: [
            new OA\Parameter(name: 'conversation', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 25, minimum: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated message list', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/ChatMessage')),
                new OA\Property(property: 'links', type: 'object'),
                new OA\Property(property: 'meta', type: 'object'),
            ], type: 'object')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function conversationMessages(): void {}

    #[OA\Post(
        path: '/api/conversations/{conversation}/messages',
        summary: 'Send a chat message',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/StoreChatMessageRequest')),
        tags: ['Chat'],
        parameters: [
            new OA\Parameter(name: 'conversation', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 201, description: 'Message sent', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'message', type: 'string'),
                new OA\Property(property: 'chat_message', ref: '#/components/schemas/ChatMessage'),
            ], type: 'object')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 422, description: 'Validation failed', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ]
    )]
    public function conversationMessagesStore(): void {}

    #[OA\Patch(
        path: '/api/conversations/{conversation}/read',
        summary: 'Mark conversation messages as read',
        security: [['sanctum' => []]],
        tags: ['Chat'],
        parameters: [
            new OA\Parameter(name: 'conversation', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Conversation marked as read', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'message', type: 'string'),
                new OA\Property(property: 'read_messages_count', type: 'integer', example: 3),
            ], type: 'object')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function conversationsRead(): void {}
}
