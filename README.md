# Cadre.Module

This is a lightweight module system based on [Aura.Di](https://github.com/auraphp/Aura.Di).

This library defines several classes that implement `Aura\Di\ContainerConfigInterface` so
it can be used in any project that uses Aura.Di.

This is in early development so please provide feedback via [Issues](https://github.com/cadrephp/Cadre.Module/issues.).

## Example

```php
$loader = new ModuleLoader([
    FirstModule::class,
    SecondModule::class,
], $isDev = true);

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
        return [ThirdModule::class];
    }

    public function define(Container $di)
    {
        $di->params[ClassFromFirstModule::class]['foo'] = 'bar';
    }
}
```

## Cadre\Module\ModuleInterface

This interface extends `Aura\Di\ContainerConfigInterface` and defines four new methods.

### require()

Returns an array of class names of other modules that it requires.

### requireDev()

Returns an array of class names of other modules that it requires if in development.

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

This interface extends `Aura\Di\ContainerConfigInterface` and defines one new method.

### loaded($name)

Returns true or false if the module specified by `$name` has been loaded.

## Cadre\Module\ModuleLoader

This class does all the work. It contains default implementations of all
methods from `Cadre\Module\ModuleInterface`.

### __construct(array $modules, bool $isDev = false)

When you create a new `ModuleLoader` you pass into it the modules you want to load.

### protected resolveDependencies()

This method is called from `loaded`, `define` and `modify`.

It starts with the list of modules from the constructor and goes through them loading
the modules and then adding require and optionally requireDev modules to the list to load.

Throws `Cadre\Module\ConflictingModuleException` if conflicting module is loaded.
Throws `Cadre\Module\AlreadyReplacedException` if replaced module has already been replaced.
Throws `Cadre\Module\AlreadyLoadedException` if replaced module has been loaded.

### define(Container $di)

Passes through to the `define` method on all loaded modules.

### modify(Container $di)

Passes through to the `modify` method on all loaded modules.
