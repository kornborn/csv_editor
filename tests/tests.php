<?php

require '../vendor/autoload.php';
require_once '../App/csvEditor.php';
require_once 'testCsvFile.php';

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

class TestSolution extends TestCase
{
    private $directory;
    private $input_file;
    private $output_file;
    private $configs;
    private $faker;
    private $delimiter;
    private $skip_first;
    private $array_content = [];

    protected function setUp()
    {
        $this->directory = __DIR__;
        $this->root = vfsStream::setup('files');
        $this->input_file = vfsStream::url('files/input_file.csv');
        $this->output_file = vfsStream::url('files/output_file.csv');
        $this->configs = include('test_conf.php');
        $this->delimiter = ';';
        $this->skip_first = true;
        $this->faker = Faker\Factory::create();
        for ($i = 0; $i < 5; $i++) {
                $this->array_content[$i] = [$this->faker->randomDigit, $this->faker->streetName, 'ты говно'];
        }
    }

    public function testOptions()
    {
        exec('php ../script.php -i HD-94.csv -c conf.php -o new.csv -sdf', $output, $status);
        $this->assertEquals($status, 1);
        exec('php ../script.php -i HD-94.csv -o new.csv', $output, $status);
        $this->assertEquals($status, 1);
        exec('php ../script.php -i HD-94.csv -c conf.php -o new.csv', $output, $status);
        $this->assertEquals($status, 0);
    }

    public function testFileExists()
    {
        makeCsvFile($this->input_file, $this->array_content, 'UTF-8');
        csvEditor($this->input_file, $this->output_file, $this->configs, $this->faker);
        $this->assertTrue(is_file($this->input_file));
    }
}
