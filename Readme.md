# Create PHPUnit mocks with YML
----
### Installation
```bash
composer require syntaxerro/ymock 0.1.x-dev
```
----
### Configuration
```yml
bad_query_database_connection:
  class: "\\PDO"
  disable_original_constructor: true
  methods:
    query: false
```
----
### Testing
```bash
./run.sh
```
----
### Overview
##### Classical method creating of mock
```php
class CartServiceTest extends \PHPUnit_Framework_TestCase
{
    // provider etc.
    public function addProductTest($product, $expectations)
    {
        $cart = new CartService(
            $this->createMockOfDatabase()
        );

        // some test and assertions
    }

    private function createMockOfDatabase()
    {
        $values = [0, 1, 2];

        $statement = $this->getMockBuilder(\PDOStatement::class)
            ->disableOriginalConstructor()
            ->getMock();
        $statement->method('fetchAll')->willReturn($values);

        $pdo = $this->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

        $pdo->method('query')->willReturn($statement);

        return $pdo;
    }
}
```

##### Creating the same mock with the YMock
```php
class CartServiceTest extends \PHPUnit_Framework_TestCase
{
    // provider etc.
    public function addProductTest($product, $expectations)
    {
        $ymock = new \SyntaxErro\YMock\YMock($this, './path/to/mocks.yml');
        $mocks = $ymock->getMocks();

        $cart = new CartService(
            $mocks->get('valid_query_database_connection')
        );

        // some test and assertions
    }
}
```

```yml
valid_query_database_connection:
  class: "\\PDO"
  disable_original_constructor: true
  methods:
    query:
      class: "\\PDOStatement"
      disable_original_constructor: true
      methods:
        fetchAll: [0, 1, 2]
```
----

##### Features

- Creating mocks suite per test in one YAML file
- Easy creating chains of mocks
- Configuring returned values (supported arrays, objects and simple types)
