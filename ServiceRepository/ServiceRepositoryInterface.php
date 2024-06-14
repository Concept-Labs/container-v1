<?php
namespace XTC\Container\ServiceRepository;

interface ServiceRepositoryInterface
{
    /**
     * Check if repository has a service
     *
     * @param string $id The service identifier
     * 
     * @return boolean
     */
    function has(string $id): bool;

    /**
     * Get a service from the repository
     *
     * @param string $id The service identifier
     * 
     * @return mixed
     */
    function get(string $id);

    /**
     * Register a service
     *
     * @param string $id      The service identifier
     * @param mixed  $service The service to register
     * 
     * @return void
     */
    function attach(string $id, $service): void;

    /**
     * Reset
     *
     * @return void
     */
    function reset(): void;
}