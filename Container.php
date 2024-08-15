<?php
namespace XTC\Container;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

use XTC\Config\ConfigInterface;
use XTC\Config\ConfigAwareInterface;
use XTC\Config\ConfigAwareTrait;
use XTC\Container\Config\DIConfigInterface;
use XTC\Container\Resolver\ResolverInterface;
use XTC\Container\ServiceRepository\ServiceRepositoryAwareInterface;
use XTC\Container\ServiceRepository\ServiceRepositoryAwareTrait;
use XTC\Container\Preference\PreferenceInterface;

use XTC\Container\Exception\InvalidArgumentException;
use XTC\Container\Exception\NotFoundException;
use XTC\Container\ServiceRepository\ServiceRepository;
use XTC\Container\ServiceRepository\ServiceRepositoryInterface;
use XTC\EventDispatcher\EventDispatcherAwareTrait;
use XTC\EventDispatcher\ListenerProviderAwareTrait;
use XTC\Debug\DebuggerAwareTrait;
use XTC\EventDispatcher\EventDispatcherAwareInterface;
use XTC\EventDispatcher\ListenerProviderAwareInterface;

/**
 * PSR Container
 */
class Container 
    implements 
        ContainerInterface,
        ConfigAwareInterface,
        ServiceRepositoryAwareInterface,
        EventDispatcherAwareInterface,
        ListenerProviderAwareInterface,
        LoggerAwareInterface
{
    use ContainerStateTrait;
    use ContainerCallStackTrait;
    use ContainerPreferenceTrait;
    use ContainerAttributesTrait;

    use ConfigAwareTrait;
    use ServiceRepositoryAwareTrait;
    use LoggerAwareTrait;
    use EventDispatcherAwareTrait;
    use ListenerProviderAwareTrait;
    use DebuggerAwareTrait;

    /**
     * The constructor
     * 
     * @param ConfigInterface $config The configuration. Mandatory
     */
    public function __construct()
    {
        $this->init();
    }

    protected function init()
    {
        /**
         * Init the configuration
         */
        //$this->setConfig($config);

        /**
         * Create the the service repository
         * Service repository impossible to be the singleton instance
         * And can not be attached manually as well to avoid recursion
         */
        $this->serviceRepository = new ServiceRepository();
        //$this->create(ServiceRepositoryInterface::class);

        /**
         * Attach self as very first service
         */
        $this->attach(\Psr\Container\ContainerInterface::class, $this);
    }

    /**
     * {@inheritDoc}
     */
    public function attach(string $id, $service): void
    {
        $this->getServiceRepository()->attach($id, $service);
    }
    
     /**
     * {@inheritDoc}
     */
    public function has(string $id): bool
    {
        return $this->getServiceRepository()->has($id);
    }

    /**
     * {@inheritDoc}
     * @todo:vg: figure out arguments
     */
    public function get(string $id, ...$args): object
    {

        if (true === $this->has($id)) {
            
            $service = $this->getServiceRepository()->get($id);
            
        } else {
            
            $service = $this->create($id, ...$args);
        }

        if (null === $service) {
            throw new NotFoundException(
                sprintf(_('Service "%s" not found'), $id)
            );
        }

        return $service;
    }

    /**
     * {@inheritDoc}
     */
    public function create(string $id, ...$args): object
    {
        $f = new \XTC\Di\Factory\DiFactory();
        $f = $f->withContainer($this)
            ->withConfig($this->getConfig())
            ->withServiceId($id)
            ->withParameters(...$args);
        
        $s = $f->create();

        if ($this->callStackContains($id)) {
            /**
             * Avoid the recursion during resolving a service
             */
            throw new InvalidArgumentException(
                sprintf(_('Cannot instantiate "%s" recursively'), $id)
            );
        }

        $this->callStackPush($id);

        //try {
            $service = null;
            /**
             * Resolve a preference (config) for a service looking for
             * 
             * @var PreferenceInterface $preference
             */
            $preference = $this->getPreference($id);

            /**
             * @var ResolverInterface $resolver
             */
            $resolver = $preference->getResolver(...$args);

            /**
             * Get the service instance
             */
            $service = ($resolver)($this);
            
            $this->preferenceLifeCycle($preference);

        // } catch (\Throwable $e) {
        //     throw new NotFoundException(
        //         sprintf(
        //             _("Unable to create the service \"%s\": %s \n%s"),
        //             $id,
        //             $e->getMessage(),
        //             $e->getTraceAsString()
        //         ),
        //         $e->getCode(),
        //         $e
        //     );
        // }

        $this->callStackPop($id);

        return $service;
    }

    /**
     * @deprecated
     * 
     * {@inheritDoc}
     */
    public function new(string $id, ...$args): object
    {
        return $this->create($id, ...$args);
    }

    /**
     * Reset the container instance
     *
     * @return void
     */
    public function reset()
    {
        $this->config->reset();
        $this->getServiceRepository()->reset();
    }
}