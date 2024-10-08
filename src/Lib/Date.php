<?php

namespace wpSfv\Lib;

class Date
{
    public static function formatDateGerman($dateString) {
        $fmt = new \IntlDateFormatter(
            'de_CH', // Schweizerdeutsch (für Hochdeutsch: 'de_DE')
            \IntlDateFormatter::FULL, // Vollständige Datumsanzeige (z. B. Montag, 4. Oktober 2024)
            \IntlDateFormatter::NONE // Keine Uhrzeitanzeige
        );

        $date = new \DateTime($dateString);
        return $fmt->format($date);
    }
}