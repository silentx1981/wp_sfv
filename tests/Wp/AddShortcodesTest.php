<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use wpSfv\Wp\AddShortcodes;

class AddShortcodesTest extends TestCase
{
    public function testGetGames()
    {
        $this->assertEquals('Test', AddShortcodes::getGames());
    }
}