[![Dependency Status][version eye shield]][version eye]
[![GitHub issues][github issues]][issues page]
[![Total Downloads][downloads shield]][packagist page]
[![License][license shield]][packagist page]
[![Latest Stable Version][latest version shield]][packagist page]
[![Scrutinizer Code Quality][scrutinizer score shield]][scrutinizer page]
[![Scrutinizer Code Coverage][scrutinizer coverage shield]][scrutinizer page]
[![Travis][travis build shield]][travis page]
[![Codacy][codacy shield]][codacy page]
[![SensioLabsInsight][sensio shield]][sensio page]

TdnPilotBundle
==============
A pilot project generator (scaffolding) for [Symfony 2][symfony 2].

**Under development**

Description
-----------
This is a <b>very opinionated</b> bundle that aims to scaffold a working application from your entities.

Entities must follow some basic guidelines to take full advantage of the application.

These are outlined in the [entity rules] doc.

##### Generated code features:
* Create working application from entities
* CRUD application that follows PSR-4
* Generated code follows SF2 Best practices

##### What it generates
You can generate any or all of these. There are holistic and individual commands available.

* REST Controllers 
  * With API Documentation
* Form Types
  * With relationships
* Rest Handlers
  * As service
* Entity Managers
  * With interface
  * As service
* Routing

See the [road map](#road-map) for upcoming features.

Documentation
-------------

For documentation, see:

    Resources/doc/

[Read the documentation](/Resources/doc/index.md)

Road Map
--------
![Next version][next version shield]
- [ ] Implement Project generator: Scaffolds a project based on your entities (proxies all commands)
- [ ] Optional event driven architecture with "--create-events" option when generating controller
  - [ ] `<Bundle>Events::<Action>_INITIALIZE`
  - [ ] `<Bundle>Events::<Action>_SUCCESS`
  - [ ] `<Bundle>Events::<Action>_FAILURE`
  - [ ] `<Bundle>Events::<Action>_COMPLETED`
- [ ] Support multiple formats (Yaml, annotations, xml) for generated service files
- [ ] Generate Sonata admin
- [ ] Generate simple PHPUnit tests for entities (Must follow [entity rules])

![Planned][planned shield]
- [ ] Generate Entity Interfaces
- [ ] Generate Behat functional api tests
- [ ] Generate initial serializer annotations
- [ ] Generate fixture Files
- [ ] Generate basic twig HTML (To be able to respond to xml, json and HTML)
- [ ] Generate basic front end app
  - [ ] Angular
  - [ ] Typescript
  - [ ] Bootstrap 3
  - [ ] Grunt
  - [ ] Bower

Applications using TdnPilotBundle
---------------------------------
[Project Ilios][ilios project]

<sub>A leading Curriculum Management System specializing
 in the health professions educational community. [browse source][ilios core bundle]</sub>

Contributing
------------
Please read [CONTRIBUTING](CONTRIBUTING.md).

License
-------

This bundle is released under the MIT license. See the complete license in the
bundle:

    Resources/meta/LICENSE

[version eye shield]: https://www.versioneye.com/user/projects/54f6e619dd0a3627be000052/badge.svg?style=flat-square
[version eye]: https://www.versioneye.com/user/projects/54f6e619dd0a3627be000052
[github issues]: https://img.shields.io/github/issues/thedevnetwork/tdnpilotbundle.svg?style=flat-square
[issues page]: https://github.com/thedevnetwork/TdnPilotBundle/issues
[downloads shield]: https://img.shields.io/packagist/dt/tdn/pilotbundle.svg?style=flat-square
[packagist page]: https://packagist.org/packages/tdn/pilotbundle
[license shield]: https://img.shields.io/packagist/l/tdn/pilotbundle.svg?style=flat-square
[latest version shield]: https://img.shields.io/packagist/v/tdn/pilotbundle.svg?style=flat-square
[scrutinizer score shield]: https://img.shields.io/scrutinizer/g/TheDevNetwork/TdnPilotBundle.svg?style=flat-square
[scrutinizer page]: https://scrutinizer-ci.com/g/TheDevNetwork/TdnPilotBundle
[scrutinizer coverage shield]: https://img.shields.io/scrutinizer/coverage/g/TheDevNetwork/TdnPilotBundle.svg?style=flat-square
[travis build shield]: https://img.shields.io/travis/TheDevNetwork/TdnPilotBundle.svg?style=flat-square
[travis page]: https://travis-ci.org/TheDevNetwork/TdnPilotBundle
[codacy shield]: https://img.shields.io/codacy/9a9be3063c8d44ca8709497469e3d097.svg?style=flat-square
[codacy page]: https://www.codacy.com/public/vpassapera/TdnPilotBundle_2
[sensio shield]: https://insight.sensiolabs.com/projects/84a6a21c-83e0-4f21-a66f-838d1ddc5e07/mini.png
[sensio page]: https://insight.sensiolabs.com/projects/84a6a21c-83e0-4f21-a66f-838d1ddc5e07
[pilot icon]: https://raw.githubusercontent.com/TheDevNetwork/Aux/master/images/icon_plane.png
[next version shield]: https://img.shields.io/badge/status-next--version-green.svg
[symfony 2]: http://symfony.com
[note]: https://img.shields.io/badge/note-*-orange.svg
[planned shield]: https://img.shields.io/badge/status-planned-5F9FDE.svg
[ilios core bundle]: https://github.com/ilios/ilios/tree/master/src/Ilios/CoreBundle
[ilios project]: https://github.com/ilios/ilios
[Entity Rules]: Resources/doc/entity-rules.md

