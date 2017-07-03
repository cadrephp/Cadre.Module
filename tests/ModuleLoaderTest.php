<?php
namespace Cadre\Module;

use Aura\Di\ContainerBuilder;
use Cadre\Module\Sample\ConflictModule;
use Cadre\Module\Sample\LoadedModule;
use Cadre\Module\Sample\NotAModule;
use Cadre\Module\Sample\ReplaceModule;
use Cadre\Module\Sample\ReplaceAgainModule;
use Cadre\Module\Sample\RequireModule;
use Cadre\Module\Sample\RequiredModule;
use Cadre\Module\Sample\RequireDevModule;
use Cadre\Module\Sample\RequireEnvModule;
use Cadre\Module\Sample\Value;
use InvalidArgumentException;

class ModuleLoaderTest extends \PHPUnit\Framework\TestCase
{
    public function testModuleRequire()
    {
        $loader = new ModuleLoader([
            RequireModule::class,
        ]);

        $builder = new ContainerBuilder();
        $di = $builder->newConfiguredInstance([$loader]);

        $value = $di->newInstance(Value::class);

        $this->assertEquals('required', $value->value);
    }

    public function testModuleRequireDevWhenDev()
    {
        $loader = new ModuleLoader([
            RequireDevModule::class,
        ], 'dev');

        $builder = new ContainerBuilder();
        $di = $builder->newConfiguredInstance([$loader]);

        $value = $di->newInstance(Value::class);

        $this->assertEquals('required', $value->value);
    }

    public function testIsEnv()
    {
        $loader = new ModuleLoader([
            RequireDevModule::class,
        ], 'dev');

        $this->assertTrue($loader->isEnv('dev'));
        $this->assertFalse($loader->isEnv('prod'));
    }

    public function testModuleRequireDevWhenNotDev()
    {
        $loader = new ModuleLoader([
            RequireDevModule::class,
        ]);

        $builder = new ContainerBuilder();
        $di = $builder->newConfiguredInstance([$loader]);

        $value = $di->newInstance(Value::class);

        $this->assertEquals('undefined', $value->value);
    }

    public function testLoadModuleTwice()
    {
        $loader = new ModuleLoader([
            RequireModule::class,
            RequireModule::class,
        ]);

        $builder = new ContainerBuilder();
        $di = $builder->newConfiguredInstance([$loader]);

        $value = $di->newInstance(Value::class);

        $this->assertEquals('required', $value->value);
    }

    public function testReplaceModule()
    {
        $loader = new ModuleLoader([
            ReplaceModule::class,
            RequireModule::class,
        ]);

        $builder = new ContainerBuilder();
        $di = $builder->newConfiguredInstance([$loader]);

        $value = $di->newInstance(Value::class);

        $this->assertEquals('replace', $value->value);
    }

    public function testAlreadyReplacedModule()
    {
        $this->expectException(AlreadyReplacedException::class);

        $loader = new ModuleLoader([
            ReplaceModule::class,
            ReplaceAgainModule::class,
            RequireModule::class,
        ]);

        $builder = new ContainerBuilder();
        $di = $builder->newConfiguredInstance([$loader]);

        $value = $di->newInstance(Value::class);
    }

    public function testAlreadyLoadedModule()
    {
        $this->expectException(AlreadyLoadedException::class);

        $loader = new ModuleLoader([
            RequiredModule::class,
            ReplaceModule::class,
        ]);

        $builder = new ContainerBuilder();
        $di = $builder->newConfiguredInstance([$loader]);

        $value = $di->newInstance(Value::class);
    }

    public function testConflictModule()
    {
        $this->expectException(ConflictingModuleException::class);

        $loader = new ModuleLoader([
            RequiredModule::class,
            ConflictModule::class,
        ]);

        $builder = new ContainerBuilder();
        $di = $builder->newConfiguredInstance([$loader]);

        $value = $di->newInstance(Value::class);
    }

    public function testConflictModuleAlt()
    {
        $this->expectException(ConflictingModuleException::class);

        $loader = new ModuleLoader([
            ConflictModule::class,
            RequiredModule::class,
        ]);

        $builder = new ContainerBuilder();
        $di = $builder->newConfiguredInstance([$loader]);

        $value = $di->newInstance(Value::class);
    }

    public function testLoadedModule()
    {
        $loader = new ModuleLoader([
            ConflictModule::class,
            LoadedModule::class,
        ]);

        $builder = new ContainerBuilder();
        $di = $builder->newConfiguredInstance([$loader]);

        $value = $di->newInstance(Value::class);

        $this->assertEquals('loaded', $value->value);
    }

    public function testNotLoadedModule()
    {
        $loader = new ModuleLoader([
            LoadedModule::class,
        ]);

        $builder = new ContainerBuilder();
        $di = $builder->newConfiguredInstance([$loader]);

        $value = $di->newInstance(Value::class);

        $this->assertEquals('not-loaded', $value->value);
    }

    public function testInvalidModuleModuleAlt()
    {
        $this->expectException(InvalidArgumentException::class);

        $loader = new ModuleLoader([
            NotAModule::class,
        ]);

        $builder = new ContainerBuilder();
        $di = $builder->newConfiguredInstance([$loader]);

        $value = $di->newInstance(Value::class);
    }

    public function testModuleRequireEnv()
    {
        $loader = new ModuleLoader([
            RequireEnvModule::class,
        ], 'special_environment');

        $builder = new ContainerBuilder();
        $di = $builder->newConfiguredInstance([$loader]);

        $value = $di->newInstance(Value::class);

        $this->assertEquals('required', $value->value);
    }
}
