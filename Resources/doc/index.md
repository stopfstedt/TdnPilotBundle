TdnPilotBundle
==============
A pilot project generator for symfony2.

Assumptions
-----------
The documentation assumes the following locations:

- `/bin` for binaries (e.g. `./bin/console`). standard location: `/app`. Only affects documentation.
- Entities are in a loadable state and following the [entity guidelines][entity-guidelines]
- `doctrine:schema:validate` passes.

#### Dependencies:
This bundle depends on a number of other symfony bundles, so they need to be configured
 in order for the generator to work properly.

See the following bundles to properly configure them to use in conjunction with this bundle:
* [FriendsOfSymfony/FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle)
* [nelmio/NelmioApiDocBundle](https://github.com/nelmio/NelmioApiDocBundle)
* [schmittjoh/JMSSerializerBundle](https://github.com/schmittjoh/JMSSerializerBundle)
* [nelmio/NelmioCorsBundle](https://github.com/nelmio/NelmioCorsBundle)

A working example of the aforementioned bundles configured and working with this bundle can
 be found in this [config example].

Installation
------------
Require the "tdn/pilotbundle" package in composer.json

```bash
$ composer require tdn/pilotbundle dev-master
```

Add the bundle to your application kernel and configure.

``` php
public function registerBundles()
{
    $bundles = array(
        //...
          new FOS\RestBundle\FOSRestBundle(),
          new JMS\SerializerBundle\JMSSerializerBundle($this),
          new Nelmio\CorsBundle\NelmioCorsBundle(),
          new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
        //...
    );

    //...

    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        //...
        $bundles[] = new Tdn\PilotBundle\TdnPilotBundle();
        //...
    }
}
```

Enable the bundle's configuration in `app/config/config.yml`:
``` yaml
# app/config/config.yml
tdn_pilot: ~
```

At this point, please do the following if you have not done so (only if applicable):
- `bin/console doctrine:create:database`
- `bin/console doctrine:migrations:migrate` *
- `bin/console doctrine:fixtures:load` *

<sub>* Optional commands provided by MigrationsBundle and FixturesBundle.</sub>

Configuration
-------------
#### Full configuration
``` yaml
tdn_pilot:
    output:
        engine: twig_output_engine # Or create your own service implementing OutputEngineInterface
```

Usage
-----
After the bundle is configured all commands will be availale through the console:

```bash
$ ./bin/console
```

### Pilot (all in one)

#### [tdn:generate:project](generate-project.md)

Pilots a project based on entities. [[documentation](generate-project.md)] [[command](../../Command/GenerateProjectCommand.php)]

Please note that this is the **recommended** command to use when using this bundle.

If you need more granular control, please continue reading.

### Specific commands
If executing single commands, please mind the order.

The generated files rely on each other to some extent (E.g. Controllers rely on handlers which rely on forms which use the managers).

The following list displays the commands in the order they should be executed.

You may use the appropriate command to generate the required files, or you may create them manually. 
However, if creating the files manually it is recommended that you **extend the generated code instead**, and use the DIC to handle dependencies and calls.

Each command has validation to check for any files it expects.

#### [tdn:generate:manager](generate-manager.md)

Generates an entity manager (DAO) for a specified entity. [[documentation](generate-manager.md)] [[command](../../Command/GenerateManagerCommand.php)]

#### [tdn:generate:form](generate-form.md)

Generates a form type based on the specified entity. [[documentation](generate-form.md)] [[command](../../Command/GenerateFormCommand.php)]

#### [tdn:generate:handler](generate-handler.md)

Generates a rest handler used in controllers based on the specified entity. [[documentation](generate-handler.md)] [[command](../../Command/GenerateHandlerCommand.php)]

#### [tdn:generate:controller](generate-controller.md)

Generates a restful controller based on the specified entity. [[documentation](generate-controller.md)] [[command](../../Command/GenerateControllerCommand.php)]

#### [tdn:generate:routing](generate-routing.md)

Generates routes (or removes them) for controllers based on a the specified entity. [[documentation](generate-routing.md)] [[command](../../Command/GenerateRoutingCommand.php)]

Development
-----------

If you are contributing or otherwise developing in this bundle, please read the [contributor guidelines](../../CONTRIBUTING.md).

[config example]: https://github.com/ilios/ilios/blob/master/app/config/config.yml
[entity-guidelines]: entity-rules.md
