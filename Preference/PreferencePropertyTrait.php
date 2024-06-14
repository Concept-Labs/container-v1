<?php
namespace XTC\Container\Preference;

use Reflection;
use ReflectionClass;
use XTC\Container\Config\DIConfigInterface;

trait PreferencePropertyTrait
{

    /**
     * @var object|null The resolved service
     */
    protected ?object $service = null;
    
    /**
     * @var PreferenceInterface|null The referer
     */
    protected ?PreferenceInterface $referer = null;
    
    /**
     * {@inheritDoc}
     */
    public function getService(): object
    {
        return $this->service;
    }

    /**
     * {@inheritDoc}
     */
    public function setService($service): void
    {
        $this->service = $service;
    }

    /**
     * {@inheritDoc}
     */
    public function getId(): string
    {
        return $this->getConfigValue(
            DIConfigInterface::NODE_ID
        );
    }

    /**
     * {@inheritDoc}
     */
    public function hasReferer(): bool
    {
        return $this->referer instanceof PreferenceInterface;
    }

    /**
     * {@inheritDoc}
     */
    public function getReferer()
    {
        return $this->referer;
    }

    /**
     * {@inheritDoc}
     */
    public function setReferer(PreferenceInterface $referer): void
    {
        /**
         * Save the current config state
         */
        $this->config->pushState();
        /**
         * Merge a configuration from referer`s configuration 
         * So a referer may use the same configuration except:
         */
        $refererConfigData = $referer->getConfigValue("");
        unset(//@TODO:VG merge path?
            $refererConfigData[DIConfigInterface::NODE_ID],  
            $refererConfigData[DIConfigInterface::NODE_CLASS],
            $refererConfigData[DIConfigInterface::NODE_PREFERENCE],
        );
        $this->getConfig()->mergeFrom($refererConfigData);
        
        $this->referer = $referer;
    }

    /**
     * {@inheritDoc}
     */
    public function getReference()
    {
        return $this->getConfigValue(
            DIConfigInterface::NODE_REFERENCE
        );
    }
    
    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->getConfigValue(
            DIConfigInterface::NODE_CLASS
        );
    }


    /**
     * {@inheritDoc}
     */
    function hasDiConfig(): bool
    {
        return null !== $this->getConfigValue(
            DIConfigInterface::NODE_DI_CONFIG
        );
    }

    /**
     * {@inheritDoc}
     */
    function getDiConfigData(): array
    {
        return $this->getConfigValue(
            DIConfigInterface::NODE_DI_CONFIG
        ) ?? [];
    }

    function hasInlineDiConfig(): bool
    {
        //@TODO: CHECK return value for php versions (null | false)
        return $this->getReflectionConstant(DIConfigInterface::INLINE_DI_CONFIG_CONSTANT) ? true : false;
    }
    

    /**
     * {@inheritDoc}
     */
    function getInlineContainerConfigData(): array
    {
        if ($this->hasInlineDiConfig()) {
            $configData = $this->getReflectionConstant(DIConfigInterface::INLINE_DI_CONFIG_CONSTANT);
            if (!is_array($configData)) {
                //@TODO:VG 
                // throw new \InvalidArgumentException(
                //     sprintf(_('The inline container config must be an array, %s given'), gettype($configData))
                // );
                return []; //???
            }
            //@TODO:VG validate config
            return $configData;
        }
        return [];
    }
    
    function hasDiConfigClass(): bool
    {
        //@TODO: CHECK return value for php versions (null | false)
        return $this->getReflectionConstant(DIConfigInterface::INLINE_DI_CONFIG_CLASS_CONSTANT) ? true : false;
    }
    

    /**
     * {@inheritDoc}
     */
    function getContainerConfigClassData(): array
    {
        if ($this->hasDiConfigClass()) {
            
            $configReflectionClass = new ReflectionClass(
                $this->getReflectionConstant(DIConfigInterface::INLINE_DI_CONFIG_CLASS_CONSTANT)
            );

            $config = $configReflectionClass->newInstanceWithoutConstructor();
            if (!is_object($config)) {
                return []; //???
            }
            $configData = $config->getConfig();
            //@TODO:VG validate config
            return $configData;
        }
        return [];
    }

    /**
     * {@inheritDoc}
     */
    function hasInlineDiConfigFile(): bool
    {
        return $this->getReflectionConstant(DIConfigInterface::INLINE_DI_CONFIG_FILE_CONSTANT) ? true : false;
    }
    
    /**
     * {@inheritDoc}
     */
    function getInlineDiConfigFileData(): array
    {
        if (!$this->hasInlineDiConfigFile()) {
            //@TODO:VG: return type?
            return [];
        }

        $file = join(
            DIRECTORY_SEPARATOR,
            [
                dirname($this->getReflectionClass()->getFileName()),
                $this->getReflectionConstant(DIConfigInterface::INLINE_DI_CONFIG_FILE_CONSTANT)
            ]
        );
        /**
         * @todo:vg data provider
         */
        return json_decode(file_get_contents($file), true);
    }

    /**
     * {@inheritDoc}
     */
    public function isSingleton(): bool
    {
        return null !== $this->getConfigValue(
            DIConfigInterface::NODE_SINGLETON
        )
            ? $this->getConfigValue(DIConfigInterface::NODE_SINGLETON) 
            : false;
    }

    /**
     * {@inheritDoc}
     */
    public function getParameterConfigData(string $name)
    {
        return $this->getConfigValue(
            DIConfigInterface::NODE_PARAMETERS,
            $name
        );
    }

}