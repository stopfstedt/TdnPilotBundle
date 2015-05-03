<?php

namespace Tdn\PilotBundle\Tests\Fixtures;

/**
 * Static data fixture for handler tests.
 *
 * Class HandlerData
 * @package Tdn\PilotBundle\Tests\Fixtures
 */
class HandlerData
{
    const FOO_HANDLER_SERVICE_XML = <<<'FOO_HANDLER_XML'
<?xml version="1.0" encoding="utf-8"?>
<container xmlns="http://symfony.com/schema/dic/services" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
  <parameters>
    <parameter key="foobar.foo.handler.class">Foo\BarBundle\Handler\FooHandler</parameter>
  </parameters>
  <services>
    <service id="foobar.foo.handler" class="%foobar.foo.handler.class%">
      <argument type="service" id="doctrine"/>
      <argument>Foo\BarBundle\Entity\Foo</argument>
      <argument type="service" id="form.factory"/>
    </service>
  </services>
</container>

FOO_HANDLER_XML;

    const FOO_HANDLER_SERVICE_YAML = <<<'FOO_HANDLER_YAML'
parameters:
    foobar.foo.handler.class: Foo\BarBundle\Handler\FooHandler

services:
    foobar.foo.handler:
        class: %foobar.foo.handler.class%
        arguments: ['@doctrine', Foo\BarBundle\Entity\Foo, '@form.factory']

FOO_HANDLER_YAML;

    const FOO_HANDLER = <<<'FOO_HANDLER'
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
            $foo = $form->getData();
            $this->updateFoo($foo, true);

            return $foo;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}

FOO_HANDLER;

    const ANNOTATED_FOO_HANDLER = <<<'ANOTATED_FOO_HANDLER'
<?php

namespace Foo\BarBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Foo\BarBundle\Exception\InvalidFormException;
use Foo\BarBundle\Form\Type\FooType;
use Foo\BarBundle\Entity\Manager\FooManager;
use Foo\BarBundle\Entity\FooInterface;

/**
 * @Di\Service("foobar.foo.handler")
 *
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
     * @Di\InjectParams({
     *     "em" = @Di\Inject("foobar.foo.manager"),
     *     "class" = "Foo\BarBundle\Entity\Foo",
     *     "formFactory" = @Di\Inject("form.factory")
     * })
     *
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
            $foo = $form->getData();
            $this->updateFoo($foo, true);

            return $foo;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}

ANOTATED_FOO_HANDLER;


}
