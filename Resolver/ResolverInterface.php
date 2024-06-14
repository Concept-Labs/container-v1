<?php
namespace XTC\Container\Resolver;

use Psr\Container\ContainerInterface;

interface ResolverInterface
{
   
    /**
     * Get the resolver based on closure
     *
     * @param \Closure $resolver The resolver
     * 
     * @return ResolverInterface The configured resolver instance
     */
    static public function withResolver(\Closure $resolver);

    /**
     * Invoke resolver
     *
     * @param ContainerInterface $container The container
     * 
     * @return mixed Invoke the resolver
     */
    public function __invoke(ContainerInterface $container);

    /**
     * Set a resolver
     *
     * @param \Closure $resolver the closure to invoke
     * 
     * @return void
     */
    function setResolver(\Closure $resolver);
    
    /**
     * Get a resolver
     * 
     * @return \Closure The resolver closure
     */
    function getResolver(): \Closure;
}