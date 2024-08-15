<?php
namespace XTC\Container;

class LazyContainer implements LazyContainerInterface
{
    /**
     *
     *
     * @var callable
     */
    protected $container = null;

    public function __construct(ContainerInterface $container)
    {
        $this->container = function() use ($container) {
            return $container;
        };
    }

    protected function getContainer(): callable
    {
        return $this->container;
    }

    public function has(string $id): bool
    {
        $container = $this->getContainer();
        return ($container())->has($id);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $id/*, ...$args*/): object
    {
        $container = $this->getContainer();
        return ($container())->get($id/*, ...$args*/);
    }

    function new(string $id, ...$args)
    {
        $container = $this->getContainer();
        return ($container())->new($id, ...$args);
    }

    /**
     * Attach a service 
     *
     * @param string $id      The service identifier
     * @param mixed  $service The service instance or whatever else
     * 
     * @return void
     */
    function attach(string $id, $service): void
    {
        $container = $this->getContainer();
        ($container())->attach($id, $service);
    }
}