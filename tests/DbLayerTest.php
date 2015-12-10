<?php

namespace vaidasif\dbal;

use Prophecy\Argument;

class DbLayerTest extends \PHPUnit_Framework_TestCase
{
    /** @var DbLayer */
    private $dbLayer;

    protected function setUp()
    {
        $this->dbLayer = new DbLayer();
    }

    protected function tearDown()
    {
        $this->dbLayer = null;
    }

    public function testSelectAllRecords()
    {
        $sTable = 'someTable';
        $aRowsData = ['data'];

        $pdoStatement = $this->prophesize('\PDOStatement');
        $pdoStatement->execute()->willReturn(true);
        $pdoStatement->fetchAll(\PDO::FETCH_ASSOC)->willReturn($aRowsData);

        $pdo = $this->prophesize('PDO');
        $pdo->prepare(Argument::any())->willReturn($pdoStatement->reveal());

        /** @var DbConnection $connection */
        $connection = $this->prophesize('vaidasif\dbal\DbConnection');
        $connection->getConnection()->willReturn($pdo->reveal());

        $this->dbLayer->setConnection($connection->reveal());
        $this->assertTrue(is_array($this->dbLayer->selectAll($sTable)));
    }

    public function testSelectAllOneRecord()
    {
        $sTable = 'someTable';
        $aRowData = ['data'];
        $iId = 123;

        $pdoStatement = $this->prophesize('\PDOStatement');
        $pdoStatement->bindParam(Argument::any(), $iId)->shouldBeCalled();
        $pdoStatement->execute()->willReturn(true);
        $pdoStatement->fetch(\PDO::FETCH_ASSOC)->willReturn($aRowData);

        $pdo = $this->prophesize('PDO');
        $pdo->prepare(Argument::any())->willReturn($pdoStatement->reveal());

        /** @var DbConnection $connection */
        $connection = $this->prophesize('vaidasif\dbal\DbConnection');
        $connection->getConnection()->willReturn($pdo->reveal());

        $this->dbLayer->setConnection($connection->reveal());
        $this->assertTrue(is_array($this->dbLayer->selectOne($sTable, $iId)));
    }

    public function testDeleteRecord()
    {
        $sTable = 'someTable';
        $iId = 123;
        $blRes = true;

        $pdoStatement = $this->prophesize('\PDOStatement');
        $pdoStatement->bindParam(Argument::any(), $iId)->shouldBeCalled();
        $pdoStatement->execute()->willReturn($blRes);

        $pdo = $this->prophesize('PDO');
        $pdo->prepare(Argument::any())->willReturn($pdoStatement->reveal());

        /** @var DbConnection $connection */
        $connection = $this->prophesize('vaidasif\dbal\DbConnection');
        $connection->getConnection()->willReturn($pdo->reveal());

        $this->dbLayer->setConnection($connection->reveal());
        $this->assertSame($blRes, $this->dbLayer->delete($sTable, $iId));
    }

    /**
     * @param $aData
     * @param $sResult
     * @dataProvider prepareUpdateDataProvider
     */
    public function testUpdateData($aData, $sResult)
    {
        $this->assertSame($sResult, $this->dbLayer->prepareUpdateData($aData));
    }

    public function prepareUpdateDataProvider()
    {
        return [
            [
                [
                    'field1' => 'value1',
                    'field2' => 'value2'
                ],
                'field1=:field1, field2=:field2'
            ],
            [
                [
                    'field1' => 'value1'
                ],
                'field1=:field1'
            ],
            [
                [
                    'field1' => 'value1',
                    'field2' => null
                ],
                'field1=:field1, field2=:field2'
            ]
        ];
    }

    /**
     */
    public function testDataBinding()
    {
        $aData = [
            'field1' => 'value1',
            'field2' => 'value2'
        ];

        $pdoStatement = $this->prophesize('\PDOStatement');
        $pdoStatement->bindParam(':field1', 'value1')->shouldBeCalled();
        $pdoStatement->bindParam(':field2', 'value2')->shouldBeCalled();

        $this->dbLayer->bindData($aData, $pdoStatement->reveal());
    }

    /**
     * @param $aData
     * @param $aBindData
     * @dataProvider insertUpdateActionProvider
     */
    public function testUpdateAction($aData, $aBindData)
    {
        $sTable = 'someTable';
        $iId = 123;
        $blRes = true;

        $pdoStatement = $this->prophesize('\PDOStatement');
        $pdoStatement->bindParam(Argument::any(), $iId)->shouldBeCalled();
        foreach ($aBindData as $sField => $sValue) {
            $pdoStatement->bindParam($sField, $sValue)->shouldBeCalled();
        }
        $pdoStatement->execute()->willReturn($blRes);

        $pdo = $this->prophesize('PDO');
        $pdo->prepare(Argument::any())->willReturn($pdoStatement->reveal());

        /** @var DbConnection $connection */
        $connection = $this->prophesize('vaidasif\dbal\DbConnection');
        $connection->getConnection()->willReturn($pdo->reveal());

        $this->dbLayer->setConnection($connection->reveal());
        $this->assertSame($blRes, $this->dbLayer->update($sTable, $aData, $iId));
    }

    /**
     * @param $aData
     * @param $aBindData
     * @dataProvider insertUpdateActionProvider
     */
    public function testInsertAction($aData, $aBindData)
    {
        $sTable = 'someTable';
        $iId = 123;

        $pdoStatement = $this->prophesize('\PDOStatement');
        foreach ($aBindData as $sField => $sValue) {
            $pdoStatement->bindParam($sField, $sValue)->shouldBeCalled();
        }
        $pdoStatement->execute()->willReturn(true);

        $pdo = $this->prophesize('PDO');
        $pdo->prepare(Argument::any())->willReturn($pdoStatement->reveal());
        $pdo->lastInsertId()->willReturn($iId);

        /** @var DbConnection $connection */
        $connection = $this->prophesize('vaidasif\dbal\DbConnection');
        $connection->getConnection()->willReturn($pdo->reveal());

        $this->dbLayer->setConnection($connection->reveal());
        $this->assertSame($iId, $this->dbLayer->insert($sTable, $aData));
    }

    public function insertUpdateActionProvider()
    {
        return [
            [
                [
                    'field1' => 'value1',
                    'field2' => 'value2'
                ],
                [
                    ':field1' => 'value1',
                    ':field2' => 'value2',
                ]
            ],
            [
                [
                    'field1' => 'value1'
                ],
                [
                    ':field1' => 'value1'
                ]
            ],
            [
                [
                    'field1' => 'value1',
                    'field2' => null
                ],
                [
                    ':field1' => 'value1'
                ]
            ]
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsDbConnectionValidationException()
    {
        $this->dbLayer->getConnection();
    }
}
