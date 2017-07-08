# Cadre.Module

This is a lightweight module system based on [Aura.Di][].

This library defines several classes that implement 
`Aura\Di\ContainerConfigInterface` so it can be used in any project that uses 
Aura.Di.

This is in early development so please provide feedback via 
[Issues](https://github.com/cadrephp/Cadre.Module/issues).

## Installation and Autoloading

This package is installable and PSR-4 autoloadable via Composer as
[cadre/module][].

Alternatively, [download a release][], or clone this repository, then map the
`Cadre\Module\` namespace to the package `src/` directory.

## Dependencies

This package requires PHP 7.0 or later; it has been tested on PHP 7.0 and 
PHP 7.1. We recommend using the latest available version of PHP as a matter of
principle.

## Quality

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cadrephp/Cadre.Module/badges/quality-score.png?b=0.x)](https://scrutinizer-ci.com/g/cadrephp/Cadre.Module/?branch=0.x)
[![Code Coverage](https://scrutinizer-ci.com/g/cadrephp/Cadre.Module/badges/coverage.png?b=0.x)](https://scrutinizer-ci.com/g/cadrephp/Cadre.Module/?branch=0.x)
[![Build Status](https://travis-ci.org/cadrephp/Cadre.Module.svg?branch=0.x)](https://travis-ci.org/cadrephp/Cadre.Module)

To run the unit tests at the command line, issue `composer install` and then
`vendor/bin/phpunit` at the package root. This requires [Composer][] to be 
available as `composer`, and [PHPUnit][] to be available as `vendor/bin/phpunit`.

This package attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If
you notice compliance oversights, please send a patch via pull request.

## Example

```php
$loader = new ModuleLoader([
    FirstModule::class,
    SecondModule::class,
], 'development', 'web');

$builder = new ContainerBuilder();
$di = $builder->newConfiguredInstance([$loader]);

$obj = $di->newInstance(ClassFromThirdModule::class);
```

```php
use Aura\Di\Container;
use Cadre\Module\Module;

class FirstModule extends Module
{
    public function require()
    {
        if ($this->loader()->isContext('web')) {
            // Only require ThirdModule in the web context
            return [ThirdModule::class];
        } else {
            return [];
        }
    }

    public function define(Container $di)
    {
        $di->params[ClassFromFirstModule::class]['foo'] = 'bar';
    }
}
```

## Cadre\Module\ModuleInterface

This interface extends `Aura\Di\ContainerConfigInterface` and defines four methods.

### require()

Returns an array of class names of other modules that it requires.

### conflict()

Returns an array of class names of other modules that it conflicts with.

### replace()

Returns an array of class names of other modules that it replaces.

## Cadre\Module\Module

This is a base class that your module can extend. It contains default implementations of all
methods from `Cadre\Module\ModuleInterface`.

### loader()

This method is only defined on `Cadre\Module\Module` and returns the associated
`Cadre\Module\ModuleLoaderInterface`.

This is so you can conditionally configure your module based on the existance of other
modules.

```php
public function define(Container $di)
{
    if ($this->loader()->loaded(OtherModule::class)) {
        $di->set('service', $di->lazyNew(OtherService::class);
    } else {
        $di->set('service', $di->lazyNew(DefaultService::class);
    }
}
```

## Cadre\Module\ModuleLoaderInterface

This interface extends `Aura\Di\ContainerConfigInterface` and defines three methods.

### loaded($name)

Returns true or false if the module specified by `$name` has been loaded.

### isEnv($environment)

Returns true or false if the ModuleLoader was instanciated with the specified environment.

### isContext($context)

Returns true or false if the ModuleLoader was instanciated with the specified context.

## Cadre\Module\ModuleLoader

This class does all the work. It contains default implementations of all
methods from `Cadre\Module\ModuleInterface`.

### __construct(array $modules, string $environment = '', string $context = '')

When you create a new `ModuleLoader` you pass into it the modules you want to load.

You may also specify the environment you're running. When you specify a environment
we will check for a method requiring modules when in that environment. For example
if your environment is "dev" we will look for a method `requireDev`. 

To generate the method name we convert a snake cased (ex: special_environment) 
environment into a camel cased method name prefixed with "require"
(ex: requireSpecialEnvironment).

You may also specify the context you're running. By default we do nothing with the
context. However, you can query it via `isContext` method on the loader from inside
a module. For example if your context is "web" you could query against it like 
`if ($this->loader()->isContext('web')) { }` and configure things differently.

### protected resolveDependencies()

This method is called from `loaded`, `define` and `modify`.

It starts with the list of modules from the constructor and goes through them loading
the modules and then adding require and optionally require{Environment} modules to the
list to load.

Throws `Cadre\Module\ConflictingModuleException` if conflicting module is loaded.
Throws `Cadre\Module\AlreadyReplacedException` if replaced module has already been replaced.
Throws `Cadre\Module\AlreadyLoadedException` if replaced module has been loaded.

### define(Container $di)

Passes through to the `define` method on all loaded modules.

### modify(Container $di)

Passes through to the `modify` method on all loaded modules.

[Aura.Di]: https://github.com/auraphp/Aura.Di
[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
[Composer]: http://getcomposer.org/
[PHPUnit]: http://phpunit.de/
[download a release]: https://github.com/cadrephp/Cadre.Module/releases
[cadre/module]: https://packagist.org/packages/cadre/module
