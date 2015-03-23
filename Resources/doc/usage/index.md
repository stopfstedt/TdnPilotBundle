Introduction
------------
If all the previous sections were accomplished the commands should be visible in the console by running

```bash
$ ./bin/console
```

Please note that running the pilot command is the **recommended** way to use this bundle.

If executing single commands, please mind the order.

The generated files rely on each other to some extent (E.g. Controllers rely on handlers which rely on forms which use the managers).

The following list (sans pilot) displays the commands in the order they should be executed.

You may use the appropriate command to generate the required files, or you may create them manually. 
However, if creating the files manually it is recommended that you **extend the generated code instead** 
and use the DIC to handle dependencies and calls.

Commands
--------
#### [tdn:generate:project](generate-project.md)

Pilots a project based on entities.

<sub>**Each command has validation to check for any files it expects/has dependencies on**</sub>

#### [tdn:generate:manager](generate-manager.md)

Generates an entity manager (DAO) for a specified entity.

#### [tdn:generate:form](generate-form.md)

Generates a form type based on the specified entity.

#### [tdn:generate:handler](generate-handler.md)

Generates a rest handler used in controllers based on the specified entity.

#### [tdn:generate:controller](generate-controller.md)

Generates a restful controller based on the specified entity.

#### [tdn:generate:routing](generate-routing.md)

Generates routes (or removes them) for controllers based on a the specified entity.


Things to do after
------------------

Check out the [post generation doc](post-generation.md) for some goodness in optimization.
