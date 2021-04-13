<?php
namespace Tests;


use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{

    public function generateFile()
    {
        $rows = [
            [
                ['01', 2],
                ['12345', 10],
                ['20161201', 8],
                ['ABC', 3],
                ['00849514521', 17, ' '],
                ['20161201', 8],
                ['0800', 4],
                ['Z', 1],
                ['1', 2],
                ['', 106, ' ']
            ],
            [
                ['05', 2],
                ['123456', 13],
                ['22', 4],
                ['', 142, ' ']
            ],
            [
                ['06', 2],
                ['1040035062', 48],
                ['1000200', 14],
                ['99', 2],
                ['15', 2],
                ['123456', 6],
                ['123456', 6],
                ['PSE', 3],
                ['1234', 4],
                ['2000000', 7],
                ['', 3, ' '],
                ['', 65, ' ']
            ],
            [
                ['08',2],
                ['1',9],
                ['1000200',18],
                ['1',4],
                ['', 129, ' ']
            ]
        ];

        $file = [];

        foreach ($rows as $row){
            $x = "";
            foreach ($row as $item){
                $x .= str_pad($item[0], $item[1], isset($item[2]) ? $item[2] : 0, STR_PAD_LEFT);
            }
            $file[] = $x;
        }

        file_put_contents(__DIR__ . '/files/2001', implode("\n", $file));
        return __DIR__ . '/files/2001';
    }

}