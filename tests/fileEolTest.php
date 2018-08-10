<?php

namespace Test;

use PHPUnit\Framework\TestCase;

class TestEol extends TestCase
{
    private $script = '../script.php';
    private $output_file = 'test_output.csv';

    /**
     * @dataProvider additionProvider
     */
    public function testFileExists($input, $arrayParams)
    {
        $status = "";
        exec(
            "php " . $this->script . " " . implode(" ", $arrayParams),
            $output,
            $status
        );

        $input_eol = $this->detectEOL($input);
        $output_eol = $this->detectEOL($this->output_file);

        $this->assertEquals($input_eol, $output_eol);
    }

    public function additionProvider()
    {
        $input_N = 'files/eolN.csv';
        $input_RN = 'files/eolRN.csv';
        $test_conf = 'files/test_conf.php';
        $output = $this->output_file;
        return [
            // EOL = \n
            [$input_N, ["-i $input_N", "-c $test_conf", "-o $output"]],

            // EOL = \r\n
            [$input_RN, ["-i $input_RN", "-c $test_conf", "-o $output"]],
        ];
    }

    //Функция определяет окончание строки в исходном файле
    private function detectEOL($input)
    {
        if (($fo= fopen($input, "r")) !== false) {
            $row = fgets($fo);
            $eol = substr($row, -2);
            if ($eol == "\r\n") {
                return '\r\n';
            } elseif ($eol == "\n\r") {
                return '\n\r';
            } elseif (substr($eol, -1) == "\n") {
                return '\n';
            }
            return 'undefined';
        }
    }

    protected function tearDown()
    {
        if (file_exists($this->output_file)) {
            unlink($this->output_file);
        }
    }
}
