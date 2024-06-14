<?php
namespace XTC\Container;

use XTC\Container\Attribute\Handler;
use XTC\Container\Attribute\HandlerInterface;

trait ContainerAttributesTrait
{
    /**
     * @var Handler|null The attributes handler
     */
    private ?HandlerInterface $attributesHandler = null;

    /**
     * @return HandlerInterface
     */
    private function getAttributesHandler(): HandlerInterface
    {
        if (null === $this->attributesHandler) {
            $this->attributesHandler = new Handler();
        }
        return $this->attributesHandler;
    }
}