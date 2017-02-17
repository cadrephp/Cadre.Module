<?php
namespace Cadre\Module;

use ArrayIterator;
use Aura\Di\Container;
use Aura\Di\ContainerConfigInterface;

class ModuleLoader implements ModuleLoaderInterface
{
    protected $modules = [];
    protected $environment;
    protected $isResolved = false;
    protected $containerConfigs = [];
    protected $conflictsWith = [];
    protected $replacedWith = [];

    public function __construct(array $modules, $environment = '')
    {
        $this->modules = $modules;
        $this->environment = $environment;
    }

    public function define(Container $di)
    {
        $this->resolveDependencies();
        foreach ($this->containerConfigs as $containerConfig) {
            if ($containerConfig instanceof ContainerConfigInterface) {
                $containerConfig->define($di);
            }
        }
    }

    public function modify(Container $di)
    {
        $this->resolveDependencies();
        foreach ($this->containerConfigs as $containerConfig) {
            if ($containerConfig instanceof ContainerConfigInterface) {
                $containerConfig->modify($di);
            }
        }
    }

    public function loaded($name)
    {
        $this->resolveDependencies();
        return isset($this->containerConfigs[$name]);
    }

    protected function resolveDependencies()
    {
        if ($this->isResolved) {
            return;
        }

        $modules = new ArrayIterator($this->modules);

        foreach ($modules as $module) {
            $module = $this->getModule($module);
            $name = get_class($module);

            if (isset($this->containerConfigs[$name])) {
                continue;
            }

            if (isset($this->replacedWith[$name])) {
                continue;
            }

            if (isset($this->conflictsWith[$name])) {
                throw new ConflictingModuleException($name, $this->conflictsWith[$name]);
            }

            $this->containerConfigs[$name] = $module;

            $this->resolveRequire($modules, $module);
            $this->resolveRequireEnv($modules, $module);
            $this->resolveConflict($module, $name);
            $this->resolveReplace($module, $name);
        }

        $this->isResolved = true;
    }

    protected function resolveRequire($modules, $module)
    {
        $requiredModules = $module->require();
        foreach ($requiredModules as $requiredModule) {
            $modules->append($requiredModule);
        }
    }

    protected function resolveRequireEnv($modules, $module)
    {
        $envMethod = 'require' . str_replace(' ', '', ucwords(
            strtolower(str_replace('_', ' ', trim($this->environment)))
        ));
        if ('require' !== $envMethod && method_exists($module, $envMethod)) {
            $requiredModules = $module->$envMethod();
            foreach ($requiredModules as $requiredModule) {
                $modules->append($requiredModule);
            }
        }
    }

    protected function resolveConflict($module, $name)
    {
        $conflictingModules = $module->conflict();
        foreach ($conflictingModules as $conflictingModule) {
            $this->conflictsWith[$conflictingModule] = $name;
            if (isset($this->containerConfigs[$conflictingModule])) {
                throw new ConflictingModuleException($conflictingModule, $name);
            }
        }
    }

    protected function resolveReplace($module, $name)
    {
        $replacesModules = $module->replace();
        foreach ($replacesModules as $replacesModule) {
            if (isset($this->replacedWith[$replacesModule])) {
                throw new AlreadyReplacedException(
                    $name,
                    $replacesModule,
                    $this->replacedWith[$replacesModule]
                );
            }
            if (isset($this->containerConfigs[$replacesModule])) {
                throw new AlreadyLoadedException(
                    $name,
                    'replace',
                    $replacesModule
                );
            }
            $this->replacedWith[$replacesModule] = $name;
        }
    }

    protected function getModule($module)
    {
        if (is_string($module)) {
            $module = new $module($this);
        }

        if (! $module instanceof ModuleInterface) {
            throw new \InvalidArgumentException(
                'Modules must implement ModuleInterface'
            );
        }

        return $module;
    }
}
