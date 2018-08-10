<?php

namespace Test;

use PHPUnit\Framework\TestCase;

class TestOptions extends TestCase
{
    private $script;
    private $output_file = 'test_output.csv';

    protected function setUp()
    {
        $this->script = '../script.php';
    }

    /**
     * @dataProvider additionProvider
     */
    public function testOptions($expected, $arrayParams)
    {
        $status = "";
        exec(
            "php " . $this->script . " " . implode(" ", $arrayParams),
            $output,
            $status
        );
        $this->assertEquals($expected, $status == 0);
    }

    public function additionProvider()
    {
        $input_utf_8 = 'files/test_utf-8.csv';
        $input_cp1251 = 'files/test_cp1251.csv';
        $input_wrong = 'files/test_wrong.csv';
        $input_delim = 'files/test_delim.csv';
        $test_conf = 'files/test_conf.php';
        $test_wrong_conf = 'files/test_wrong_conf.php';
        $output = $this->output_file;
        return [
            // нет обязательных параметров
            [false, []],
            [false, ["-i"]],
            [false, ["-c"]],
            [false, ["-o"]],
            [false, ["--input"]],
            [false, ["--config"]],
            [false, ["--output"]],
            [false, ["-i fff", "-c zzz"]],
            [false, ["-c rrr", "-o ggg"]],
            [false, ["-i sss", "-o ggg"]],
            [false, ["-i fff", "-o sss", "-c eee"]],
            [false, ["-i $input_utf_8", "-c $test_conf", "--strict"]],

            // неправильный делиметр
            [false, ["-i $input_utf_8", "-c $test_conf", "-o $output", '-d "asdf"']],

            // неправильный конфиг
            [false, ["-i $input_utf_8", "-c $test_wrong_conf", "-o $output"]],

            // стрикт и плохой конфиг
            [false, ["-i $input_utf_8", "-c $test_wrong_conf", "-o $output", "--strict"]],

            // неправильные входные данные
            [false, ["-i $input_wrong", "-c $test_conf", "-o $output"]],

            // правильный делиметр и файл с ним
            [true, ["-i $input_utf_8", "-c $test_conf", "-o $output", '-d ","']],
            [true, ["-i $input_delim", "-c $test_conf", "-o $output", '-d ";"']],

            // skip-first
            [true, ["-i $input_utf_8", "-c $test_conf", "-o $output", "--skip-first"]],

            // стрикт с правильным конфигом
            [true, ["-i $input_utf_8", "-c $test_conf", "-o $output", "--strict"]],

            // обычный вызов с корректными данными и конфигурацией
            [true, ["-i $input_utf_8", "-c $test_conf", "-o $output"]],
            [true, ["-i $input_utf_8", "-c $test_conf", "-o $output", '-d']],

            // обычный вызов с кодировкой cp1251 данными и конфигурацией
            [true, ["-i $input_cp1251", "-c $test_conf", "-o $output"]],

            // help
            [true, ["-h"]],
            [true, ["--help"]],
        ];
    }

    protected function tearDown()
    {
        if (file_exists($this->output_file)) {
            unlink($this->output_file);
        }
    }
}
