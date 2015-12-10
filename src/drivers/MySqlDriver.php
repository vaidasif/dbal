<?php

namespace vaidasif\dbal\drivers;

use vaidasif\dbal\DbDriver;

class MySqlDriver implements DbDriver
{
    private $sDsnTemplate = 'mysql:host=%s;dbname=%s';

    /**
     * @param array $aParams
     * @return string
     */
    public function generateDsn($aParams)
    {
        if (!isset($aParams['dbal_host']) || !$aParams['dbal_host']) {
            throw new \InvalidArgumentException('Missing "dbal_host"');
        }

        if (!isset($aParams['dbal_database']) || !$aParams['dbal_database']) {
            throw new \InvalidArgumentException('Missing "dbal_database"');
        }

        return sprintf($this->sDsnTemplate, $aParams['dbal_host'], $aParams['dbal_database']);
    }
}