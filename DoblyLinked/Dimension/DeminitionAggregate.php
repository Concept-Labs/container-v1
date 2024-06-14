<?php
namespace XTC\Container\DoblyLinked\Dimension;

use XTC\Container\DoblyLinked\DoublyLinkedAggregate;
use XTC\Container\DoblyLinked\DoublyLinkedAggregateInterface;

class DimensionAggregate extends DoublyLinkedAggregate implements DimensionAggregateInterface
{
    protected ?DoublyLinkedAggregateInterface $dimension = null;

    public function addDimension(DoublyLinkedAggregateInterface $item): DoublyLinkedAggregateInterface
    {
        $this->dimension = $item;
        return $this;
    }

    public function getDimension(): DoublyLinkedAggregateInterface
    {
        return $this->dimension;
    }
}