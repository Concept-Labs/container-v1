<?php
namespace XTC\Container;

use Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends PsrContainerInterface
{
    /**
     * Create a new service instance
     *
     * @param string $id The service identifier.                      
     *                   May be a service name or a class name
     * 
     * //param mixed  ...$args The optional service constructor arguments
     * 
     * @return mixed
     */
    function create(string $id, ...$args);

    /**
     * @deprecated
     * 
     * Create a new service instance
     *
     * @param string $id The service identifier.                      
     *                   May be a service name or a class name
     * 
     * //param mixed  ...$args The optional service constructor arguments
     * 
     * @return mixed
     */
    function new(string $id, ...$args);

    /**
     * Attach a service 
     *
     * @param string $id      The service identifier
     * @param mixed  $service The service instance or whatever else
     * 
     * @return void
     */
    function attach(string $id, $service): void;

}