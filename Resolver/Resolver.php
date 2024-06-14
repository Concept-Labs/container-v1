<?php
namespace XTC\Container\Resolver;

use Psr\Container\ContainerInterface;

class Resolver implements ResolverInterface
{
    /**
     * Closure for invoke
     *
     * @var \Closure
     */
    protected ?\Closure $resolver = null;

    protected function __construct()
    {
        /**
         * Avoid instantiation
         */
    }

    /**
     * {@inheritDoc}
     */
    static public function withResolver(\Closure $resolver): ResolverInterface
    {
        $instance = new static;
        $instance->setResolver($resolver);
        
        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public function setResolver(\Closure $resolver): void
    {
        $this->resolver = $resolver;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getResolver(): \Closure
    {
        return $this->resolver;
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container)
    {
        return ($this->getResolver())($container);
    }
}