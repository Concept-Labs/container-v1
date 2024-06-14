<?php
namespace XTC\Container\Factory;

interface FactoryInterface
{
   /**
    * Set the service identifier
    * 
    * @param string $serviceId
    * 
    * @return FactoryInterface
    */
   public function setServiceId(string $serviceId): FactoryInterface;

    /**
     * Create the instance
     * 
     * @param mixed ...$args Optional constructor parameters
     * 
     * @return object The service instance
     */
    function new(...$args);

    /**
     * Create the instance (alias)
     * @see FactoryInterface::new()
     * 
     * @param mixed ...$args Optional constructor parameters
     * 
     * @return object The service instance
     */
    function create(...$args);

    /**
     * The alias for new()
     *
     * @return object The service instance
     */
    function __invoke();

}