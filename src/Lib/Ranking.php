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
        $leagueIds = Config::get('league', 'ids');
        if (!is_array($leagueIds)) {
            return;
        }

        $dateNextUpdate = new \DateTime('now');
        $interval = new \DateInterval($reload);
        $dateNextUpdate->sub($interval);

        $lastRun = $db->getLastRun($this->tableName)['timestamp'] ?? null;
        if ($lastRun)
            $lastRun = new \DateTime($lastRun);
        if (!$lastRun || $lastRun < $dateNextUpdate) {
            $db->truncate($this->tableName);
            foreach ($leagueIds as $leagueId) {
                $db->insert($this->tableName, $api->getRanking($leagueId));
            }
            $db->updateLastRun($this->tableName);
        }
    }

    public function getRanking(string $leagueId)
    {
        global $wpdb;
        $db = new DB();
        $today = new \DateTime('now');
        $dbTableName = $db->getWordpressTableName($this->tableName);
        $where =  [
            'leagueId = '.$leagueId,
        ];

        if ($where !== [])
            $where = ' WHERE '.implode(' AND ', $where);
        else
            $where = null;

        $sql = 'SELECT * FROM '.$dbTableName.' '.$where.' ORDER BY position ASC';
        $ranking = $wpdb->get_results($sql, ARRAY_A);

        $result = $this->renderRanking($ranking);

        return $result;
    }

    private function renderRanking(array $ranking)
    {
        $positions = [];
        foreach ($ranking as $rank) {
            $positions[] = $this->renderRankingPosition($rank);
        }

        $result = '    <table class="table">
                        <thead class="table-light">
 							<tr>
								<th class="text-center">Rang</th>
								<th>Mannschaft</th>
								<th class="text-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Spiele">S</th>
								<th class="text-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Siege">S</th>
								<th class="text-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Unentschieden">U</th>
								<th class="text-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Niederlagen">N</th>
								<th class="text-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Strafpunkte">S</th>
								<th class="text-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Tore">T</th>
								<th class="text-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Punkte">P</th>
							</tr>                           
                        </thead>
                        <tbody>
                            '.implode('', $positions).'
                        </tbody>
                       </table>';

        return $result;
    }

    private function renderRankingPosition(array $position)
    {
        $teams = new Teams();
        $homeTeam = '';

        if ($teams->isHomeTeam($position['teamId']))
            $homeTeam = 'table-secondary fw-bold';

        $result = '	<tr class="'.$homeTeam.'">
						<td class="text-center" data-title="Rang" style="padding: 6px 24px !important">'.$position['position'].'</td>
						<td data-title="Mannschaft" style="padding: 6px 24px !important">'.$teams->renderTeamName($position['teamId'], $position['teamName']).'</td>
						<td class="text-center" data-title="Spiele" style="padding: 6px 24px !important">'.$position['matches'].'</td>
						<td class="text-center" data-title="Siege" style="padding: 6px 24px !important">'.$position['wins'].'</td>
						<td class="text-center" data-title="Unentschieden" style="padding: 6px 24px !important">'.$position['draws'].'</td>
						<td class="text-center" data-title="Niederlagen" style="padding: 6px 24px !important">'.$position['losses'].'</td>
						<td class="text-center" data-title="Strafpunkte" style="padding: 6px 24px !important">'.$position['penaltyPoints'].'</td>
						<td class="text-center" data-title="Tore" style="padding: 6px 24px !important">'.$position['goalsFor'].' : '.$position['goalsAgainst'].'</td>
						<td class="text-center" data-title="Punkte" style="padding: 6px 24px !important">'.$position['points'].'</td>
					</tr>';

        return $result;
    }


}