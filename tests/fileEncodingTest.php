<?php

namespace Test;

use PHPUnit\Framework\TestCase;

class TestEncoding extends TestCase
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

        $input_content = file_get_contents($input);
        $input_encoding = mb_check_encoding($input_content, 'UTF-8') ? 'UTF-8' : 'Windows-1251';

        $output_content = file_get_contents($this->output_file);
        $output_encoding = mb_check_encoding($output_content, 'UTF-8') ? 'UTF-8' : 'Windows-1251';

        $this->assertEquals($input_encoding, $output_encoding);
    }

    public function additionProvider()
    {
        $input_utf_8 = 'files/test_utf-8.csv';
        $input_cp1251 = 'files/test_cp1251.csv';
        $input_delim = 'files/test_delim.csv';
        $test_conf = 'files/test_conf.php';
        $output = $this->output_file;
        return [
            // Кодировка UTF-8
            [$input_utf_8, ["-i $input_utf_8", "-c $test_conf", "-o $output"]],
            [$input_delim, ["-i $input_delim", "-c $test_conf", "-o $output", '-d ";"']],

            // Кодировка cp1251
            [$input_cp1251, ["-i $input_cp1251", "-c $test_conf", "-o $output"]],
            [$input_cp1251, ["-i $input_cp1251", "-c $test_conf", "-o $output", "--strict"]],
        ];
    }

    protected function tearDown()
    {
        if (file_exists($this->output_file)) {
            unlink($this->output_file);
        }
    }
}
