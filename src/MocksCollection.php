<?php

namespace SyntaxErro\YMock;

use SyntaxErro\YMock\Exception\InaccessibleCollectionElementException;

class MocksCollection implements \Iterator
{
    /**
     * @var array
     */
    private $mocks = [];

    /**
     * @var int
     */
    private $index = 0;

    /**
     * MocksCollection constructor.
     * @param array $mocks
     */
    public function __construct(array $mocks = [])
    {
        $this->mocks = $mocks;
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return $this->mocks[$this->index];
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        return array_key_exists($this->index, $this->mocks);
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->mocks;
    }

    /**
     * @param array $mocks
     * @return MocksCollection
     */
    public function setMocks(array $mocks)
    {
        $this->mocks = $mocks;
        return $this;
    }

    /**
     * @param $mock
     * @param mixed $index
     * @return MocksCollection
     */
    public function addMock($mock, $index)
    {
        $this->mocks[$index] = $mock;
        return $this;
    }

    /**
     * @param $mock
     * @return MocksCollection
     */
    public function removeMock($mock)
    {
        foreach($this->mocks as &$inCollectionMock) {
            if($inCollectionMock == $mock) {
                unset($inCollectionMock);
            }
        }
        $this->mocks = array_values($this->mocks);
        return $this;
    }

    /**
     * @param $mockIndex
     * @return MocksCollection
     * @throws InaccessibleCollectionElementException
     */
    public function removeIndex($mockIndex)
    {
        if(!isset($this->mocks[$mockIndex])) {
            throw new InaccessibleCollectionElementException(
                sprintf('Cannot remove "%s" element from mocks collection, because does not exists!', $mockIndex)
            );
        }

        unset($this->mocks[$mockIndex]);
        $this->mocks = array_values($this->mocks);
        return $this;
    }

    /**
     * @param $mockIndex
     * @return mixed
     * @throws InaccessibleCollectionElementException
     */
    public function get($mockIndex)
    {
        if(!isset($this->mocks[$mockIndex])) {
            throw new InaccessibleCollectionElementException(
                sprintf('Cannot get "%s" element from mocks collection, because does not exists!', $mockIndex)
            );
        }

        return $this->mocks[$mockIndex];
    }

    /**
     * @return mixed
     * @throws InaccessibleCollectionElementException
     */
    public function first()
    {
        if(!$this->mocks) {
            throw new InaccessibleCollectionElementException(
                sprintf('Cannot get first element from mocks collection, because collection is empty!')
            );
        }

        return array_values($this->mocks)[0];
    }
}