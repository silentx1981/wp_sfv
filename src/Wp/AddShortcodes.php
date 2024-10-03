<?php

namespace wpSfv\Wp;

use SfvApi\Config\Config;
use SfvApi\Sfv;
use wpSfv\Db\CreateDb;
use wpSfv\Db\DB;
use wpSfv\Db\DropDb;
use wpSfv\Lib\Games;

class AddShortcodes
{
    public static function run() : void
    {
        Config::init(plugin_dir_path(dirname(__FILE__, 2)));
        add_shortcode('sfvGames', [self::class, 'getGames']);
    }

    public static function getGames(array $attrs = [])
    {
        $games = new Games();
        $result = $games->getGames(Games::ViewMode_Carousel, Games::GroupBy_Day);


        /*
        $api = new Sfv();
        $games = $api->getGames(1);

        $db = new DB();
        $x = $db->getLastRun('schedule');
        $db->truncate('schedule');
        $db->insert('schedule', $games);
        $db->updateLastRun('schedule');

        $x = new CreateDb();
        //$x->run();

*/

        return $result;
        return 'xyz';
        //return implode(',', $games);
    }
}