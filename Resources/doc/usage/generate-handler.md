[tdn:generate:handler](generate-handler.md)
===========================================
Generates an entity handler based on an entity to use in your rest controller. Processes form data, etc.

Usage
-----
```php

$ ./bin/console tdn:generate:handler [-t|--entity[="..."]] [-l|--entities-location[="..."]] [-o|--overwrite] [-d|--target-directory[="..."]]

```

#### Required (options)

You should only input one of these:

- entity: The entity to initialize in shortcut format (e.g. MyVendorFooBundle:MyEntity)
- entities-location: The relative (or absolute) path to your entities directory

#### Options
- overwrite: Overwrites existing files located in directory. **optional**
  <sub>Defaults to false.</sub>
- target-directory: Override the default output directory. **optional**
  <sub>Defaults to `<Bundle>/Handler/`.</sub>

In addition to this document, you can also pass in the `--help` flag for more information when running the command.

Dependencies
------------
* `<Bundle>/Entity/Manager/<Entity>Manager.php`
* `<Bundle>/Form/Type/<Entity>Type.php`

Output
------
By default the output directory will be `<Bundle>/Handler/`.

Files generated:

- `<Entity>Handler.php`
- `handlers.xml` (output in `Resources/config`)

#### Example

Running:
```bash
$ ./bin/console tdn:generate:handler FooBarBundle:Foo
```

Creates:
Handler
``` php
<?php

namespace Foo\BarBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;
use Foo\BarBundle\Exception\InvalidFormException;
use Foo\BarBundle\Form\Type\FooType;
use Foo\BarBundle\Entity\Manager\FooManager;
use Foo\BarBundle\Entity\FooInterface;

/**
 * Class FooHandler
 * @package Foo\BarBundle\Handler
 */
class FooHandler extends FooManager
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @param EntityManager $em
     * @param string $class
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(EntityManager $em, $class, FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
        parent::__construct($em, $class);
    }

    /**
     * @param array $parameters
     *
     * @return FooInterface
     */
    public function post(array $parameters)
    {
        $foo = $this->createFoo();

        return $this->processForm($foo, $parameters, 'POST');
    }

    /**
     * @param FooInterface $foo
     * @param array $parameters
     *
     * @return FooInterface
     */
    public function put(
        FooInterface $foo,
        array $parameters
    ) {
        return $this->processForm(
            $foo,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param FooInterface $foo
     * @param array $parameters
     *
     * @return FooInterface
     */
    public function patch(
        FooInterface $foo,
        array $parameters
    ) {
        return $this->processForm(
            $foo,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param FooInterface $foo
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return FooInterface
     */
    protected function processForm(
        FooInterface $foo,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            new FooType(),
            $foo,
            array('method' => $method)
        );
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $foo = $form->getContentsInFormat();
            $this->updateFoo($foo, true);

            return $foo;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}

```

Service
```xml
<?xml version="1.0"?>
<container xmlns="http://symfony.com/schema/dic/services" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
  <parameters>
    <parameter key="foobar.foo.handler.class">Foo\BarBundle\Handler\FooHandler</parameter>
  </parameters>
  <services>
    <service id="foobar.foo.handler" class="%foobar.foo.handler.class%">
      <argument type="service" id="doctrine.orm.entity_manager"/>
      <argument>Foo\BarBundle\Entity\Foo</argument>
      <argument type="service" id="form.factory"/>
    </service>
  </services>
</container>

```
