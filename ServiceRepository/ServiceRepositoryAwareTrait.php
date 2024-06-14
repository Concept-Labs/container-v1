<?php
namespace XTC\Container\ServiceRepository;

use XTC\Container\ServiceRepository\ServiceRepositoryInterface;

trait ServiceRepositoryAwareTrait
{
    /**
     * The service repository
     *
     * @var ServiceRepositoryInterface|null
     */
    protected ?ServiceRepositoryInterface $serviceRepository = null;

    /**
     * {@inheritDoc}
     */
    public function setServiceRepository(ServiceRepositoryInterface $serviceRepository): void
    {
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * Get the service repository
     *
     * @return ServiceRepositoryInterface
     */
    protected function getServiceRepository(): ServiceRepositoryInterface
    {
        return $this->serviceRepository;
    }
    
}