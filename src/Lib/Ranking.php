<?php

namespace wpSfv\Lib;

use SfvApi\Config\Config;
use SfvApi\Sfv;
use wpSfv\Db\DB;

class Ranking
{
    private $tableName = 'ranking';

    public function __construct()
    {
        $db = new DB();
        $api = new Sfv();
        $reload = Config::get('sfvApiReload', 'Ranking') ?? 'PT1H';
        $dateNextUpdate = new \DateTime('now');
        $interval = new \DateInterval($reload);
        $dateNextUpdate->sub($interval);

        $lastRun = $db->getLastRun($this->tableName)['timestamp'] ?? null;
        if ($lastRun)
            $lastRun = new \DateTime($lastRun);
        if (!$lastRun || $lastRun < $dateNextUpdate) {
            $db->truncate($this->tableName);
            $db->insert($this->tableName, $api->getRanking(13029));
            $db->updateLastRun($this->tableName);
        }
    }
}