<?php

namespace SyntaxErro\YMock\TestsUtils;

class FakeEntityManager
{
    /**
     * @param object $entity
     * @return bool
     */
    public function persist($entity)
    {
        return (bool)$entity;
    }

    /**
     * @param $repositoryName
     * @return FakeEntityRepository
     */
    public function getRepository($repositoryName)
    {
        return new FakeEntityRepository($repositoryName);
    }
}