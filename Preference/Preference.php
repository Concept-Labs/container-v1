<?php
namespace XTC\Container\Preference;

use Psr\Container\ContainerInterface;
use XTC\Config\ConfigAwareInterface;
use XTC\Config\ConfigInterface;
use Xtc\Config\ConfigAwareTrait;
use XTC\Container\Config\DIConfigInterface;
use XTC\Container\Exception\InstantiateException;
use XTC\Container\Preference\Exception\PreferenceException;
use XTC\Container\Preference\Parameter\Parameter;
use XTC\Container\Preference\Parameter\ParameterInterface;
use XTC\Container\Resolver\Resolver;
use XTC\Container\Resolver\ResolverInterface;

/**
 * The preference implementation
 * The preference is configuration entity used by the container
 * The preference contains a data for the service instantiation
 * See container configuration files
 */
class Preference implements PreferenceInterface, ConfigAwareInterface
{
    use ConfigAwareTrait;
    use PreferencePropertyTrait;
    use PreferenceReflectionTrait;

    /**
     * Private constructor
     */
    private function __construct()
    {
        /**
         * No direct instantiation
         */
    }

    /**
     * @todo: non static
     * {@inheritDoc}
     */
    static public function withConfig(ConfigInterface $config): PreferenceInterface
    {
        /**
         * Can`t be created using the container
         */
        $preference = new static; //@todo: clone

        $preference->setConfig($config);

        return $preference;
    }

    /**
     * {@inheritDoc}
     */
    public function getResolver(...$args): ResolverInterface
    {
        
        if (true === $this->canInstanceWithoutConstructor()) {
            /**
             * Create the service instance without a constructor
             * 
             * @var object $service
             */
            $resolver = \Closure::bind(
                function (/*ContainerInterface $container*/) {
                    return $this->newInstanceWithoutConstructor();
                },
                $this
            );

        } elseif (true === $this->isInstantiable()) {

            /**
             * Create the new service instance using the constructor
             */
            $resolver = \Closure::bind(
                function (ContainerInterface $container) use ($args) {
                    
                    if (empty($args)) {
                        $parameters = [];
                        /**
                         * @var ParameterInterface $parameter
                         * Resolve parameters
                         */
                        foreach ($this->getParameters() as $parameter) {
                            
                            $parameters[] = ($parameter->getResolver())($container);

                        }
                    } else {
                        /**
                         * Constructor arguments may be provided implicitly
                         * @TODO
                         */
                        $parameters = $args;
                    }
                    
                    /**
                     * Create the service instance
                     */
                    return $this->newInstance(...$parameters);
                },
                $this
            );

        } else {
            throw new InstantiateException(
                sprintf(
                    _('Class "%s" is not instatiable. Preference identifier: "%s".'),
                    $this->getClass(),
                    $this->getId()
                )
            );
        }

        return Resolver::withResolver($resolver);
    }

    /**
     * Iterate service constructor parameters
     * and instantiate Preference parameters
     *
     * @return \Generator<ParameterInterface> An Parameters
     */
    protected function getParameters(): \Generator
    {
        /**
         * @var \ReflectionParameter $reflectionParameter
         */
        foreach ($this->getConstructorParameters() as $reflectionParameter) {
            yield $this->createParameter($reflectionParameter);
        }
    }

    /**
     * Instantiate Preference parameter using the reflecion parameter
     *
     * @param \ReflectionParameter $reflectionParameter The reflection parameter
     * 
     * @return ParameterInterface The instantiated preference parameter
     */
    protected function createParameter(\ReflectionParameter $reflectionParameter): ParameterInterface
    {
        /**
         * Get parameter data frrom the configuration
         */
        $parameterData = $this->getConfigValue(
            DIConfigInterface::NODE_PARAMETERS,
            $reflectionParameter->getName()
        );
    
        if (null !== $parameterData && !is_array($parameterData) ) {
            throw new PreferenceException(
                sprintf(_('Parameter "%s" configuration is invalid'), $reflectionParameter->getName())
            );
        }
        
        /**
         * @var ParameterInterface $parameter
         */
        $parameter = Parameter::withConfig(
            $this->getConfig()
                ->withData(
                    /**
                     * Add parameter name to configuration
                     */
                    array_merge(
                        $parameterData ?? [],
                        [DIConfigInterface::NODE_NAME => $reflectionParameter->getName()]
                    )
                )
        );

        $parameter->setReflection($reflectionParameter);

        return $parameter;
    }
    
    /**
     * {@inheritDoc}
     */
    public function reset(): void
    {
        $this->config->reset();
        $this->service = null;
        $this->referer = null;
        $this->reflectionClass = null;
    }
   
}