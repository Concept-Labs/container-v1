<?php
namespace XTC\Container\ServiceRepository;

class ServiceRepository implements ServiceRepositoryInterface
{
    /**
     * The service container
     *
     * @var array<string, object>
     */
    protected array $services = [];

    /**
     * {@inheritDoc}
     */
    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $id)
    {
        if ($this->has($id)) {
            return $this->services[$id];
        }
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(string $id, $service): void
    {
        $this->services[$id] = $service;
    }

    /**
     * {@inheritDoc}
     */
    public function reset(): void
    {
        $this->services = [];
    }
}