<?php
namespace XTC\Container\Preference\Parameter;

use XTC\Config\ConfigInterface;
use XTC\Container\Preference\Parameter\Exception\ParameterException;
use XTC\Container\Resolver\ResolverInterface;

/**
 * Interface representing a parameter.
 */
interface ParameterInterface
{
    /**
     * Create a new Parameter instance with the given configuration.
     *
     * @param ConfigInterface $config The configuration to associate with the parameter.
     * 
     * @return ParameterInterface A new Parameter instance.
     */
    static function withConfig(ConfigInterface $config): ParameterInterface;

    /**
     * Get the parameter resolver
     *
     * @return ResolverInterface
     * 
     * @throws ParameterException If can not resolve the parameter
     */
    function getResolver(): ResolverInterface;

    /**
     * Get the name of the parameter.
     *
     * @return string The name of the parameter.
     */
    function getName();

    /**
     * Get the type of the parameter.
     *
     * @return mixed The type of the parameter.
     */
    function getType();

    /**
     * Get the value of the parameter.
     *
     * @return mixed The value of the parameter.
     */
    function getValue();

    /**
     * Check if the parameter has defined value
     * If not it means that the parameter is not configured in DI
     * So a reflection will be used for resolving
     *
     * @return boolean
     */
    public function hasValue(): bool;

    /**
     * Get the reflection for parameter
     *
     * @return \ReflectionParameter| null The reflection for parameter
     */
    function getReflection();
    
    /**
     * Se the reflection
     *
     * @param \ReflectionParameter $reflection The reflection for parameter
     * 
     * @return void
     */
    public function setReflection(\ReflectionParameter $reflection): void;


    /**
     * Reset the parameter.
     *
     * This method resets the parameter to its initial state.
     *
     * @return void
     */
    function reset(): void;
}