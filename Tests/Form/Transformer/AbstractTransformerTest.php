<?php

namespace Tdn\PilotBundle\Tests\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use \Mockery;

/**
 * Class AbstractTransformerTest
 * @package Tdn\PilotBundle\Tests\Form\Transformer
 */
abstract class AbstractTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return DataTransformerInterface
     */
    abstract protected function getTransformer();

    /**
     * @return mixed
     */
    abstract protected function getOriginal();

    /**
     * @return mixed
     */
    abstract protected function getTransformed();

    public function testTransform()
    {
        $this->assertEquals(
            $this->getTransformed(),
            $this->getTransformer()->transform($this->getOriginal())
        );
    }

    public function testReverseTransform()
    {
        $this->assertEquals(
            $this->getOriginal(),
            $this->getTransformer()->reverseTransform($this->getTransformed())
        );
    }

    /**
     * @return \stdClass
     */
    protected function getEntity()
    {
        $entity = new \stdClass();
        $entity->name = 'Foo';
        $entity->id = 1;

        return $entity;
    }

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
