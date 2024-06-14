<?php
namespace XTC\Container\Factory;

use Psr\Container\ContainerInterface;
use XTC\Container\ContainerInterface as CustomContainer;

class Factory implements FactoryInterface
{

    /**
     * @var ContainerInterface|null The container
     */
    protected ?ContainerInterface $container = null;

    /**
     * @var string|null The service identifier
     */
    protected ?string $serviceId = null;

    /**
     * @var array|null The service constructor parameters
     */
    protected ?array $args = null;

    /**
     * The constructor
     *
     * @param ContainerInterface $container The factory
     * @param string             $serviceId The service identifier
     * @param array              ...$args   The service constructor parameters
     */
    public function __construct(ContainerInterface $container, ?string $serviceId = null, ?array $args = null)
    {
        $this->container = $container;
        $this->serviceId = $serviceId;
        $this->args = $args;
    }

    /**
     * {@inheritDoc}
     */
    public function setServiceId(string $serviceId): FactoryInterface
    {
        $this->serviceId = $serviceId;

        return $this;
    }

    /**
     * Get the service identifier
     * 
     * @return string
     */
    protected function getServiceId(): string
    {
        return $this->serviceId;
    }

    /**
     * Get the container
     * 
     * @return ContainerInterface
     */
    protected function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * {@inheritDoc}
     */
    public function new(...$args)
    {
        return $this->create(...$args);
    }

    /**
     * {@inheritDoc}
     */
    public function create(...$args)
    {
        if (null === $this->getServiceId()) {
            throw new \Exception(_('Service identifier not set'));
        }
        $arguments = !empty($args) ? $args : $this->args ?? [];
        return $this->getContainer()
            ->create($this->getServiceId(), ...$arguments);
        
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(...$args)
    {
        return $this->create(...$args);
    }

    /**
     * {@inheritDoc}
     */
    public function getFactory(...$args): \Closure
    {
        return \Closure::bind(
            function () use ($args) {
                return $this->create(...$args);
            },
            $this
        );
    }
}