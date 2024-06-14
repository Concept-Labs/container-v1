<?php
namespace XTC\Container\Preference\Parameter;

use Psr\Container\ContainerInterface;
use XTC\Config\ConfigInterface;
use Xtc\Config\ConfigAwareInterface;
use Xtc\Config\ConfigAwareTrait;
use XTC\Container\Config\DIConfigInterface;
use XTC\Container\Preference\Parameter\Exception\ParameterException;
use XTC\Container\Resolver\Resolver;
use XTC\Container\Resolver\ResolverInterface;

/**
 * The preference parameter implementation
 * The parameter represents the mix of the service reflection parameter 
 * And the preference parameter configuration
 * Finally do resolve the service parameter with the appropriate di instance
 */
class Parameter implements ParameterInterface, ConfigAwareInterface
{
    use ConfigAwareTrait;

    /**
     * @var \ReflectionParameter The parameter reflection
     */
    protected ?\ReflectionParameter $reflection = null;

    /**
     * The callable resolver
     * First argument must be instance of Psr\Container\ContainerInterface
     *
     * @var ResolverInterface
     */
    protected ?ResolverInterface $resolver = null;

    /**
     * Private constructor
     */
    private function __construct()
    {
        /**
         * Avoid direct instantiation
         */
    }

    /**
     * {@inheritDoc}
     */
    static public function withConfig(ConfigInterface $config): ParameterInterface
    {
        $parameter = new static;
        $parameter->setConfig($config);

        return $parameter;
    }


    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->getConfigValue(DIConfigInterface::NODE_NAME);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return $this->getConfigValue(DIConfigInterface::NODE_TYPE);
    }
    
    /**
     * {@inheritDoc}
     */
    public function hasValue(): bool
    {
        return $this->config->has(DIConfigInterface::NODE_VALUE);
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        return $this->getConfigValue(DIConfigInterface::NODE_VALUE);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getReflection()
    {
        return $this->reflection;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setReflection(\ReflectionParameter $reflection): void
    {
        $this->reflection = $reflection;
    }

    /**
     * {@inheritDoc}
     */
    public function getResolver(): ResolverInterface
    {
        $this->assert();

        /**
         * Check the parameter DI configuration first
         */
        if ($this->hasValue() && DIConfigInterface::NODE_TYPE_OBJECT == $this->getType()) {
            /**
             * If defined type is object than resolve throw container
             */
            $resolver = \Closure::bind(
                fn (ContainerInterface $container) => $container->get($this->getValue()),
                $this
            );
            
        } elseif ($this->hasValue()) {
        
            /**
             * If a defined type is atomic than use the value from a configuration
             */
            $resolver =\Closure::bind(
                fn (ContainerInterface $container) => $this->getValue(),
                $this
            );

        } 
        /**
         * The parameter has no DI configuration...proceed with reflection
         */
        elseif ($this->getReflection()->isVariadic()
            || $this->getReflection()->getType() instanceof \ReflectionUnionType 
            || $this->getReflection()->getType() instanceof \ReflectionIntersectionType
            
        ) {
            /**
             * Parameter is a union type
             * @TODO:VG php8 union, intersection, variadic types
             */
            throw new ParameterException(
                _('DI for parameters with either union or intersection or variadic types has no support yet:'.$this->getReflection()->getType())
            );
        } elseif ($this->getReflection()->isOptional() && $this->getReflection()->isDefaultValueAvailable()) {
            /**
             * Parameter has default value, so use it
             */
            $resolver = \Closure::bind(
                fn (ContainerInterface $container) => $this->getReflection()->getDefaultValue(),
                $this
            );

        } elseif ($this->getReflection()->getType() instanceof \ReflectionNamedType) {
            /**
             * @var \ReflectionNamedType $type
             */
            $type = $this->getReflection()
                ->getType();
            /**
             * Parameter declaration is a class
             * Resolve throw the container
             */
            $resolver = \Closure::bind(
                fn (ContainerInterface $container) => $container->get($type->getName()),
                $this
            );

        } 

        if (null === $resolver) {
            throw new ParameterException(
                sprintf(
                    _('Cant resolve the  "%s": %s::%s'),
                    $this->getName(),
                    $this->getReflection()->getDeclaringClass()->getName(),
                    $this->getReflection()->getName()
                )
            );
        }
        return $this->resolver = Resolver::withResolver($resolver);
    }

    /**
     * Assert data
     *
     * @return void
     * 
     * @throws ParameterException If parameter data is not valid
     */
    protected function assert(): void
    {
        if (null === $this->getName()) {
            throw new ParameterException('Failed to assert the parameter data. The name must be provided.');
        }

        if (null === $this->reflection) {
            throw new ParameterException(_('Reflection not found. Use setReflection()'));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function reset(): void
    {
        $this->config->reset();
    }
}