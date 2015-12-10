<?php

namespace vaidasif\dbal;

use PDO;

class DbConnection
{
    private $aDbParams;
    private $aDbOptions = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];
    private $connection;
    private $dbDriverManager;

    public function __construct($aParams = [])
    {
        $this->aDbParams = $aParams;
    }

    /**
     * @return PDO
     */
    public function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->connect();
        }
        return $this->connection;
    }

    /**
     * @param PDO $connection
     */
    public function setConnection(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return PDO
     */
    protected function connect()
    {
        $this->validateDbParams();
        $sDsn = $this->getDbDriverManager()->prepareDsn($this->aDbParams);

        return new PDO($sDsn, $this->aDbParams['dbal_user'], $this->aDbParams['dbal_password'], $this->aDbOptions);
    }

    /**
     * @return DbDriverManager
     */
    public function getDbDriverManager()
    {
        if (!$this->dbDriverManager) {
            $this->dbDriverManager = new DbDriverManager();
        }

        return $this->dbDriverManager;
    }

    /**
     * @param $dbDriverManager
     */
    public function setDbDriverManager($dbDriverManager)
    {
        $this->dbDriverManager = $dbDriverManager;
    }

    /**
     * @return bool
     * @throws \InvalidArgumentException
     */
    protected function validateDbParams()
    {
        if (!isset($this->aDbParams['dbal_user']) || !$this->aDbParams['dbal_user']) {
            throw new \InvalidArgumentException('Missing "dbal_user"');
        }

        if (!isset($this->aDbParams['dbal_password']) || !$this->aDbParams['dbal_password']) {
            throw new \InvalidArgumentException('Missing "dbal_password"');
        }

        return true;
    }
}