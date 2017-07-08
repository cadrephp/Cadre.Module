<?php
namespace Cadre\Module;

use Aura\Di\Container;
use Aura\Di\ContainerConfigInterface;

class ModuleLoader implements ModuleLoaderInterface
{
    protected $modules = [];
    protected $environment;
    protected $context;
    protected $isResolved = false;
    protected $containerConfigs = [];
    protected $conflictsWith = [];
    protected $replacedWith = [];
    protected $touchedModules = [];

    public function __construct(array $modules, $environment = '', $context = '')
    {
        $this->modules = $modules;
        $this->environment = $environment;
        $this->context = $context;
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

    public function isEnv($environment)
    {
        return 0 === strcmp($environment, $this->environment);
    }

    public function isContext($context)
    {
        return 0 === strcmp($context, $this->context);
    }

    protected function resolveDependencies()
    {
        if ($this->isResolved) {
            return;
        }

        $idx = 0;

        while ($idx < count($this->modules)) {
            $module = $this->getModule($this->modules[$idx]);
            $name = get_class($module);

            if (isset($this->containerConfigs[$name])) {
                $idx++;
                continue;
            }

            if (isset($this->replacedWith[$name])) {
                $idx++;
                continue;
            }

            if (isset($this->conflictsWith[$name])) {
                throw new ConflictingModuleException($name, $this->conflictsWith[$name]);
            }

            if (empty($this->touchedModules[$name])) {
                $this->touchedModules[$name] = 0;
            }

            if (1 < $this->touchedModules[$name]) {
                throw new CircularReferenceException($name, $this->touchedModules[$name]);
            }

            $this->touchedModules[$name]++;

            $this->resolveRequire($idx, $module);
            $this->resolveRequireEnv($idx, $module);
            $this->resolveConflict($module, $name);
            $this->resolveReplace($module, $name);

            $module = $this->getModule($this->modules[$idx]);
            if (0 === strcmp($name, get_class($module))) {
                // No new modules were inserted
                $this->containerConfigs[$name] = $module;
            } else {
                // Do not increment $idx, reprocess this $idx
                continue;
            }

            $idx++;
        }

        $this->isResolved = true;
    }

    protected function injectRequiredModule($idx, $requiredModules)
    {
        foreach ($requiredModules as $requiredModule) {
            $foundIdx = array_search($requiredModule, $this->modules);
            if (false === $foundIdx) {
                // New module, not found yet
                array_splice($this->modules, $idx, 0, $requiredModule);
            } elseif ($idx < $foundIdx) {
                // Found module after us in list. Move up
                array_splice($this->modules, $foundIdx, 1);
                array_splice($this->modules, $idx, 0, $requiredModule);
            }
        }
    }

    protected function resolveRequire($idx, $module)
    {
        $requiredModules = $module->require();
        $this->injectRequiredModule($idx, $requiredModules);
    }

    protected function resolveRequireEnv($idx, $module)
    {
        $envMethod = 'require' . str_replace(' ', '', ucwords(
            strtolower(str_replace('_', ' ', trim($this->environment)))
        ));
        if ('require' !== $envMethod && method_exists($module, $envMethod)) {
            $requiredModules = $module->$envMethod();
            $this->injectRequiredModule($idx, $requiredModules);
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
