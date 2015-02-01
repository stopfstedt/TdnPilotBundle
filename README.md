# SF Project Generator Bundle (alpha)
Inspired by [voryx/RESTGeneratorBundle](https://github.com/voryx/restgeneratorbundle).

## Description
This bundle aims to provide developers a way to generate all "boilerplate" code required by projects (not only stubs but actual implementation).

##### Generated code features:
* CRUD application that follows PSR-4
* Generated code follows SF2 Best practices
* 90% + code-coverage from the get-go. (Without you having to write a single line of test code)

##### Generators:
- [x] Generate Controllers (with Api documentation from [nelmio/NelmioApiDocBundle](https://github.com/nelmio/NelmioApiDocBundle), using [FriendsOfSymfony/FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle))
  - [ ] Optional event driven architecture with "--create-events" option when genrating controller
- [x] Form Types (with relationships)
- [x]  Generate Entity Interfaces
- [x]  Generate Entity Managers with interfaces
- [x]  Generate Rest Handlers
- [x]  Generate Routing files (credit to [testabit/restgeneratorbundle](https://github.com/testabit/restgeneratorbundle))
- [ ] Generate Behat functional api tests
- [ ] Generate PHPUnit tests for entities
- [ ] Generate Sonata scaffolding (credit to [testabit/restgeneratorbundle](https://github.com/testabit/restgeneratorbundle))
- [ ] Generate basic HTML (using bootstrap) templates for HTML in accept header

## Comparison to other generators:
* No dependency on ID property of entities.
* In track with latest symfony version (2.6 at the moment).
* Output is FULLY PSR compliant (up to PSR4)
* Actively developed

## Installation

Require the "tdn/sfprojectgeneratorbundle" package in composer.json

```bash
$ composer require tdn/sfprojectgeneratorbundle dev-master
```

Add the bundle to your application kernel and configure.

```php
public function registerBundles()
{
    $bundles = array(
        //...
          new Tdn\SfProjectGeneratorBundle\SfProjectGeneratorBundle(),
          new FOS\RestBundle\FOSRestBundle(),
          new JMS\SerializerBundle\JMSSerializerBundle($this),
          new Nelmio\CorsBundle\NelmioCorsBundle(),
          new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
        //...
    );
    //...
}
```

## Configuration

This bundle depends on a number of other symfony bundles, so they need to be configured in order for the generator to work properly.

<b>This bundle has (at the moment) no configuration of it's own.</b>

See Nelmio/NelmioApiDocBundle and FriendsOfSymfony/FOSRestBundle documentation for configuration details.

## Usage
Please refer to the generator's documentation:
[Project Generator.md (all generators in one)]()

[Controller Generator.md]()

[Entity Generator.md]()

[Form Generator.md]()

[Manager Generator.md]()

[Handler Generator.md]()

[Routing Generator.md]()
