<?php

namespace Tdn\PilotBundle\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use \Mockery;

/**
 * Class AbstractRelatedTypeTest
 * @package Tdn\PilotBundle\Tests\Form\Type
 */
abstract class AbstractRelatedTypeTest extends TypeTestCase
{
    /**
     * @return \stdClass
     */
    abstract protected function getEntity();

    /**
     * @return EntityRepository
     */
    protected function getRepositoryMock()
    {
        $repository = Mockery::mock('\Doctrine\ORM\EntityRepository');
        $repository
            ->shouldDeferMissing()
            ->shouldReceive([
                'find' => $this->getEntity()
            ])
            ->zeroOrMoreTimes()
        ;

        return $repository;
    }

    /**
     * @return ObjectManager
     */
    protected function getEntityManager()
    {
        $entityManager = Mockery::mock('\Doctrine\Common\Persistence\ObjectManager');
        $entityManager
            ->shouldDeferMissing()
            ->shouldReceive([
                'getRepository' => $this->getRepositoryMock(),
                'getClassMetadata' => $this->getMetadata()
            ])
            ->zeroOrMoreTimes()
        ;

        return $entityManager;
    }

    /**
     * @return ClassMetadata
     */
    protected function getMetadata()
    {
        $metadata = Mockery::mock('\Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $metadata
            ->shouldReceive([
                'getIdentifierFieldNames' => ['id'],
                'getIdentifierValues'     => ['id' => 1]
            ])
            ->withAnyArgs()
            ->zeroOrMoreTimes()
        ;

        return $metadata;
    }
}
