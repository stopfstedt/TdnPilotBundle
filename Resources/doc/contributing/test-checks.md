Summary
-------
The bundle comes with a unit test suite and a coding standard.

To perform these checks please review their respective sections below or enable the provided git hook.

Git Hook
--------
If you choose to enable the pre-commit hook, make sure to enable the following checks:

- phpcs
- phpunit
- phplint

And optionally, an automated way of fixing code style errors:

- php-cs-fixer

PHPUnit
-------
All the configuration for PHPUnit tests are located in the `.phpunit.xml.dist` file.

To manually run (from the project root):
```bash
$ bin/phpunit -c .
```

PHPCS
-----
All of the configurations for PHP Code Sniffer are located in the `.phpcs.xml` file.

To manually run (from the project root):
```bash
$ bin/phpcs --standard=.phpcs.xml
```
