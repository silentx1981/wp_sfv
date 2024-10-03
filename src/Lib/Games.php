<?php

namespace wpSfv\Lib;

use SfvApi\Sfv;
use wpSfv\Db\DB;

class Games
{
    const ViewMode_Grid = 'grid';
    const ViewMode_Carousel = 'carousel';
    const GroupBy_Game = 'game';
    const GroupBy_Day = 'day';
    private $tableName = 'schedule';

    public function __construct()
    {
        $db = new DB();
        $api = new Sfv();
        $dateNextUpdate = new \DateTime('now');
        $interval = new \DateInterval('PT1H');
        $dateNextUpdate->sub($interval);

        $lastRun = $db->getLastRun($this->tableName)['timestamp'] ?? null;
        if ($lastRun)
            $lastRun = new \DateTime($lastRun);
        if (!$lastRun || $lastRun < $dateNextUpdate) {
            $db->truncate($this->tableName);
            $db->insert($this->tableName, $api->getGames());
            $db->updateLastRun($this->tableName);
        }
    }

    public function getGames($viewMode = self::ViewMode_Grid, $groupBy = self::GroupBy_Game)
    {
        global $wpdb;
        $db = new DB();
        $dbTableName = $db->getWordpressTableName($this->tableName);

        $sql = 'SELECT * FROM ' . $dbTableName .' ORDER BY matchDate';
        $games = $wpdb->get_results($sql, ARRAY_A);

        if ($groupBy === self::GroupBy_Day)
            $games = $this->groupByDay($games);

        $result = '';
        if ($viewMode == self::ViewMode_Grid)
            return $this->renderGamesGrid($games);
        else if ($viewMode == self::ViewMode_Carousel)
            return $this->renderGamesCarousel($games);

        return $result;
    }

    private function groupByDay($games)
    {
        $result = [];
        foreach ($games as $game) {
            $date = new \DateTime($game['matchDate']);
            $date = $date->format('Y-m-d');
            $result[$date][] = $game;
        }

        return $result;
    }

    private function renderGame($game)
    {
        $teams = new Teams();
        $league = new League();
        $matchDate = new \DateTime($game['matchDate']);
        $zeit = $matchDate->format('H:i');
        $type = $league->renderLeagueName($game['leagueId'], $game['leagueName']);
        $teamNameA = $teams->renderTeamName($game['teamAId'], $game['teamNameA']);
        $scoreTeamA = $this->renderScore($game['scoreTeamA'], $game['matchState']);
        $teamNameB = $teams->renderTeamName($game['teamBId'], $game['teamNameB']);
        $scoreTeamB = $this->renderScore($game['scoreTeamB'], $game['matchState']);
        $typeIcon = '';

        if ($game['matchType'] === '2')
            $typeIcon = '<span><i class="fa-solid fa-trophy"></i> </span>';
        else if ($game['matchType'] === '3')
            $typeIcon = '<span><i class="fa-solid fa-dumbbell"></i> </span>';

        if ($teams->isHomeTeam($game['teamAId'])) {
            $teamNameA = "<strong>$teamNameA</strong>";
            $scoreTeamA = "<strong>$scoreTeamA</strong>";
        } else if ($teams->isHomeTeam($game['teamBId'])) {
            $teamNameB = "<strong>$teamNameB</strong>";
            $scoreTeamB = "<strong>$scoreTeamB</strong>";
        }

        $result = ' <div>
                        <div class="badge bg-primary w-100 mb-3">'.$typeIcon.$type.'</div>
                        <div class="d-flex mb-3">
                            <div class="align-self-start text-center ps-2 me-2">
                                <i class="far fa-2x fa-clock"></i>
                            </div>
                            <div class="align-self-center">'.$zeit.'</div>
                            <div class="align-self-center ms-auto"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="align-self-start">
                                    '.$teamNameA.'
                            </div>
                            <div>
                                '.$scoreTeamA.'
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="align-self-start">
                                    '.$teamNameB.'
                            </div>
                            <div>
                                '.$scoreTeamB.'
                            </div>
                        </div>
                    </div>';

        return $result;
    }

    private function renderGameDay($gamedaykey, $gameday)
    {
        $matchDate = new \DateTime($gamedaykey);
        $date = $matchDate->format('l d. F Y');

        $games = [];
        foreach ($gameday as $game)
            $games[] = $this->renderGame($game);

        $result = '    
                    <div class="col-md-4">
                        <div class="card m-2">
                            <div class="card-header text-center"><strong>'.$date.'</strong></div>
                            <div class="card-body">'.implode('<hr class="mb-2 border-2">', $games).'</div> 
                       </div>
                       <br><br><br><br>
                    </div>';

        return $result;
    }

    private function renderGamesCarousel($games)
    {
        $gamesChunk = array_chunk($games, 3, true);
        $slideId = 'slide99';
        $content = '';
        $active = 'active';
        $indicatorActive = 'class="active" aria-current="true"';

        $gamesChunk = array_slice($gamesChunk, 0, 4);

        $items = [];
        $indicators = [];
        foreach ($gamesChunk as $chunkIndex => $games) {

            $gamedays = [];
            foreach ($games as $gamekey => $game) {
                $gamedays[] = $this->renderGameDay($gamekey, $game);
            }

            $items[] = '
                <div class="carousel-item '.$active.'">
                    <div class="container">
                        <div class="row">
                            '.implode('', $gamedays).'
                        </div>
                    </div>
                </div>
            ';
            $indicators[] = '
                <button type="button" data-bs-target="#'.$slideId.'" data-bs-slide-to="'.$chunkIndex.'" '.$indicatorActive.' aria-label="Slide '.($chunkIndex+1).'" ></button>
            ';

            $active = '';
            $indicatorActive = '';
        }


        $result = '
            <div id="'.$slideId.'" class="carousel slide">
                <div class="carousel-indicators">
                    '.implode('', $indicators).'
                </div>
                <div class="carousel-inner" style="background-color: #8899aa">
                    '.implode(  '', $items).'
                </div>
                  <button class="carousel-control-prev" type="button" data-bs-target="#'.$slideId.'" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                  </button>
                  <button class="carousel-control-next" type="button" data-bs-target="#'.$slideId.'" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                  </button>
            </div>
        ';

        return $result;
    }

    private function renderGamesGrid($games)
    {
        $renderedGames = '';
        foreach ($games as $game) {
            $renderedGame = $this->renderGameDay($game);
            $renderedGames .= '<div class="col-md-4 col-sm-6 col-12 pb-3">'.$renderedGame.'</div>';
        }

        $result = '<div class="container-fluid"><div class="row">'.$renderedGames.'</div></div>';

        return $result;
    }

    private function renderScore($score, $matchState)
    {
        if ($matchState !== "2")
            $score = null;

        return $score;
    }


}