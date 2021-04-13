<?php

namespace Tests;

use Tests\BaseTestCase;
use contSoft\Finanzas\Clases\Asobancaria\AsobanParser;

class Format2001Test extends BaseTestCase
{

    public function testItWorks()
    {
        $file = $this->generateFile();

        $asoban = AsobanParser::load($file, AsobanParser::F_2001)->parse();

        $this->assertEquals('12345', $asoban->header()->nit());
        $this->assertEquals('00849514521', $asoban->header()->accountNumber());
        $this->assertEquals('1', $asoban->header()->accountType());
    }

    /**
     * @expectedException \Exception
     */
    public function testItThrowsExceptionWhenFileDoesntExists()
    {
        AsobanParser::load(__DIR__.'/files/not-exists', AsobanParser::F_2001)->parse();
    }

}