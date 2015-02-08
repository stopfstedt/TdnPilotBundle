[tdn:generate:manager](generate-manager.md)
===========================================
Generates an entity manager (DAO) for a specified entity.

Usage
-----
```bash

$ ./bin/console tdn:generate:manager [-t|--entity[="..."]] [-l|--entities-location[="..."]] [-o|--overwrite] [-d|--target-directory[="..."]]

```

#### Required (options)

You should only input one of these:

- entity: The entity to initialize in shortcut format (e.g. MyVendorFooBundle:MyEntity)
- entities-location: The relative (or absolute) path to your entities directory

#### Options
- overwrite: Overwrites existing files located in directory. **optional**
  <sub>Defaults to false.</sub>
- target-directory: Override the default output directory. **optional**
  <sub>Defaults to `<Bundle>/Entity/Manager/`.</sub>

In addition to this document, you can also pass in the `--help` flag for more information when running the command.

Dependencies
------------
None

Output
------
By default the output directory will be `<Bundle>/Entity/Manager/`.

Files generated:

- `<Entity>Manager.php`
- `<Entity>ManagerInterface.php`
- `managers.xml` (Output in `Resources/config`)

#### Example

Running:
```php
$ ./bin/console tdn:generate:manager FooBarBundle:Foo
```

Creates:
Manager
``` php
<?php

namespace Foo\BarBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @param EntityManager $em
     * @param string $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em         = $em;
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
     */
    public function updateFoo(
        FooInterface $foo,
        $andFlush = true
    ) {
        $this->em->persist($foo);
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

```

Interface
```php
<?php

namespace Foo\BarBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
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
     *
     * @return void
     */
    public function updateFoo(
        FooInterface $foo,
        $andFlush = true
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

```

Service
```xml
<?xml version="1.0"?>
<container xmlns="http://symfony.com/schema/dic/services" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
  <parameters>
    <parameter key="foobar.foo.manager.class">Foo\BarBundle\Entity\Manager\FooManager</parameter>
  </parameters>
  <services>
    <service id="foobar.foo.manager" class="%foobar.foo.manager.class%">
      <argument type="service" id="doctrine.orm.entity_manager"/>
      <argument>Foo\BarBundle\Entity\Foo</argument>
    </service>
  </services>
</container>

```
