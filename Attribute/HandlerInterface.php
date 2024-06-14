<?php
namespace XTC\Container\Attribute;

use Psr\Container\ContainerInterface;

interface HandlerInterface
{
    /**
     * Handle the attributes
     * 
     * @param ContainerInterface $container
     * 
     * @return void
     */
    public function handle(object $service, ContainerInterface $container): void;

    /**
     * Set the service
     * 
     * @param object $service
     * 
     * @return HandlerInterface
     */
    public function setService(object $service): HandlerInterface;

    /**
     * Reset the handler
     * 
     * @return HandlerInterface
     */
    public function reset(): HandlerInterface;
}