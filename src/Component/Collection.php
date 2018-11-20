<?php
namespace Component;

use Model\Contract\HasId;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of Collection
 *
 * @author arslanhajdarevic
 */
abstract class Collection implements \Iterator, \ArrayAccess, \Countable {
    
    abstract protected function buildEntity(): HasId;
    
    private $pool = [];
    private $indexed = [];
    private $map = [];
    
    private $position = 0;
    private $total = 0;
    private $offset = 0;
    private $limit = 10;
    
    
    /**
     * Add new domain entity, that is constructed using array as values. Each array key
     * will be attempted top match with entity's setter method and provided with
     * the respective array value. It returns the newly created entity.
     *
     * @param array $parameters
     *
     * @return HasId
     */
    public function addBlueprint(array $parameters)
    {
        $instance = $this->buildEntity();
        $this->populateEntity($instance, $parameters);
        $this->addEntity($instance);
        return $instance;
    }
    
    
    /** code that does the actual population of data from the given array in blueprint */
    protected function populateEntity($instance, array $parameters)
    {
        foreach ($parameters as $key => $value) {
            $method = 'set' . str_replace('_', '', $key);
            if (method_exists($instance, $method)) {
                $instance->{$method}($value);
            }
        }
    }
    
    
    /**
     * Method for adding already existing domain entity to the collection.
     *
     * @param HasId $entity
     */
    public function addEntity(HasId $entity, $key = null)
    {
        if (is_null($key) === false) {
            $this->replaceEntity($entity, $key);
            return;
        }
        $entityId = $entity->getId();
        $this->pool[] = $entity;
        $this->indexed[$entityId] = $entity;
        $this->map[$entityId] = $this->retrieveLastPoolKey();
    }
    
    
    public function removeEntityById($key)
    {
        unset($this->pool[$this->map[$key]]);
        $this->removeIndexEntry($key);
        $this->pool = array_values($this->pool);
    }
    
    
    public function getFirstEntity()
    {
        $key = $this->retrieveFirstPoolKey();
        return $this->pool[$key];
    }
    
    
    public function getLastEntity()
    {
        $key = $this->retrieveLastPoolKey();
        return $this->pool[$key];
    }
    
    
    private function replaceEntity(HasId $entity, $key)
    {
        if (isset($this->pool[$key])) {
            $this->removeIndexEntry($this->pool[$key]->getId());
        }
        $entityId = $entity->getId();
        $this->pool[$key] = $entity;
        $this->indexed[$entityId] = $entity;
        $this->map[$entityId] = $key;
    }
    
    
    private function removeIndexEntry($key)
    {
        unset($this->indexed[$key]);
        unset($this->map[$key]);
    }
    
    
    private function retrieveFirstPoolKey()
    {
        reset($this->pool);
        return key($this->pool);
    }
    
    
    private function retrieveLastPoolKey()
    {
        end($this->pool);
        return key($this->pool);
    }
    
    
    /**
     * Method for getting an ordered list of IDs for items in the collection.
     *
     * @return array
     */
    public function getIds()
    {
        $keys = array_keys($this->indexed);
        sort($keys);
        return $keys;
    }
    
    
    public function findEntityById(int $key)
    {
        if (array_key_exists($key, $this->indexed)) {
            return $this->indexed[$key];
        }
        return null;
    }
    
    
    /**
     * Replaces all of the domain entities with a content of some other collection
     *
     * @param Collection $replacement
     */
    public function replaceWith(Collection $replacement)
    {
        $this->pool = [];
        $this->map = [];
        $this->indexed = [];
        foreach ($replacement as $entity) {
            $this->addEntity($entity);
        }
    }
    
    
    /**
     * Removes an entity from collection.
     *
     * @param HasId $entity
     */
    public function removeEntity(HasId $entity)
    {
        $key = $entity->getId();
        if ($key !== null) {
            unset($this->pool[$this->map[$key]]);
            $this->removeIndexEntry($key);
        }
    }
    
    
    /**
     * Removes all of the content of the collection and resets it to pristine state.
     */
    public function purge()
    {
        $this->pool = [];
        $this->indexed = [];
        $this->map = [];
        $this->position = 0;
    }
    
    // implementing Countable
    public function count()
    {
        return count($this->pool);
    }
    
    
    // implementing Iterator
    public function rewind()
    {
        $this->position = 0;
    }
    
    
    public function current()
    {
        return $this->pool[$this->position];
    }
    
    
    /**
     * @codeCoverageIgnore
     */
    public function key()
    {
        return $this->position;
    }
    
    
    public function next()
    {
        ++$this->position;
    }
    
    
    public function valid()
    {
        return isset($this->pool[$this->position]);
    }
    
    
    // implementing ArrayAccess
    public function offsetSet($offset, $value)
    {
        $this->addEntity($value, $offset);
    }
    
    
    public function offsetExists($offset)
    {
        return isset($this->pool[$offset]);
    }
    
    
    public function offsetUnset($offset)
    {
        $this->removeEntity($this->pool[$offset]);
    }
    
    
    public function offsetGet($offset)
    {
        if (isset($this->pool[$offset])) {
            return $this->pool[$offset];
        }
        return null;
    }
    
    
    public function toArray(): array
    {
        return $this->pool;
    }
    
    
    public function setLimit(int $limit)
    {
        $this->limit = $limit;
    }
    
    
    public function getLimit(): int
    {
        return $this->limit;
    }
}
