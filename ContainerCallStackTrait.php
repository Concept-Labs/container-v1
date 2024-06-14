<?php
namespace XTC\Container;

use XTC\Container\Exception\InvalidArgumentException;

trait ContainerCallStackTrait
{
     /**
     * A call stack. Used to avoid recursion
     */
    protected array $callStack = [];

    /**
     * Check if call stack contains id
     * 
     * @return bool
     * @throws InvalidArgumentException
     */
    protected function callStackContains(string $id): bool
    {
        return in_array($id, $this->callStack);
    }

    /**
     * Push a service onto the stack
     * 
     * @return void
     */
    protected function callStackPush(string $id): void
    {
        array_push($this->callStack, $id);
    }

    /**
     * Clear call stack
     * 
     * @return string
     */
    protected function callStackPop(): string
    {
        return array_pop($this->callStack);
    }
}