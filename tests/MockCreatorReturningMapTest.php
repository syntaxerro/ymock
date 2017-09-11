<?php

namespace SyntaxErro\Tests\YMock;

use SyntaxErro\YMock\TestsUtils\TestUtils;

class MockCreatorReturningMapTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatingMocksWithReturningArrayMapFromYml()
    {
        $mocks = TestUtils::configureMockCreatorWithConfigurationPath($this, __DIR__.'/Resources/returning_array_map_configuration.yml');
        $pdo = $mocks->get('ArrayMap___bad_query_database_connection');

        $this->assertInstanceOf(\PDO::class, $pdo);
        $execResultUsers = $pdo->exec('SELECT * FROM users');
        $execResultConfig = $pdo->exec('SELECT * FROM configuration');
        $this->assertEquals('users_result', $execResultUsers);
        $this->assertEquals('configuration_result', $execResultConfig);
    }
}