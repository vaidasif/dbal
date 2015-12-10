<?php

namespace vaidasif\dbal;

class DbConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider wrongParameters
     * @expectedException \InvalidArgumentException
     */
    public function testValidateParameters($aParams)
    {
        $obj = new DbConnection($aParams);
        $obj->getConnection();
    }

    public function wrongParameters()
    {
        return [
            [[]],
            [['something' => 'foo']],
            [['dbal_user' => 'baz']],
            [['dbal_user' => 'bar', 'dbal_password' => null]]
        ];
    }

    /**
     * @expectedException \PDOException
     */
    public function testThrowsPdoException()
    {
        $sDsn = 'invalidDsn';
        $aValidParams = [
            'dbal_user' => 'john',
            'dbal_password' => 'secret',
        ];

        /** @var DbDriverManager $manager */
        $manager = $this->prophesize('vaidasif\dbal\DbDriverManager');
        $manager->prepareDsn($aValidParams)->willReturn($sDsn);

        $obj = new DbConnection($aValidParams);
        $obj->setDbDriverManager($manager->reveal());
        $obj->getConnection();
    }

    public function testDriverManagerInstance()
    {
        $obj = new DbConnection();
        $this->assertInstanceOf(DbDriverManager::class, $obj->getDbDriverManager());
    }
}
