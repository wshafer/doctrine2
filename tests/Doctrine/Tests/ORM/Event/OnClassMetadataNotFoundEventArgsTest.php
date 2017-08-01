<?php

declare(strict_types=1);

namespace Doctrine\Tests\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnClassMetadataNotFoundEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataBuildingContext;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\Tests\DoctrineTestCase;

/**
 * Tests for {@see \Doctrine\ORM\Event\OnClassMetadataNotFoundEventArgs}
 *
 * @covers \Doctrine\ORM\Event\OnClassMetadataNotFoundEventArgs
 */
class OnClassMetadataNotFoundEventArgsTest extends DoctrineTestCase
{
    public function testEventArgsMutability()
    {
        $entityManager = $this->createMock(EntityManager::class);
        $metadataBuildingContext = new ClassMetadataBuildingContext(
            $this->createMock(ClassMetadataFactory::class)
        );

        $args = new OnClassMetadataNotFoundEventArgs('foo', $metadataBuildingContext, $entityManager);

        self::assertSame('foo', $args->getClassName());
        self::assertSame($metadataBuildingContext, $args->getClassMetadataBuildingContext());
        self::assertSame($entityManager, $args->getObjectManager());

        self::assertNull($args->getFoundMetadata());

        /* @var $metadata \Doctrine\ORM\Mapping\ClassMetadata */
        $metadata = $this->createMock(ClassMetadata::class);

        $args->setFoundMetadata($metadata);

        self::assertSame($metadata, $args->getFoundMetadata());

        $args->setFoundMetadata(null);

        self::assertNull($args->getFoundMetadata());
    }
}
