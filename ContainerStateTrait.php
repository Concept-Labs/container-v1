<?php
namespace XTC\Container;

trait ContainerStateTrait
{
    
    /**
     * The state stack
     * Use to save and resotre the current container configuration and the service repository
     *
     * @var array
     */
    protected array $containerStateStack = [];

    /**
     * Save current containers repository
     * Save current configuration
     *
     * @return void
     */
    protected function pushState(): void
    {
        array_push(
            $this->containerStateStack,
            [
                "serviceRepository" => clone $this->getServiceRepository(),
                "config" => clone $this->config
            ]
        );
    }
    

    /**
     * Restore container repository
     * Restore configuration
     *
     * @return void
     */
    protected function popState(): void
    {
        $popState = array_pop($this->containerStateStack);
        if (null !== $popState) {
            $this->setServiceRepository($popState['serviceRepository']);
            $this->setConfig($popState['config']);
        }
    }
}