<?php

declare(strict_types=1);

it('returns health status', function (): void {
    $client = static::createClient();
    $client->request('GET', '/health');

    expect($client->getResponse()->getStatusCode())->toBeIn([200, 503]);

    $payload = json_decode($client->getResponse()->getContent() ?: '{}', true, 512, \JSON_THROW_ON_ERROR);
    expect($payload)->toHaveKeys(['status', 'checks']);
});
