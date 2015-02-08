Running the checks
==================
The bundle comes with a unit test suite and a coding standard.

To perform these checks please review their respective sections below or enable the provided git hook.

### Git Hook

The git hook provides some additional checks that are relevant:

- phplint

And an automated way of fixing code style errors:

- php-cs-fixer

### PHPUnit

All the configuration for PHPUnit tests are located in the `.phpunit.xml.dist` file.

To run (from the project root):
```bash
$ bin/phpunit -c .
```

### PHPCS

All of the configurations for PHP Code Sniffer are located in the `.phpcs.xml` file.

To run (from the project root):
```bash
$ bin/phpcs --standard=.phpcs.xml
```
