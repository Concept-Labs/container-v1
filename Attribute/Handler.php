<?php
namespace XTC\Container\Attribute;

use cash;
use Psr\Container\ContainerInterface;
use ReflectionMethod;
use ReflectionObject;
use XTC\Container\Container;

/**
 * The attributes handler
 */
class Handler implements HandlerInterface
{

    /**
     * @var ContainerInterface
     */
    protected ?ContainerInterface $container = null;

    /**
     * @var object|null $service The service handled by the handler
     */
    protected ?object $service = null;


    /**
     * @var ReflectionObject|null $reflection The reflection object of the service
     */
    protected ?ReflectionObject $reflection = null;

    /**
     * @return array
     */
    protected function getHandlers(): array
    {
        return [
            'di' => 'handleDiConfig',
            'injector' => 'handleInjector',
            'initializer' => 'handleInitializer',
        ];
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(object $service, ContainerInterface $container): void
    {

        $this->container = $container;
        $this->service = $service;

        if (!method_exists(ReflectionMethod::class, 'getAttributes')) {
            /**
             * @todo:vg Implement a fallback for PHP < 8.0
             * f.e. method __di(...) for Injector
             * f.e. method __init(...) for Initializer
             * etc.
             */
            return;
        }

        foreach ($this->getHandlers() as $handler) {
            $this->{$handler}();
        }

        // $this->handleDiConfig();
        // $this->handleInjector();
        // $this->handleInitializer();
    }

    /**
     * Get the service
     * 
     * @return object
     */
    protected function getService(): object
    {
        return $this->service;
    }

    /**
     * Get the service reflection object
     * 
     * @return ReflectionObject
     */
    protected function getServiceRelection(): ReflectionObject
    {
        if (!$this->reflection instanceof ReflectionObject) {
            $this->reflection = new ReflectionObject($this->getService());
        }

        return $this->reflection;
    }

    /**
     * Get the DI attribute class
     * 
     * @return string
     */
    protected function getInjectorClass(): string
    {
        return Injector::class;
    }

    /**
     * Get the Initialize attribute class
     * 
     * @return string
     */
    protected function getInitializerClass(): string
    {
        return Initializer::class;
    }


    /**
     * Handle the DI config 
     * 
     * @return void
     */
    protected function handleDiConfig(): void
    {
        /**
         * @todo:vg Implement the DI config handler
         */
    }

    /**
     * Handle the Dependency Injection
     * 
     * @return void
     */
    protected function handleInjector(): void
    {
        
        if (empty($this->getServiceRelection()->getAttributes($this->getInjectorClass()))) {
            return;
        }

        /**
         * @var ReflectionMethod $method
         */
        foreach ($this->getServiceRelection()->getMethods() as $method) {
            if (empty($method->getAttributes($this->getInitializerClass()))) {
                continue;
            }
            
            $diParameters = [];

            foreach ($method->getParameters() as $parameter) {
                /**
                 * @var ReflectionNamedType $type
                 */
                $type = $parameter->getType();

                if (null === $type) {
                    continue;
                }

                if (!$this->getContainer()->has($type->getName())) {
                    /**
                     * @todo:vg: probably skip non existing services?
                     * decide what to do
                     * probably config for enabling/disabling
                     */
                    //continue;
                }
                try {
                    $diParameters[] = $this->getContainer()->get($type->getName());
                } catch (\Throwable $e) {
                    /**
                     * @todo:vg: throw or ignore?
                     */
                    throw new \RuntimeException("DI for {$type->getName()} failed", 0, $e);
                    /**
                     * exit for sure
                     */
                    return;
                }
            }
            try {
                $method->invoke($this->getService(), ...$diParameters);
            } catch (\Throwable $e) {
                /**
                 * @todo:vg: throw or ignore?
                 */
                throw $e;
            }
                
        }
    }

        

    /**
     * @deprecated
     * Handle the initializer
     * just test example
     * 
     * @return void
     */
    protected function handleInitializer(): void
    {
        if (empty($this->getServiceRelection()->getAttributes($this->getInitializerClass()))) {
            return;
        }

        foreach ($this->getServiceRelection()->getMethods() as $method) {
            if (empty($method->getAttributes($this->getInitializerClass()))) {
                continue;
            }
            $method->invoke($this->getService());
        }
    }

    /**
     * Reset the attributes handler
     * 
     * @return HandlerInterface
     */
    public function reset(): HandlerInterface
    {
        $this->container = null;
        $this->service = null;
        $this->reflection = null;
        return $this;
    }
}