Introduction
------------
This bundle can be extended by a number of symfony bundles to provide additional functionality or features.
These bundles **are not be pulled into the project automatically** and instead you can choose which ones to bring
in according to your needs (that prevents your dependencies from growing out of control).

Each bundle will have to be enabled in the appropriate section of your `app/AppKernel.php` file.

At a glance
-----------

* For Annotations
  * [schmittjoh/JMSDiExtraBundle](#jmsdiextrabundle)
* For documentation (swagger-ish)
  * [nelmio/NelmioApiDocBundle](#nelmioapidocbundle)
* For Sonata Scaffolding
  * [sonata-project/SonataDoctrineORMAdminBundle](#sonatadoctrineormadminbundle)
  * [sonata-project/SonataAdminBundle](#sonataadmin)
* For Test generation
  * [liip/LiipFunctionalTestBundle](#liipfunctionaltestbundle)
  * [fzaninotto/faker](#faker)


JMSDiExtraBundle
----------------
*Must be added to your composer.json `require` section to enable **annotation format***

Add to composer:
```bash
$ composer require nelmio/api-doc-bundle ~2.8
```

Configuration instructions in their [repo docs][jms-di-extra-docs].

JMSDiExtraBundle allows for DI using annotations. It is used whenever the format is set to `annotation`
on some of the commands such as:

- Controller

**Example Configuration:**

```yml
```


NelmioApiDocBundle 
------------------
*Must be added to your composer.json `require` section to enable **swagger documentation***

Add to composer:
```bash
$ composer require nelmio/api-doc-bundle ~2.8
```

Configuration instructions in their [repo docs][nelmio-api-docs].

NelmioApiDocBundle generates documentation for your API through the usage of the `@Api` annotation.

It is used any time the `--with-swagger` flag is used in the controller generator.

**Example Configuration:**

```yml
# app/config/config.yml
nelmio_api_doc:
    name: Excalibur API
    sandbox:
        body_format:
            formats:
                - form
                - json
            default_format: json
        request_format:
            formats:
                json: application/json
                xml: application/xml
            method: format_param
            default_format: json
```


SonataDoctrineORMAdminBundle
----------------------------
*Must be added to your composer.json `require-dev` or `require` section to enable **sonata scaffolding***

Add to composer:
```bash
$ composer require sonata-project/doctrine-orm-admin-bundle ~2.7
```

Configuration instructions in their [repo docs][sonata-doctrine-docs].

**Example Configuration:**

```yml
```


SonataAdminBundle
-----------------
* Will be pulled automatically by SonataDoctrineORMAdminBundle*

Configuration instructions in their [repo docs][sonata-admin-docs].

**Example Configuration:**

```yml
```


LiipFunctionalTestBundle 
------------------------
*Must be added to your composer.json `require-dev` section to enable **test generation***

Add to composer:
```bash
$ composer require --dev nelmio/api-doc-bundle ~2.7
```

This bundle is used in the generation of tests for your entities.

Configuration instructions in their [repo docs][liip-functional-test-docs].

**Example Configuration**

```yml
# app/config/config_test.yml
liip_functional_test: ~
```


Faker
-----
*Must be added to your composer.json `require-dev` section to enable **fixture generation***

Add to composer:
```bash
$ composer require --dev fzaninotto/faker ~1.4
```

[jms-di-extra-docs]: 
[nelmio-api-docs]: https://github.com/nelmio/NelmioApiDocBundle/blob/master/Resources/doc/index.md
[nelmio-cors-docs]: https://github.com/nelmio/NelmioCorsBundle
[sonata-doctrine-docs]:
[sonata-admin-docs]:
[liip-functional-test-docs]: https://github.com/liip/LiipFunctionalTestBundle
