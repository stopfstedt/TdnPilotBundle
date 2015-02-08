Prerequisites
-------------
This bundle requires symfony >= 2.3+.

In addition the following requirements must be met:

- The command `doctrine:schema:validate` passes
- Entities are in a loadable/working state
- All [dependencies] are properly installed and configured
- Any [extras] (to provide additional functionality) should be properly configured
- Project falls within the parameters listed in [caveats] page (projects following sf2 standards should)

<sub>**Note**: [extras] will not be installed automatically. You must add each one to the composer file
Please review the [extras] page for details.</sub>

Composer install
----------------
Require the "tdn/pilotbundle" package in composer.json

```bash
$ composer require tdn/pilotbundle dev-master
```

<sub>GitFlow + Semver for 1.0.0+, master will only contain stable tags</sub>

Update kernel
-------------
Add the bundle to your application kernel dev/test bundles and configure:
``` php
public function registerBundles()
{
    $bundles = array(
        //...
        new Tdn\PilotBundle\TdnPilotBundle(),
        //...
    );

    //...
}
```

Configure
---------
Enable the bundle's configuration in `app/config/config.yml`:
``` yaml
# app/config/config.yml
tdn_pilot: ~
```

Configuration
-------------
#### Full configuration
``` yaml
tdn_pilot:
    template:
        strategy: twig_template_strategy # Or create your own service implementing TemplateStrategyInterface
```

[dependencies]: dependencies.md
[extras]: extras.md
[caveats]: caveats.md
