<?php

namespace wpSfv\Wp;

use SfvApi\Config\Config;
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
        $viewMode = $attrs['viewMode'] ?? Games::ViewMode_Grid;
        $groupBy = $attrs['groupBy'] ?? Games::GroupBy_Day;
        $daysBefore = $attrs['daysBefore'] ?? null;
        $daysAfter = $attrs['daysAfter'] ?? null;

        $games = new Games();
        $result = $games->getGames( $viewMode, $groupBy, $daysBefore, $daysAfter); //41006);

        return $result;
    }
}