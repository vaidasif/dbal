<?php

namespace vaidasif\dbal\drivers;

/**
 * Class MysqlDriverTest
 * @package vaidasif\dbal\drivers
 */
class MysqlDriverTest extends \PHPUnit_Framework_TestCase
{
    /** @var MySqlDriver */
    private $driver;

    protected function setUp()
    {
        $this->driver = new MySqlDriver();
    }

    protected function tearDown()
    {
        $this->driver = null;
    }

    /**
     * @param array $aParams
     * @dataProvider wrongParameters
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionWhenMissingParameters(array $aParams)
    {
        $this->driver->generateDsn($aParams);
    }

    public function testGeneratesDsnString()
    {
        $aParams = [
            'dbal_host' => 'myHost',
            'dbal_database' => 'myDb'
        ];

        $this->assertStringStartsWith('mysql:', $this->driver->generateDsn($aParams));
    }

    public function wrongParameters()
    {
        return [
            [[]],
            [['dbal_hosts' => 'something']],
            [['dbal_host' => 'something', 'dbal_database' => null ]],
            [['dbal_host' => 'something', 'databases' => 'else' ]],
        ];
    }
}
