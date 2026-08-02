<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class TestCase extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get('doctrine')->getManager();
        $metadata = $em->getMetadataFactory()->getAllMetadata();

        if ([] !== $metadata) {
            $tool = new SchemaTool($em);
            $tool->dropSchema($metadata);
            $tool->createSchema($metadata);
        }

        self::ensureKernelShutdown();
    }
}
