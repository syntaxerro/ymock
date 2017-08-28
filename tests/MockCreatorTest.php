<?php

namespace SyntaxErro\Tests\YMock;

use SyntaxErro\YMock\Configuration\Configuration;
use SyntaxErro\YMock\Configuration\RecursiveConfiguration;
use SyntaxErro\YMock\MockCreator;
use SyntaxErro\YMock\TestsUtils\FakeServiceContainer;
use SyntaxErro\YMock\TestsUtils\FakeEntityRepository;
use SyntaxErro\YMock\TestsUtils\FakeEntity;

class MockCreatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Any number allow
     */
    const TESTING_ENTITY_ID = 20;

    /**
     * Testing create chain of mocks from array
     */
    public function testCreatingMocksWithArrayConfiguration()
    {
        $mockCreator = new MockCreator($this);

        $mockCreator->setConfiguration(
            $this->createConfigurationForMockWithReturnedArrayByOneOfConfiguredMethod()
        );

        $mocks = $mockCreator->getMocks();
        $serviceContainer = $mocks->get('service_container');
        $this->assertInstanceOf(FakeServiceContainer::class, $serviceContainer);
        $this->assertTrue(method_exists($serviceContainer, 'getServices'));

        $services = $serviceContainer->getServices();
        $this->assertTrue(is_array($services));
        $this->assertArrayHasKey('entity_repository', $services);

        /** @var FakeEntityRepository $entityRepository */
        $entityRepository = $services['entity_repository'];
        $this->assertInstanceOf(FakeEntityRepository::class, $entityRepository);
        $this->assertTrue(method_exists($entityRepository, 'find'));

        /** @var FakeEntity $entity */
        $entity = $entityRepository->find(0);
        $this->assertInstanceOf(FakeEntity::class, $entity);

        $id = $entity->getId();
        $this->assertNotNull($id);
        $this->assertEquals(self::TESTING_ENTITY_ID, $entity->getId());
    }

    /**
     * @return RecursiveConfiguration
     */
    private function createConfigurationForMockWithReturnedArrayByOneOfConfiguredMethod()
    {
        return new RecursiveConfiguration([
            'service_container' => [
                'class' => FakeServiceContainer::class,
                'disable_original_constructor' => true,
                'methods' => [
                    'getServices' => [
                        'entity_repository' => [
                            'class' => FakeEntityRepository::class,
                            'constructor_args' => ['\AppBundle\Repository\UserRepository'],
                            'methods' => [
                                'find' => [
                                    'class' => FakeEntity::class,
                                    'methods' => [
                                        'getId' => self::TESTING_ENTITY_ID
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * @throws \SyntaxErro\YMock\Exception\InaccessibleCollectionElementException
     */
    public function testCreatingMocksFromYml()
    {
        $mockCreator = new MockCreator($this);

        $configuration = new Configuration(__DIR__.'/Resources/sample_configuration.yml');
        $mockCreator->setConfiguration($configuration);

        $mocks = $mockCreator->getMocks();
        $badQueryDatabaseConnection = $mocks->get('bad_query_database_connection');

        $this->assertNotNull($badQueryDatabaseConnection);
        $this->assertInstanceOf(\PDO::class, $badQueryDatabaseConnection);

        $badQueryResult = $badQueryDatabaseConnection->query('SELECT * FROM users');
        $this->assertFalse($badQueryResult);

        /** @var \PDO $validQueryDatabaseConnection */
        $validQueryDatabaseConnection = $mocks->get('valid_query_database_connection');
        $this->assertNotNull($badQueryDatabaseConnection);
        $this->assertInstanceOf(\PDO::class, $validQueryDatabaseConnection);

        /** @var \PDOStatement $validQueryResultStatement */
        $validQueryResultStatement = $validQueryDatabaseConnection->query('SELECT * FROM users');
        $this->assertNotNull($validQueryResultStatement);
        $this->assertInstanceOf(\PDOStatement::class, $validQueryResultStatement);

        $validQueryResult = $validQueryResultStatement->fetchAll();
        $this->assertTrue(is_array($validQueryResult));
        $this->assertEquals([0, 1, 2], $validQueryResult);
    }
}