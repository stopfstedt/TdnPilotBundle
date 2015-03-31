<?php

namespace Tdn\PilotBundle\Tests\Fixtures;

/**
 * Static data fixture for services.yml/services.xml tests.
 *
 * Class ServiceUtilsData
 * @package Tdn\PilotBundle\Tests\Fixtures
 */
class ServiceUtilsData
{
    const YAML = <<<'YAML'
parameters:
    foo_bar.manager.foo_manager.class: Foo\BarBundle\Entity\Manager\FooManager
services:
    foo_bar.manager.foo_manager:
        class: '%foo_bar.manager.foo_manager.class%'
        arguments: ['@doctrine', Foo\BarBundle\Entity\Foo]
imports:
    - { resource: "@FooBarBundle/Resources/config/services.yml" }

YAML;

    const XML = <<<'XML'
<?xml version="1.0" encoding="utf-8"?>
<container xmlns="http://symfony.com/schema/dic/services" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
  <parameters>
    <parameter key="foo_bar.manager.foo_manager.class">Foo\BarBundle\Entity\Manager\FooManager</parameter>
  </parameters>
  <services>
    <service id="foo_bar.manager.foo_manager" class="%foo_bar.manager.foo_manager.class%">
      <argument type="service" id="doctrine"/>
      <argument>Foo\BarBundle\Entity\Foo</argument>
    </service>
  </services>
  <imports>
    <import resource="@FooBarBundle/Resources/config/services.xml"/>
  </imports>
</container>

XML;
}
