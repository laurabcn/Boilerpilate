<?php

declare(strict_types=1);

arch('domain does not depend on infrastructure or framework')
    ->expect('App\Domain')
    ->not->toUse([
        'App\Infrastructure',
        'App\Application',
        'Symfony',
        'Doctrine',
    ]);

arch('shared domain does not depend on framework')
    ->expect('App\Shared\Domain')
    ->not->toUse([
        'App\Infrastructure',
        'Symfony',
        'Doctrine',
    ]);

arch('shared application does not depend on framework')
    ->expect('App\Shared\Application')
    ->not->toUse([
        'App\Infrastructure',
        'Symfony',
        'Doctrine',
    ]);

arch('application does not depend on infrastructure or http foundation')
    ->expect('App\Application')
    ->not->toUse([
        'App\Infrastructure',
        'Symfony\Component\HttpFoundation',
        'Doctrine\ORM',
    ]);

arch('controllers live in infrastructure http')
    ->expect('App\Infrastructure\Http\Controller')
    ->toBeClasses();
