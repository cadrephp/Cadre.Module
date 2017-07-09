# Changelog

All notable changes to this package will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## 0.5.3 - 2017-07-08
### Changed
-  Allowing anything implementing Aura\Di\ContainerConfigInterface to be loaded

## 0.5.2 - 2017-07-07
### Changed
-  Required modules now get loaded before the module that depended on them
-  If there is a circular reference a `CircularReferenceException` exception is thrown

## 0.5.1 - 2017-07-07
### Added
-  `$context = ''` to ModuleLoader constructor
-  `isContext` method to ModuleLoader

## 0.5.0 - 2017-07-03
### Added
-  `isEnv` method to ModuleLoader

## 0.4.1 - 2017-03-17
### Changed
- Updating dev dependencies

## 0.4.0 - 2017-02-17
Transitioned from `isDev` and `requireDev` to `environment` and `require{Environment}`
### Changed
- `$isDev = false` in ModuleLoader constructor to `$environment = ''`
### Removed
- `isDev()` method from `ModuleLoader` and `ModuleLoaderInterface`
- `requireDev()` method from `Module` and `ModuleInterface`
### Added
- Support for new `require{Environment}` methods (ex: `requireSpecialEnvironment`)

## 0.3.0 - 2016-11-30
Bringing up to speed with new Cadre.Package skeleton
### Added
- CHANGELOG.md, .editorconfig, and .gitattributes files
### Changed
- Cleaned up build.xml removing extra targets and adding aliases
- phpunit.xml.dist Renaming testsuite name to Cadre
- phpunit.xml.dist Removing whitelist exclude for non-existant file

## 0.2.0 - 2016-11-07
### Added
- Method isDev to ModuleLoaderInterface and ModuleLoader

## 0.1.0 - 2016-10-20
### Added
- Initial release
