<?php
namespace XTC\Container;

use XTC\Container\Config\DIConfigInterface;
use XTC\Container\Preference\Preference;
use XTC\Container\Preference\PreferenceInterface;

/**
 * PSR Container
 */
trait ContainerPreferenceTrait
{
    /**
     * Get the preference instance based on configuration
     *
     * @param string $id The preference identifier
     * 
     * @return PreferenceInterface
     */
    protected function getPreference(string $id): PreferenceInterface
    {
        
        $preferenceConfigData = $this->getConfigValue(
            DIConfigInterface::NODE_PREFERENCE,
            $id
        );
        
        /**
         * @var PreferenceInterface $preference The preference object
         */
        $preference = Preference::withConfig(
            $this->getConfig()
                ->withData(
                    array_merge(
                        /**
                         * Preference configuration may be absent
                         * so the id may be used as the class name
                         */
                        [
                            DIConfigInterface::NODE_ID => $id,
                            DIConfigInterface::NODE_CLASS => $id
                        ],
                        is_array($preferenceConfigData) ? $preferenceConfigData : []
                    )
                )
        );
        
        /**
         * A preference can refer to the another one
         */
        if (null !== $preference->getReference()) {

            $referer = $preference;

            $preference = $this->getPreference($preference->getReference());

            $preference->setReferer($referer);
        }

        $this->applyPreferenceConfig($preference);
        
        return  $preference;
    }


    /**
     * A preferernce may contain config with container addons/overrides
     * see config JSON:
     *  {
     *    <preference>: {
     *      "container-config":{...<config>...}
     *    }
     * }
     *
     * @param PreferenceInterface $preference The preference
     * 
     * @return void
     */
    protected function applyPreferenceConfig(PreferenceInterface $preference): void
    {
        if ($preference->hasDiConfig() 
            || $preference->hasInlineDiConfig() 
            || $preference->hasInlineDiConfigFile()
            || $preference->hasDiConfigClass()
            ) {
            /**
             * Save the current state of the container
             */
            $this->pushState();

            /**
             * Container configs by priority.
             * Earlier call has lower priority.
             */

            if($preference->hasDiConfig()) {
                /**
                 * Merge the global container config
                 */
                $this->getConfig()->mergeFrom($preference->getDiConfigData());
            }
            
            if($preference->hasInlineDiConfigFile()) {
                /**
                 * Merge the class inline container config from the file
                 */
                $this->getConfig()->mergeFrom($preference->getInlineDiConfigFileData());
            }

            if ($preference->hasDiConfigClass()) {
                /**
                 * Merge the class inline container config
                 */
                $this->getConfig()->mergeFrom($preference->getContainerConfigClassData());
            }
            
            if ($preference->hasInlineDiConfig()) {
                /**
                 * Merge the class inline container config
                 */
                $this->getConfig()->mergeFrom($preference->getInlineContainerConfigData());
            } 
        }
    }

    /**
     * Proceed preference`s lifecycle
     *
     * @param PreferenceInterface $preference The preference instance
     * 
     * @return void
     */
    protected function preferenceLifeCycle(PreferenceInterface $preference)
    {
        /**
         * Find an original preference by checking a referers chain
         */
        $finalPreference = $preference;

        /**
         * Get the service instance
         */
        $service = $finalPreference->getService();

        /**
         * Get a root referer
         */
        while ($preference->hasReferer()) {

            $preference = $preference->getReferer();

            /**
             * Restore a container state if the preference did override
             */
            if ($preference->hasDiConfig()) {
                $this->popState();
            }
        }

        /**
         * Make a class alias
         * As far as the preference ID may be not an existing class name
         * This avoid a constructor parameter type error
         * @TODO:VG generate the code with class/interface declaration instead, 
         * so IDE can resolve them ?
         */
        if ($finalPreference->getClass() !== $preference->getClass()) {
            if (true === $this->getConfigValue(
                DIConfigInterface::NODE_POLICY,
                DIConfigInterface::NODE_USE_CLASS_ALIAS
            )
            ) {
                if (!class_exists($preference->getId())) {
                    /**
                     * @todo: vg: warning class already exists
                     */
                    @class_alias($finalPreference->getClass(), $preference->getId());
                }
            }

        }

        /**
         * If the preference config has addons/overrides than 
         * restore a previous state of the container`s configuration
         */
        if ($finalPreference->hasDiConfig()) {
            $this->popState();
        }

        /**
         * Handle the service attributes
         */
        // $this->getAttributesHandler()
        //     ->handle($service, $this);

        /**
         * If the preference configured as singleton than register the service 
         */
        if ($finalPreference->isSingleton()) {
            $this->attach($preference->getId(), $service);
        }
    }
}