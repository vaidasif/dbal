<?php

namespace vaidasif\dbal;

use Prophecy\Argument;
use vaidasif\dbal\drivers\MySqlDriver;

class DbDriverManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var DbDriverManager */
    private $manager;

    protected function setUp()
    {
        $this->manager = new DbDriverManager();
    }

    protected function tearDown()
    {
        $this->manager = null;
    }

    /**
     * @param array $aParams
     * @dataProvider wrongParameters
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionWhenMissingParameters(array $aParams)
    {
        $this->manager->prepareDsn($aParams);
    }

    public function wrongParameters()
    {
        return [
            [[]],
            [['someKey' => 'something']],
            [['dbal_host' => 'something']],
            [['dbal_type' => 'wrong']],
        ];
    }

    public function testDsnGeneration()
    {
        $sDsn = 'someDsn';

        /** @var MysqlDriver $driver */
        $driver = $this->prophesize('vaidasif\dbal\drivers\MySqlDriver');
        $driver->generateDsn(Argument::any())->willReturn($sDsn);

        $this->manager->setDriver($driver->reveal());
        $this->assertSame($sDsn, $this->manager->prepareDsn(['dbal_type' => 'mysql']));
    }
}
