Upgrading
=========

This document will be updated to list important BC breaks and behavioral changes.

### Upgrading from 0.0.1 (unreleased alpha)

 * Bundle name changed. Replace references to `SfProjectGeneratorBundle` with the new Bundle namespace `TdnPilotBundle`
 Specifically in `AppKernel.php`.
 * Commands have changed.
 Please review docs located in [Resources/doc/](/Resources/doc/) or type in `bin/console` to view changes.
 * New OutputEngineInterface and service were added. It can be changed through the configuration by adding the following to your `app/config/config_dev.yml` file.
 The default value for this parameter is twig_output_engine.
```yml
tdn_pilot:
  output:
    engine: twig_output_engine # Or create your own service implementing OutputEngineInterface
```
