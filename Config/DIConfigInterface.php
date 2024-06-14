<?php
namespace XTC\Container\Config;

use XTC\Config\ConfigInterface;

interface DIConfigInterface extends ConfigInterface
{
    const NODE_ID = 'id';
    //const NODE_CONTAINER_CONFIG = 'container-config';
    const NODE_DI_CONFIG = 'di';
    const NODE_PREFERENCE = 'preference';
    const NODE_CLASS = 'class';
    const NODE_REFERENCE = 'reference';
    const NODE_POLICY = 'policy';
    const NODE_USE_CLASS_ALIAS = 'use-class-alias';
    const NODE_SINGLETON = 'singleton';

    const NODE_PARAMETERS = 'parameters';
    /**
     * Configuration path for parameter name.
     */
    const NODE_NAME = 'name';
    /**
     * Configuration path for parameter type.
     */
    const NODE_TYPE = 'type';
    /**
     * Configuration path for parameter value.
     */
    const NODE_VALUE = 'value';
    /**
     * Type constant for object parameters.
     */
    const NODE_TYPE_OBJECT = 'object';
    

    const INLINE_DI_CONFIG_CONSTANT = 'DI_CONFIG_INLINE';
    const INLINE_DI_CONFIG_FILE_CONSTANT = 'DI_CONFIG_FILE';
    const INLINE_DI_CONFIG_CLASS_CONSTANT = 'DI_CONFIG_CLASS';

    /**
     * Get the DI configuration
     * @return array
     */
    //public function getConfig(): array;
}