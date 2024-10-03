<?php

namespace wpSfv\Lib;

use SfvApi\Config\Config;

class Teams
{
    private $teamNames;
    private $homeIds;

    public function __construct()
    {
        $this->teamNames = Config::get('teams', 'names');
        $this->homeIds = Config::get('teams', 'homeIds');
    }

    public function renderTeamName($teamId, $teamName)
    {
        $result = $teamName;
        if (isset($this->teamNames[$teamId]))
            $result = $this->teamNames[$teamId];

        return $result;
    }

    public function isHomeTeam($teamId)
    {
        return in_array($teamId, $this->homeIds);
    }
}