<?php

namespace wpSfv\Db;

use SfvApi\Config\Config;

class CreateDb
{
    public function run()
    {
        $dbSchema = json_decode(file_get_contents(plugin_dir_path(dirname(__FILE__, 2)) . '/config/db.json'), true);

        foreach ($dbSchema as $tableName => $tableSchema)
            $this->createDb($tableSchema, $tableName);
    }

    private function createDb($schema, $tableName)
    {
        global $wpdb;
        $tableName = $wpdb->prefix . Config::get('tableConfig', 'prefix') . $tableName;

        $columns = [];
        foreach ($schema as $field => $properties) {
            $column = "$field " . $this->getSqlType($properties);
            if (($properties['primarykey'] ?? null) === true)
                $column .= ' PRIMARY KEY';

            if (isset($properties['nullable']) && $properties['nullable'] === true) {
                $column .= " NULL";
            } else {
                $column .= " NOT NULL";
            }
            $columns[] = $column;
        }

        $columns_sql = implode(",\n", $columns);
        $sql = "CREATE TABLE IF NOT EXISTS $tableName ($columns_sql)";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private function getSqlType($properties)
    {
        switch ($properties['type']) {
            case 'integer':
                return 'INT(' . ($properties['format'] == 'int32' ? 11 : 20) . ')';
            case 'string':
                return 'VARCHAR(255)';
            case 'boolean':
                return 'TINYINT(1)';
            case 'date-time':
                return 'DATETIME';
            default:
                return 'TEXT';
        }
    }
}