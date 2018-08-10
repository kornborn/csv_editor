<?php

namespace Test;

use PHPUnit\Framework\TestCase;

final class FileOutputContentTest extends TestCase
{
    private $script = '../script.php';
    private $output = 'files/test_output.csv';

    public function testContent()
    {
        $input_file = 'files/test_utf-8.csv';
        $output_file = $this->output;

        exec(
            'php ' . $this->script . ' -i ' . $input_file .' -c files/test_conf.php -o ' . $output_file,
            $output,
            $status
        );
        $data_input = [];
        $data_output = [];
        if (($handle = fopen($input_file, "r")) !== false) {
            $data = fgets($handle);
            $data_input = str_getcsv($data);
            fclose($handle);
        }
        if (($handle = fopen($output_file, "r")) !== false) {
            $data = fgets($handle);
            $data_output = str_getcsv($data);
            fclose($handle);
        }
        $this->assertFalse(is_numeric($data_output[0]));
        $this->assertTrue($data_output[1] == $data_input[1]);
        $this->assertFalse(is_numeric($data_output[2]));
        $this->assertTrue(is_numeric($data_output[3]));
    }

    protected function tearDown()
    {
        if (file_exists($this->output)) {
            unlink($this->output);
        }
    }
}