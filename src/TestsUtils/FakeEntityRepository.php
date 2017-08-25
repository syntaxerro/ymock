<?php

namespace SyntaxErro\YMock\TestsUtils;

class FakeEntityRepository
{
    /**
     * @var string
     */
    private $repositoryName;

    /**
     * FakeEntityRepository constructor.
     * @param string $repositoryName
     */
    public function __construct($repositoryName)
    {
        $this->repositoryName = $repositoryName;
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return [];
    }

    /**
     * @param $id
     * @return null
     */
    public function find($id)
    {
        return $id ? null : null;
    }
}