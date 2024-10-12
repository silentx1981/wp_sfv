<?php

namespace wpSfv\Wp;

use SfvApi\Config\Config;
use wpSfv\Lib\Games;
use wpSfv\Lib\Ranking;

class AddShortcodes
{
    public static function run() : void
    {
        Config::init(plugin_dir_path(dirname(__FILE__, 2)));
        add_shortcode('sfvGames', function($attrs) {
            return self::getGames($attrs);
        });
        add_shortcode('sfvRanking', function($attrs) {
            return self::getRanking($attrs);
        });
    }

    public static function getGames(array $attrs = [])
    {
        $viewMode = $attrs['viewmode'] ?? Games::ViewMode_Grid;
        $groupBy = $attrs['groupby'] ?? Games::GroupBy_Day;
        $daysBefore = $attrs['daysbefore'] ?? null;
        $daysAfter = $attrs['daysafter'] ?? null;
        $teamId = $attrs['teamid'] ?? null;

        $games = new Games();
        $result = $games->getGames( $viewMode, $groupBy, $daysBefore, $daysAfter, $teamId);

        return $result;
    }

    public static function getRanking(array $attrs = [])
    {
        $leagueId = $attrs['leagueid'] ?? null;

        $ranking = new Ranking();
        $result = $ranking->getRanking($leagueId);

        return $result;
    }
}