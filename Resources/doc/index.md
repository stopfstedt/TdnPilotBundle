TdnPilotBundle
==============
A pilot project generator (scaffolding) for [Symfony 2][symfony 2].

Overview
--------
TdnPilotBundle is a <b>very opinionated</b> bundle that provides commands
that can help scaffold a restful application (or selected parts) based on your doctrine entities.
These dependencies are listed on the [dependencies] page. Only entities are required as a base. 
Due to the nature of the bundle it is very opinionated about directory structure and naming
conventions all of which are in the [caveats] page (if followings PSR & best practices you should
have no problems!).

Documentation notes
-------------------
This documentation uses the symfony 3 directory structure. Specific cases are listed below.

Please note this does not affect your project in any way, it is just how the documentation is written.

- Directory `/bin` for binaries (e.g. `./bin/console`). The current 2.x location is `/app/<console>`.

License
-------
This bundle is released under the MIT license. See the complete license in the
[repository] under the following directory:

    Resources/meta/LICENSE

Contributing
------------

Please review the [contributing section].


Contents
--------
* [installation](installation/)
* [usage](usage/)
* [contributing](contributing/)

[Dependencies]: installation/dependencies.md
[caveats]: installation/caveats.md
[contributing section]: contributing/index.md
[symfony 2]: http://symfony.com
[repository]: https://github.com/TheDevNetwork/TdnPilotBundle
[the api docs]: https://github.com/TheDevNetwork/TdnPilotBundle/
