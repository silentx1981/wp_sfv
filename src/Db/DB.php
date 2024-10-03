<?php

namespace wpSfv\Db;

use _PHPStan_9815bbba4\Nette\Utils\DateTime;
use SfvApi\Config\Config;

class DB
{

    public function getLastRun($tableName)
    {
        global $wpdb;
        $tableName = $this->getWordpressTableName($tableName);
        $lastRunTablename = $wpdb->prefix . Config::get('tableConfig', 'prefix') . 'lastrun';

        $sql = $wpdb->prepare(
            "SELECT timestamp FROM $lastRunTablename WHERE tablename = '%s'",
            $tableName
        );

        $result = $wpdb->get_row($sql, ARRAY_A);

        return $result;
    }

    public function getWordpressTableName($tableName)
    {
        global $wpdb;

        return $wpdb->prefix . Config::get('tableConfig', 'prefix') . $tableName;
    }

    public function insert($tableName, $data)
    {
        global $wpdb;
        $tableName = $this->getWordpressTableName($tableName);

        foreach ($data as $value)
            $wpdb->insert($tableName, $value);
    }

    public function truncate($tableName)
    {
        global $wpdb;
        $tableName = $this->getWordpressTableName($tableName);

        $wpdb->query("TRUNCATE TABLE $tableName");
    }

    public function updateLastRun($tableName)
    {
        global $wpdb;
        $tableName = $this->getWordpressTableName($tableName);
        $lastRunTablename = $wpdb->prefix . Config::get('tableConfig', 'prefix') . 'lastrun';

        $date = new DateTime('now');
        $isoDate = $date->format(DateTime::ATOM);
        $sql = $wpdb->prepare(
            "INSERT INTO $lastRunTablename (tablename, timestamp) VALUES (%s, %s) ON DUPLICATE KEY UPDATE timestamp = VALUES(timestamp)",
            $tableName, $isoDate
        );

        $wpdb->query($sql);
    }
}