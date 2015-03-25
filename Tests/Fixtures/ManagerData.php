<?php

namespace Tdn\PilotBundle\Tests\Fixtures;

/**
 * Static data fixture for manager tests.
 *
 * Class ManagerData
 * @package Tdn\PilotBundle\Tests\Fixtures
 */
class ManagerData
{
    const FOO_MANAGER_SERVICE_XML = <<<'FOO_HANDLER_XML'
<?xml version="1.0" encoding="utf-8"?>
<container xmlns="http://symfony.com/schema/dic/services" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
  <parameters>
    <parameter key="foobar.foo.manager.class">Foo\BarBundle\Entity\Manager\FooManager</parameter>
  </parameters>
  <services>
    <service id="foobar.foo.manager" class="%foobar.foo.manager.class%">
      <argument type="service" id="doctrine"/>
      <argument>Foo\BarBundle\Entity\Foo</argument>
    </service>
  </services>
</container>

FOO_HANDLER_XML;

    const FOO_MANAGER_SERVICE_YAML = <<<'FOO_HANDLER_YAML'
parameters:
    foobar.foo.manager.class: Foo\BarBundle\Entity\Manager\FooManager

services:
    foobar.foo.manager:
        class: %foobar.foo.manager.class%
        arguments: ['@doctrine', Foo\BarBundle\Entity\Foo]

FOO_HANDLER_YAML;

    const FOO_MANAGER = <<<'FOO_MANAGER'
<?php

namespace Foo\BarBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Foo\BarBundle\Entity\FooInterface;

/**
 * Class FooManager
 * @package Foo\BarBundle\Entity\Manager
 */
class FooManager implements FooManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param Registry $em
     * @param string $class
     */
    public function __construct(Registry $em, $class)
    {
        $this->em         = $em->getManagerForClass($class);
        $this->class      = $class;
        $this->repository = $em->getRepository($class);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return FooInterface
     */
    public function findFooBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|FooInterface[]
     */
    public function findFoosBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param FooInterface $foo
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateFoo(
        FooInterface $foo,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($foo);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($foo));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param FooInterface $foo
     */
    public function deleteFoo(
        FooInterface $foo
    ) {
        $this->em->remove($foo);
        $this->em->flush();
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return FooInterface
     */
    public function createFoo()
    {
        $class = $this->getClass();
        return new $class();
    }
}

FOO_MANAGER;

    const FOO_MANAGER_INTERFACE = <<<'FOO_MANAGER_INTERFACE'
<?php

namespace Foo\BarBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Foo\BarBundle\Entity\FooInterface;

/**
 * Interface FooManagerInterface
 * @package Foo\BarBundle\Entity\Manager
 */
interface FooManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return FooInterface
     */
    public function findFooBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|FooInterface[]
     */
    public function findFoosBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param FooInterface $foo
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateFoo(
        FooInterface $foo,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param FooInterface $foo
     *
     * @return void
     */
    public function deleteFoo(
        FooInterface $foo
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return FooInterface
     */
    public function createFoo();
}

FOO_MANAGER_INTERFACE;

}
