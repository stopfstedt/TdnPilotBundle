[tdn:generate:routing](generate-routing.md)
===========================================
Generates or removes a route based on an entity for that entity's controller. [[command](../../Command/GenerateRoutingCommand.php)]

Usage
-----
```php
$ ./bin/console tdn:generate:routing [-o|--overwrite] [-d|--target-directory[="..."]] [-p|--route-prefix[="..."]] [-r|--remove] <entity> [routing-file="routing.yml"]
```

#### Arguments
- entity: The entity to initialize in shortcut format (e.g. MyVendorFooBundle:MyEntity)
- routing-file: The routing file to output. **optional**
  <sub>Defaults to `routing.yml`.</sub>

#### Options
- overwrite: Overwrites existing files located in directory. **optional**
  <sub>Defaults to false.</sub>
- target-directory: Override the default output directory to specified directory. **optional**
  <sub>Defaults to `<Bundle>/Resources/config/`.</sub>
- route-prefix: Specify a route prefix (e.g. v1). **optional**
- remove: Remove route from routing file instead of adding it. **optional**

In addition to this document, you can also pass in the `--help` flag for more information when running the command.

Dependencies
------------
* `<Bundle>/Controller/<Entity>Controller.php`

Output
------
By default the output directory will be `<Bundle>/Resources/config/`.

Files generated:
- argument: routing-file

#### Example

Running:
```php
$ ./bin/console tdn:generate:routing --route-prefix=v1 FooBarBundle:Foo
```

Creates:
``` yaml
api_foo_v1:
    resource: "@FooBarBundle/Controller/FooController.php"
    type:     rest
    prefix:   /v1
    defaults: {_format:json}
```
