<?php

namespace wpSfv\Lib;

use SfvApi\Config\Config;

class League
{
    private $leagueNames;

    public function __construct() {
        $this->leagueNames = Config::get('league', 'names');
    }

    public function renderLeagueName($leagueId, $leagueName)
    {
        return $this->leagueNames[$leagueId] ?? $leagueName;
    }
}