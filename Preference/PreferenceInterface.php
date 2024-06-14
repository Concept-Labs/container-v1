<?php
namespace XTC\Container\Preference;


use XTC\Config\ConfigInterface;
use XTC\Container\Resolver\ResolverInterface;

interface PreferenceInterface
{
    /**
     * Get instance with configuration
     *
     * @param ConfigInterface $config The configuration instance
     * 
     * @return PreferenceInterface The preference instance
     */
    static function withConfig(ConfigInterface $config): PreferenceInterface;

    /**
     * Get a service resolver
     * 
     * @param mixed ...$args Optional. The constructor arguments can be passed explicitly, 
     *                       So no need to resolve.
     *                       Can be used for Factories
     *
     * @return ResolverInterface The service resolver instance
     */
    function getResolver(...$args): ResolverInterface;

    /**
     * Get a service instance
     *
     * @return void
     */
    function getService();
    
    /**
     * Check if the preference has a referer
     *
     * @return boolean True if the preference has a referer
     */
    function hasReferer(): bool;

    /**
     * Get a referer
     *
     * @return PreferenceInterface|null The referer instance
     */
    function getReferer();

    /**
     * Set the referer
     *
     * @param Preference $referer The referer
     * 
     * @return void
     */
    function setReferer(Preference $referer): void;
    

    /**
     * Get a identifier
     *
     * @return string The string identifier
     */
    function getId(): string;

    /**
     * Get a class name
     *
     * @return string|null  The class name or null
     */
    function getClass();

    /**
     * Get a reference
     *
     * @return string|null The reference identifier or null
     */
    function getReference();

    /**
     * Check if a preference has the container configuration
     *
     * @return boolean true if the preference has the container configuration
     */
    function hasDiConfig(): bool;

    /**
     * Get a container configuration data
     *
     * @return array The configuration data or null
     * //@TODO:VG: return type?
     */
    function getDiConfigData(): array;

    /**
     * Check if a preference has the inline container configuration
     * 
     * @return bool
     */
    function hasInlineDiConfig(): bool;

    /**
     * Get a inline container configuration data
     *
     * @return array|null The configuration data or null
     */
    function getInlineContainerConfigData(): array;

    /**
     * Check if a preference has the inline container configuration file
     * 
     * @return bool
     */
    function hasInlineDiConfigFile(): bool;

    /**
     * Get a inline container configuration data from the file
     *
     * @return array|null The configuration data or null
     */
    function getInlineDiConfigFileData(): array;

    /**
     * Check if a preference has the container configuration class
     * 
     * @return bool
     */
    function hasDiConfigClass(): bool;
    
    /**
     * Get a container configuration data from the class
     *
     * @return array|null The configuration data or null
     */
    function getContainerConfigClassData(): array;
    
    /**
     * Check if preefrence configured as singleton
     *
     * @return boolean
     */
    function isSingleton(): bool;

    /**
     * Get a Parameter configuration as array
     *
     * @param string $name The parameter name
     * 
     * @return array|null The configuration data or null
     */
    public function getParameterConfigData(string $name);

    /**
     * Reset
     *
     * @return void
     */
    function reset(): void;
    
}