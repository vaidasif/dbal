# Usage

Import `DbLayer` class. Pass database connection parameters to it 
```php
$config = [
    'dbal_type' => 'mysql',
    'dbal_host' => 'localhost',
    'dbal_database' => 'dbname',
    'dbal_user' => 'dbUser',
    'dbal_password' => 'dbUserPass'
];
$db = new DbLayer($config);
$db->selectAll('someTableName');
```

# Testing

- Install composer dependencies `composer install`.
- Run phpunit tests `php vendor/phpunit/phpunit/phpunit`.

# Source code

Source code can be downloaded from github `https://github.com/vaidasif/dbal.git`.