<?php

namespace Test;

use PHPUnit\Framework\TestCase;

class TestFile extends TestCase
{
    private $script = '../script.php';
    private $output_file = 'test_output.csv';

    /**
     * @dataProvider additionProvider
     */
    public function testFileExists($expected, $arrayParams)
    {
        $status = "";
        exec(
            "php " . $this->script . " " . implode(" ", $arrayParams),
            $output,
            $status
        );
        $this->assertEquals($expected, file_exists($this->output_file));
    }

    public function additionProvider()
    {
        $input_utf_8 = 'files/test_utf-8.csv';
        $input_cp1251 = 'files/test_cp1251.csv';
        $input_delim = 'files/test_delim.csv';
        $test_conf = 'files/test_conf.php';
        $test_wrong_conf = 'files/test_wrong_conf.php';
        $output = $this->output_file;
        return [
            // обычный вызов с корректными данными и конфигурацией
            [true, ["-i $input_utf_8", "-c $test_conf", "-o $output"]],

            // обычный вызов с кодировкой cp1251 данными и конфигурацией
            [true, ["-i $input_cp1251", "-c $test_conf", "-o $output"]],

            // неправильный делиметр
            [false, ["-i $input_utf_8", "-c $test_conf", "-o $output", '-d "asdf"']],
            [false, ["-i $input_utf_8", "-c $test_conf", "-o $output", '-d']],

            // неправильный конфиг
            [false, ["-i $input_utf_8", "-c $test_wrong_conf", "-o $output"]],

            // правильный делиметр и файл с ним
            [true, ["-i $input_utf_8", "-c $test_conf", "-o $output", '-d ","']],
            [true, ["-i $input_delim", "-c $test_conf", "-o $output", '-d ";"']],
        ];
    }

    protected function tearDown()
    {
        if (file_exists($this->output_file)) {
            unlink($this->output_file);
        }
    }
}