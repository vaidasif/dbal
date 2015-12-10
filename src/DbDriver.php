<?php

namespace vaidasif\dbal;

interface DbDriver
{
    public function generateDsn($aParams);
}