<?php

namespace wpSfv\Wp;

use SfvApi\Sfv;

class AddShortcodes
{
    public static function run() : void
    {
        add_shortcode('sfvGames', [self::class, 'getGames']);
    }

    /**
     * @param array<string, string> $attrs
     * @return string
     */
    public static function getGames(array $attrs = []):string
    {
        $api = new Sfv(["xyz" => "abc"]);
        $games = $api->getGames(1);


        return implode(',', $games);
    }
}