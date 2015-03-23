Upgrading
=========

This document will be updated to list important BC breaks and behavioral changes.

### Upgrading from 0.0.1 (unreleased alpha)

 * Bundle name changed. Replace references to `SfProjectGeneratorBundle` with the new Bundle namespace `TdnPilotBundle`
 Specifically in `AppKernel.php`.
 * Commands have changed.
 Please review docs located in [Resources/doc/](/Resources/doc/) or type in `bin/console` to view changes.
 * New TemplateStrategyInterface and service were added. It can be changed through the configuration by adding the following to your `app/config/config_dev.yml` file.
 <sub>Default strategy: twig_template_strategy</sub>

```yml
tdn_pilot:
  template:
    strategy: twig_template_strategy # Or create your own service implementing TemplateStrategyInterface
  form:
    relationship:
        string_strategy: identifier
```
