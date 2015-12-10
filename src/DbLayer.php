<?php

namespace vaidasif\dbal;

use PDO;

class DbLayer
{
    private $aParams;
    private $connection;

    public function __construct($aParams = [])
    {
        $this->aParams = $aParams;
    }

    /**
     * @return PDO
     */
    public function getConnection()
    {
        if (!$this->connection) {
            $this->connection = new DbConnection($this->aParams);
        }
        return $this->connection->getConnection();
    }

    /**
     * @param $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Insert one record to table.
     *
     * @param string $sTable
     * @param array $aData
     * @return string
     */
    public function insert($sTable, $aData)
    {
        $aData = $this->filterData($aData);
        $sFields = join(', ', $this->extractFields($aData));
        $sFieldParams = join(', ', $this->extractFieldParams($aData));

        $sSql = "INSERT INTO {$sTable} (".$sFields.") VALUES (".$sFieldParams.")";
        $statement = $this->getConnection()->prepare($sSql);
        $this->bindData($aData, $statement);
        $statement->execute();

        return $this->getConnection()->lastInsertId();
    }

    /**
     * Update one record in table.
     *
     * @param string $sTable
     * @param array $aData
     * @param string $sId
     * @return bool
     */
    public function update($sTable, $aData, $sId)
    {
        $aData = $this->filterData($aData);
        $sSql = "UPDATE {$sTable} SET {$this->prepareUpdateData($aData)} WHERE id=:id";
        $statement = $this->getConnection()->prepare($sSql);
        $statement->bindParam(":id", $sId);
        $this->bindData($aData, $statement);

        return $statement->execute();
    }

    /**
     * Remove one record from table.
     *
     * @param string $sTable
     * @param string $sId
     * @return bool
     */
    public function delete($sTable, $sId)
    {
        $sSql = "DELETE FROM {$sTable} WHERE id=:id";
        $statement = $this->getConnection()->prepare($sSql);
        $statement->bindParam(":id", $sId);

        return $statement->execute();
    }

    /**
     * Select all records from table.
     *
     * @param string $sTable
     * @return array
     */
    public function selectAll($sTable)
    {
        $sSql = "SELECT * FROM {$sTable}";
        $statement = $this->getConnection()->prepare($sSql);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Select one record from table.
     *
     * @param string $sTable
     * @param string $sId
     * @return array
     */
    public function selectOne($sTable, $sId)
    {
        $sSql = "SELECT * FROM {$sTable} WHERE id=:id";
        $statement = $this->getConnection()->prepare($sSql);
        $statement->bindParam(":id", $sId);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param array $aData
     * @param $statement
     */
    public function bindData($aData, &$statement)
    {
        $aFieldParams = $this->extractFieldParams($aData);
        $aValues = $this->extractValues($aData);
        for ($i = 0; $i < count($aData); $i++) {
            $statement->bindParam($aFieldParams[$i], $aValues[$i]);
        }
    }

    /**
     * @param array $aData
     * @return string
     */
    public function prepareUpdateData($aData)
    {
        $aFieldNames = $this->extractFields($aData);
        $aFieldParams = $this->extractFieldParams($aData);
        $aUpdateFields = [];
        for ($i=0; $i < count($aData); $i++) {
            $aUpdateFields[] = $aFieldNames[$i]."=".$aFieldParams[$i];
        }

        return join(', ', $aUpdateFields);
    }

    /**
     * @param array $aData
     * @return array
     */
    protected function extractFields($aData)
    {
        return array_keys($aData);
    }

    /**
     * @param array $aData
     * @return array
     */
    protected function extractFieldParams($aData)
    {
        $aParams = [];
        foreach ($this->extractFields($aData) as $sColumn) {
            $aParams[] = ':'.$sColumn;
        }

        return $aParams;
    }

    /**
     * @param array $aData
     * @return array
     */
    protected function extractValues($aData)
    {
        return array_values($aData);
    }

    /**
     * @param array $aData
     * @return array
     */
    protected function filterData($aData)
    {
        return array_filter($aData);
    }
}