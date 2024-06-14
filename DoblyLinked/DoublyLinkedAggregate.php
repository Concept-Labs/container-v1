<?php
namespace XTC\Container\DoblyLinked;

use IteratorAggregate;
use XTC\Container\DoblyLinked\Exception\ItemNotFoundException;
use XTC\Container\Factory\FactoryInterface;

class DoublyLinkedAggregate implements DoublyLinkedAggregateInterface, IteratorAggregate
{
    protected ?string $uid = null;
    protected ?DoublyLinkedAggregateInterface $next = null;
    protected ?DoublyLinkedAggregateInterface $prior = null;


    protected $containerFactory = null;
    protected $container = null;

    /**
     * {@inheritDoc}
     */
    public function setContainerFactory(FactoryInterface $containerFactory): void
    {
        $this->containerFactory = $containerFactory;
    }

    /**
     * {@inheritDoc}
     */
    protected function getContainerFactory()
    {
        return $this->containerFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function createNode($data): DoublyLinkedAggregateInterface
    {
        /**
         * Create a node as resetted self clone
         */
        $node = clone $this;//new static($this->getContainerFactory());
        $node->reset();

        /**
         * Factory a node container if factory instance was set
         * Else store a raw data
         */
        $node->container = $this->getContainerFactory() instanceof FactoryInterface 
            ? (static function ($factory, $data) {
                    return ($factory)($data);
                })($this->getContainerFactory(), $data)
            : $data;
        
        return $node;
    }

    /**
     * {@inheritDoc}
     */
    public function uid(): string
    {
        return $this->uid;
    }

    /**
     * {@inheritDoc}
     */
    public function get()
    {
        return $this->container;
    }

    /**
     * {@inheritDoc}
     */
    public function set($container_or_data): DoublyLinkedAggregateInterface
    {
        $this->container = $container_or_data;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function push(...$data): DoublyLinkedAggregateInterface
    {
        foreach ($data as $nodeData) {
            // Create a new node with the given data
            $node = $this->createNode($nodeData);
            
            // Set the new node as the next node of the current last node,
            // and set the current last node as the prior node of the new node
            $node->setPrior(
                $this->last()
                    ->setNext($node)
            );
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function pop(): DoublyLinkedAggregateInterface
    {
        if ($this->isLast()) {
            throw new ItemNotFoundException('Current item is the last');
        }

        return $this->last()
            ->detach();
    }

    /**
     * {@inheritDoc}
     */
    public function insert(...$data): DoublyLinkedAggregateInterface
    {
        // Iterate over the data in reverse order for proper insertion sequence
        // So the first inserted element will be inserted before the next one
        foreach (array_reverse($data) as $nodeData) {        

            // Create a new node
            $node = $this->createNode($nodeData);

            // If the current node has a next node, set the next node for the new node
            if (true === $this->hasNext()) {
                $node->setNext(
                    $this->next()->setPrior($node)
                );
            }

            // Set the next node for the current node as the new node
            $this->setNext(
                $node->setPrior($this)
            );
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function unshift(...$data): DoublyLinkedAggregateInterface
    {
        // Iterate over the data
        foreach ($data as $nodeData) {
            // Create a new node
            $node = $this->createNode($nodeData);
            
            // If the current node has a prior node, set the prior node for the new node
            if (true === $this->hasPrior()) {
                $node->setPrior(
                    $this->prior()->setNext($node)
                );
            }
            
            // Set the prior node for the current node as the new node
            $this->setPrior($node->setNext($this));
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    
    public function shift(): DoublyLinkedAggregateInterface
    {
        // Detach the current node from the list and return the detached node
        return $this->detach();
    }

    /**
     * {@inheritDoc}
     */
    public function slice(int $count, bool $keep_nodes = true): DoublyLinkedAggregateInterface
    {
        // Create a new list to store the subset
        $slice = clone $this;
        $slice->reset();

        // Initialize the current node
        $node = $this;

        // Check if the count is positive or negative
        if ($count >= 0) {
            // Positive count: retrieve elements from the beginning of the list
            while (true === $node->hasNext() && $count > 0) {
                // Move to the next node
                $next = $node->next();
                // Add the current node's data to the slice
                $slice->push($node->get());
                // Optionally destroy the current node
                if (true === $keep_nodes) {
                    $node->destroy();
                }
                // Move to the next node and decrement the count
                $node = $next;
                $count--;
            }
        } else {
            // Negative count: retrieve elements from the end of the list
            while (true === $node->hasPrior() && $count < 0) {
                // Move to the prior node
                $prior = $node->prior();
                // Add the current node's data to the slice
                $slice->unshift($node->get());
                // Optionally destroy the current node
                if (true === $keep_nodes) {
                    $node->destroy();
                }
                // Move to the prior node and increment the count
                $node = $prior;
                $count++;
            }
        }

        // Return the new list containing the subset
        return $slice;
    }

    /**
     * {@inheritDoc}
     */
    public function hasPrior(): bool
    {
        return $this->prior instanceof DoublyLinkedAggregateInterface;
    }

    /**
     * {@inheritDoc}
     */
    protected function setPrior($prior): DoublyLinkedAggregateInterface
    {
        $this->prior = $prior;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function prior(): DoublyLinkedAggregateInterface
    {
        if ($this->hasPrior()) {
            return $this->prior;
        }
        throw new ItemNotFoundException('Prior item is not attached');
    }

    /**
     * {@inheritDoc}
     */
    public function hasNext(): bool
    {
        return $this->next instanceof DoublyLinkedAggregateInterface;
    }

    /**
     * {@inheritDoc}
     */
    protected function setNext($next): DoublyLinkedAggregateInterface
    {
        $this->next = $next;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function next(): DoublyLinkedAggregateInterface
    {
        if ($this->hasNext()) {
            return $this->next;
        }
        throw new ItemNotFoundException('Prior item is not attached');
    }

    /**
     * {@inheritDoc}
     */
    public function first(): DoublyLinkedAggregateInterface
    {
        // Initialize the prior node as the current node
        $prior = $this;
        
        // Traverse backwards until reaching the first node
        while (true === $prior->hasPrior()) {
            $prior = $prior->prior();
        }

        // Return the first node of the list
        return $prior;
    }

    
    /**
     * {@inheritDoc}
     */
    public function last(): DoublyLinkedAggregateInterface
    {
        // Initialize the next node as the current node
        $next = $this;
        
        // Traverse forward until reaching the last node
        while (true === $next->hasNext()) {
            $next = $next->next();
        }

        // Return the last node of the list
        return $next;
    }

    /**
     * {@inheritDoc}
     */
    public function isLast(): bool
    {
        return $this === $this->last();
    }
    
    /**
     * {@inheritDoc}
     */
    public function isFirst(): bool
    {
        return $this === $this->first();
    }

    /**
     * {@inheritDoc}
     */
    public function filterCallback(callable $callback): DoublyLinkedAggregateInterface
    {
        return $this->first()
            ->filterForwardCallback($callback);
    }

    /**
     * {@inheritDoc}
     */
    public function filterForwardCallback(callable $callback)
    {
        /**
         * Detach from context
         */
        $filter = static function($callback, $node) {
            return $callback($node);
        };

        $node = $this;
        while (true === $node->hasNext()) {
            $next = $node->next();
            if (true !== $filter($callback, $node)) {
                $node->destroy();
            };
            $node = $next;
        }
        
        /**
         * $this context remains as head of filtered sequence
         * Apply filter on $this and get the next node as the head if such is found
         */
        $remains = $this;
        if (true !== $callback($this)) {
            if (true !== $this->hasNext()) {
                $this->destroy();
                throw new ItemNotFoundException('No matching element found.');
            }
            $remains = $this->next();
            $this->destroy();
        }

        return $remains;
    }
   

    /**
     * {@inheritDoc}
     */
    public function detach(): DoublyLinkedAggregateInterface
    {
        if (true === $this->hasPrior()) {
            $this->prior()
                ->setNext($this->next());
        }
        if (true === $this->hasNext()) {
            $this->next()
                ->setPrior($this->prior());
        }

        $this
            ->setPrior(null)
            ->setNext(null);
        //php8
        // $this->prior()?->setNext($this->next());   
        // $this->next()?->setPrior($this->prior());

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        // Get the iterator for the list starting from the first node
        $iterator = $this->first()->getIterator();
        
        // Count the number of nodes using the iterator
        return iterator_count($iterator);
    }
    
    /**
     * {@inheritDoc}
     */
    public function countForward(): int
    {
        // Get the iterator for the list starting from the current node
        $iterator = $this->getIterator();
        
        // Count the number of nodes using the iterator
        return iterator_count($iterator);
    }

    /**
     * {@inheritDoc}
     */
    public function countBackward(): int
    {
        // Initialize the count to 0
        $count = 0;
        
        // Start from the current node and move backward until reaching the beginning of the list
        $prior = $this;
        do {
            // Increment the count for each node encountered
            $count++;
            
            // Move to the prior node
            $prior = $prior->prior();
            
        } while (true === $prior->hasPrior());

        return $count;
    }

    /**
     * {@inheritDoc}
     */
    public function reset(): DoublyLinkedAggregateInterface
    {
        // Remove connections to prior and next nodes
        $this
            ->setNext(null)
            ->setPrior(null)
            ->set(null);
        
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function destroy(): void
    {
        // Detach the current node from the list
        $this->detach();
        // Reset the node to its initial state
        $this->reset();
    }

    /**
     * {@inheritDoc}
     */    
    public function destroyAll(): void
    {
        // Destroy all nodes forward from the current node
        $this->destroyForward();
        // Destroy all nodes backward from the current node
        $this->destroyBackward();
        // Destroy the current node
        $this->destroy();
    }

    /**
     * {@inheritDoc}
     */
    public function destroyForward(): void
    {
        $next = $this;
        while (true === $next->hasNext()) {
            $next = $next->next();
            $next->destroy();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function destroyBackward(): void
    {
        $prior = $this;
        while (true === $prior->hasPrior()) {
            $prior = $prior->prior();
            $prior->destroy();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getContainerIterator(): \Traversable
    {
        foreach($this as $node) {
            yield $node->get();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        /**
         * Begginning from current item
         */
        $item = $this;//->first();
        do {
            yield $item;
        } while (true === $item->hasNext() && $item = $item->next());
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        
        return join('=>', iterator_to_array($this->getContainerIterator()));
    }

}