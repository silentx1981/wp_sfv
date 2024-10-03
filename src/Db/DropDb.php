<?php

namespace wpSfv\Db;

use SfvApi\Config\Config;

class DropDb
{
    public function run()
    {
        $dbSchema = json_decode(file_get_contents(plugin_dir_path(dirname(__FILE__, 2)) . '/config/db.json'), true);

        foreach ($dbSchema as $tableName => $tableSchema)
            $this->dropDb($tableName);
    }

    private function dropDb($tableName)
    {
        global $wpdb;
        $tableName = $wpdb->prefix . Config::get('tableConfig', 'prefix') . $tableName;
        $wpdb->query("DROP TABLE IF EXISTS $tableName");
    }

}