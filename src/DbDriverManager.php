<?php

namespace vaidasif\dbal;

use vaidasif\dbal\drivers\MySqlDriver;

class DbDriverManager
{
    private $driver;

    /**
     * @param $aParams
     * @return mixed
     */
    public function prepareDsn($aParams)
    {
        if (!isset($aParams['dbal_type']) || !$aParams['dbal_type']) {
            throw new \InvalidArgumentException('Missing "dbal_type"');
        }

        switch ($aParams['dbal_type']) {
            case 'mysql':
                $this->setDriver(new MySqlDriver());
                break;

            default:
                throw new \InvalidArgumentException('Unknown "dbal_type"');
                break;
        }

        return $this->getDriver()->generateDsn($aParams);
    }

    /**
     * @return DbDriver
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param DbDriver $driver
     */
    public function setDriver(DbDriver $driver)
    {
        if (!$this->driver || !$this->driver instanceof $driver) {
            $this->driver = $driver;
        }
    }

}