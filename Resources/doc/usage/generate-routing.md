[tdn:generate:routing](generate-routing.md)
===========================================
Generates or removes a route based on an entity for that entity's controller.

Usage
-----
```php
$ ./bin/console tdn:generate:routing [-t|--entity[="..."]] [-l|--entities-location[="..."]] [-o|--overwrite] [-d|--target-directory[="..."]] [-p|--route-prefix="..."] [-r|--remove] [routing-file="routing.yml"]
```

#### Required (options)

You should only input one of these:

- entity: The entity to initialize in shortcut format (e.g. MyVendorFooBundle:MyEntity)
- entities-location: The relative (or absolute) path to your entities directory

#### Arguments
- routing-file: The routing file to output. **optional**
  <sub>Defaults to `routing.yml`.</sub>

#### Options
- overwrite: Overwrites existing files located in directory. **optional**
  <sub>Defaults to false.</sub>
- target-directory: Override the default output directory. **optional**
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
