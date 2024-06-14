<?php
namespace XTC\Container\DoblyLinked;

use XTC\Container\Factory\FactoryInterface;

use XTC\Container\DoblyLinked\Exception\ItemNotFoundException;


/**
 * Interface DoublyLinkedAggregateInterface
 *
 * This interface defines the contract for a doubly linked aggregate, which represents a collection of nodes
 * that are interconnected in both forward and backward directions. Each node in the list is itself a sublist,
 * having the same functionality as the parent list. So 
 *
 * @package XTC\Container\DoblyLinked
 */
interface DoublyLinkedAggregateInterface
{
    /**
     * Set the container factory.
     *
     * This method sets the container factory to be used for creating nodes in the list.
     *
     * @param FactoryInterface $containerFactory The container factory to set.
     * 
     * @return void
     */
    public function setContainerFactory(FactoryInterface $containerFactory);


    /**
     * @return string
     */
    public function uid(): string;

    /**
     * Create a new node with a reset state.
     *
     * This method creates a new node by cloning the current instance and resetting its state.
     * If a factory instance is set, it uses the factory to create the node's container,
     * otherwise, it stores the raw data directly.
     *
     * @param mixed $data The data for the node's container.
     * 
     * @return DoublyLinkedAggregateInterface The newly created node.
     */
    public function createNode($data): DoublyLinkedAggregateInterface;

    /**
     * Get the container of the current node.
     *
     * This method returns the container of the current node.
     * 
     * @return mixed The container of the current node.
     */
    public function get();

    /**
     * Set the container for the current node.
     *
     * This method sets the container for the current node to the provided data or container.
     * 
     * @param mixed $container_or_data The data or container to set for the node.
     * 
     * @return DoublyLinkedAggregateInterface The current node.
     */
    public function set($container_or_data): DoublyLinkedAggregateInterface;

    /**
     * Push data onto the end of the list.
     *
     * This method adds new nodes with the provided data to the end of the list.
     * 
     * @param mixed ...$data The data to be added to the list.
     * 
     * @return DoublyLinkedAggregateInterface The updated list.
     */
    public function push(...$data): DoublyLinkedAggregateInterface;

    /**
     * Pop data from the end of the list.
     *
     * This method removes and returns the last node from the list.
     * 
     * @return DoublyLinkedAggregateInterface The removed last node.
     * @throws ItemNotFoundException If the current node is the last node in the list.
     */
    public function pop(): DoublyLinkedAggregateInterface;

    /**
     * Insert data into the list.
     *
     * This method inserts new nodes before the current node in the list.
     *
     * @param mixed ...$data The data to insert.
     * 
     * @return DoublyLinkedAggregateInterface
     */
    public function insert(...$data): DoublyLinkedAggregateInterface;

    /**
     * Add data to the beginning of the list.
     *
     * This method adds new nodes to the beginning of the list.
     *
     * @param mixed ...$data The data to add.
     * 
     * @return DoublyLinkedAggregateInterface
     */
    public function unshift(...$data): DoublyLinkedAggregateInterface;

    /**
     * Remove and return data from the beginning of the list.
     *
     * This method removes and returns the data from the beginning of the list by detaching the current node.
     *
     * @return DoublyLinkedAggregateInterface The shifted node.
     */
    public function shift(): DoublyLinkedAggregateInterface;

    /**
     * Create a new list containing a subset of the current list.
     *
     * This method creates a new list containing a subset of the elements from the current list.
     * The subset can be specified by providing a positive integer count to retrieve elements
     * from the beginning of the list, or a negative integer count to retrieve elements from the end
     * of the list. The method also provides an option to keep or discard the original nodes in the
     * current list while creating the subset.
     *
     * @param int $count The number of elements to include in the subset.
     *                   A positive count retrieves elements from the beginning of the list,
     *                   while a negative count retrieves elements from the end of the list.
     * @param bool $keep_nodes (optional) Whether to keep or discard the original nodes in the current list.
     *                         Defaults to true, meaning the original nodes will be kept.
     * 
     * @return DoublyLinkedAggregateInterface A new list containing the subset of elements.
     */
    public function slice(int $count, bool $keep_nodes = true): DoublyLinkedAggregateInterface;

    /**
     * Check if the list has a prior node.
     *
     * @return bool
     */
    public function hasPrior(): bool;

    /**
     * Get the prior node.
     *
     * @return DoublyLinkedAggregateInterface
     * @throws ItemNotFoundException
     */
    public function prior(): DoublyLinkedAggregateInterface;
    protected function setPrior($prior): DoublyLinkedAggregateInterface;

    /**
     * Check if the list has a next node.
     *
     * @return bool
     */
    public function hasNext(): bool;

    public function setNext($next): DoublyLinkedAggregateInterface;

    /**
     * Get the next node.
     *
     * @return DoublyLinkedAggregateInterface
     * @throws ItemNotFoundException
     */
    public function next(): DoublyLinkedAggregateInterface;

    /**
     * Get the first node of the list.
     *
     * This method traverses the list backwards from the current node to find the first node,
     * which is the node without any prior nodes. It returns the first node of the list.
     *
     * @return DoublyLinkedAggregateInterface The first node of the list.
     */
    public function first(): DoublyLinkedAggregateInterface;

    /**
     * Get the last node of the list.
     *
     * This method traverses the list forward from the current node to find the last node,
     * which is the node without any next nodes. It returns the last node of the list.
     *
     * @return DoublyLinkedAggregateInterface The last node of the list.
     */
    public function last(): DoublyLinkedAggregateInterface;

    /**
     * Check if the current node is the last node of the list.
     *
     * @return bool
     */
    public function isLast(): bool;

    /**
     * Check if the current node is the first node of the list.
     *
     * @return bool
     */
    public function isFirst(): bool;

    /**
     * Filter the elements of the list using a callback function, iterating forward.
     *
     * This method applies the provided callback function to each element of the list,
     * iterating from the current node forward. If the callback returns false for an element,
     * that element is removed from the list. The method also applies the callback to the
     * current node and, if it returns false, removes the current node and returns the next
     * node as the head of the filtered sequence.
     *
     * @param callable $callback The callback function to apply to each element.
     *                           It should accept a single argument, the current node,
     *                           and return a boolean indicating whether to keep the element.
     * @return DoublyLinkedAggregateInterface The head of the filtered sequence.
     * @throws ItemNotFoundException If no matching element is found and the current node
     *                                is the last node in the list.
     */
    public function filterForwardCallback(callable $callback);

    /**
     * Detach the current node from the list.
     *
     * This method removes the current node from the doubly linked list. It updates the pointers
     * of the previous and next nodes to bypass the current node. After detachment, the current
     * node loses its connections to the list, setting its prior and next pointers to null.
     *
     * @return DoublyLinkedAggregateInterface The current node after detachment.
     */
    public function detach(): DoublyLinkedAggregateInterface;

    /**
     * Count the nodes in the list.
     *
     * This method counts the number of nodes in the list by iterating through the list
     * starting from the first node and counting each node encountered.
     *
     * @return int The number of nodes in the list.
     */
    public function count(): int;

    /**
     * Count the nodes in the list forward.
     *
     * This method counts the number of nodes in the list by iterating through the list
     * starting from the current node and counting each node encountered until the end of the list.
     *
     * @return int The number of nodes in the list forward.
     */
    public function countForward(): int;

    /**
     * Count the nodes in the list backward.
     *
     * This method counts the number of nodes in the list by iterating through the list
     * starting from the current node and moving backward until reaching the beginning of the list.
     *
     * @return int The number of nodes in the list backward.
     */
    public function countBackward(): int;

    /**
     * Reset the list to its initial state.
     *
     * This method resets the list by removing all connections to prior and next nodes
     * and clearing the container data.
     *
     * @return DoublyLinkedAggregateInterface The list in its initial state.
     */
    public function reset(): DoublyLinkedAggregateInterface;

    /**
     * Destroy the current node.
     *
     * This method detaches the current node from the list and resets it to its initial state.
     * It removes connections to prior and next nodes and clears the container data.
     *
     * @return void
     */
    public function destroy(): void;

    /**
     * Destroy all nodes in the list.
     *
     * This method destroys all nodes in the list by first destroying nodes
     * forward from the current node, then destroying nodes backward from the current node,
     * and finally destroying the current node itself.
     *
     * @return void
     */
    public function destroyAll(): void;

    /**
     * Destroy all nodes forward from the current node.
     *
     * This method destroys all nodes in the list that are forward from the current node.
     *
     * @return void
     */
    public function destroyForward(): void;

    /**
     * Destroy all nodes backward from the current node.
     *
     * This method destroys all nodes in the list that are backward from the current node.
     *
     * @return void
     */
    public function destroyBackward(): void;

    /**
     * Get an iterator for the container.
     *
     * This method returns an iterator that iterates over the nodes in the list and yields their contents.
     *
     * @return \Traversable An iterator for the container.
     */
    public function getContainerIterator(): \Traversable;

    /**
     * Get an iterator for the list.
     *
     * This method returns an iterator that iterates over the nodes in the list, starting from the current node,
     * and yields each node.
     *
     * @return \Traversable An iterator for the list.
     */
    public function getIterator(): \Traversable;

    /**
     * Convert the list to a string.
     *
     * @return string
     */
    public function __toString(): string;
}
