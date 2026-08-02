<?php

declare(strict_types=1);

it('creates and retrieves an order via the API', function (): void {
    $client = static::createClient();

    $client->request(
        'POST',
        '/api/orders',
        server: ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
        content: json_encode([
            'lines' => [
                ['productSku' => 'SKU-100', 'quantity' => 2, 'unitPrice' => 15],
            ],
        ], \JSON_THROW_ON_ERROR),
    );

    expect($client->getResponse()->getStatusCode())->toBe(201);

    /** @var array{id: string, status: string, total: float} $created */
    $created = json_decode($client->getResponse()->getContent() ?: '{}', true, 512, \JSON_THROW_ON_ERROR);

    expect($created['status'])->toBe('placed')
        ->and((float) $created['total'])->toBe(30.0);

    $client->request('GET', '/api/orders/'.$created['id'], server: ['HTTP_ACCEPT' => 'application/json']);

    expect($client->getResponse()->getStatusCode())->toBe(200);
});

it('cancels an order and rejects a second cancel', function (): void {
    $client = static::createClient();

    $client->request(
        'POST',
        '/api/orders',
        server: ['CONTENT_TYPE' => 'application/json'],
        content: json_encode([
            'lines' => [
                ['productSku' => 'SKU-200', 'quantity' => 1, 'unitPrice' => 10],
            ],
        ], \JSON_THROW_ON_ERROR),
    );

    $created = json_decode($client->getResponse()->getContent() ?: '{}', true, 512, \JSON_THROW_ON_ERROR);

    $client->request('POST', '/api/orders/'.$created['id'].'/cancel');
    expect($client->getResponse()->getStatusCode())->toBe(200);

    $client->request('POST', '/api/orders/'.$created['id'].'/cancel');
    expect($client->getResponse()->getStatusCode())->toBe(409);
});
