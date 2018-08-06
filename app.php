<?php
require 'vendor/autoload.php';

$faker = Faker\Factory::create();
$help = "CSV Editor - это конслоньная программа для преобразования данных из csv-файла, в которых значения 
определенных полей заменяется по конфигурационному файлу. Программа принимает на вход 3 обязательных параметра.
Первый - путь до исходного csv-файла с данными, второй - путь до конфигурационного файла, в котором определено,
в каком столбце и по какой схеме заменять значения. Третий путь до файла для сохранения результата. 
Результат работы - csv-файл с тем же форматированием, что и исходный.\n
        \n-i|--input file - путь до исходного файла
        \n-c|--config file - путь до файла конфигурации
        \n-o|--output file - путь до файла с результатом
        \n-d|--delimiter delim - задать разделитель (по умолчанию “,”)
        \n--skip-first - пропускать модификацию первой строки исходного csv
        \n--strict - проверять, что исходный файл содержит необходимое количество описанных в конфигурационном
файле столбцов. При несоответствии выдавать ошибку.
        \n-h|--help - вывести справку\n";

//$virtual_hosts_dir = __DIR__;
//if (!is_dir($virtual_hosts_dir) || !is_writable($virtual_hosts_dir))
//{
//    echo "You must run this script as root!\n";
//    exit;
//}

//$input_file = 'HD-94.csv';
$delimiter_delim = ',';
$skip_first = false;
$strict = false;

/**
 * Считывание параметров
 */

if ($argc > 1) {
    for ($i = 1; $i < $argc; $i = $i + 2) {
        switch ($argv[$i]) {
            case "-i":
            case "--input-file":
                $input_file = $argv[$i + 1];
                break;

            case "-c":
            case "--config-file":
                $config_file = $argv[$i + 1];
                break;

            case "-o":
            case "--output-file":
                $output_file = $argv[$i + 1];
                break;

            case "-d":
            case "--delimiter-delim":
                $delimiter_delim = $argv[$i + 1];
                break;

            case "--skip-first":
                $skip_first = true;
                break;

            case "--strict":
                $strict = true;
                break;

            case "-h":
            case "--help":
                echo $help;
                break;

            default:
                if (substr($argv[$i], 0, 1) == '-') {
                    echo "Неизвестная опция: {$argv[$i]}\n";
                }
                break;
        }
    }
}

if (!$input_file || !$config_file || !$output_file) {
    echo "Необходимо ввести три обязательных параметра:
    \n-i|--input-file - путь до исходного файла
    \n-c|--config-file - путь до файла конфигураци
    \n-o|--output-file - путь до файла с результатом
    \nПолный список параметров можно увидеть в справке (-h|--help)\n";
    exit;
}

$configs = include($config_file);

if ($strict) {
    try {
        testStrict($input_file, $delimiter_delim, $configs);
    } catch (Exception $e) {
        echo 'Error: ', $e->getMessage(), "\n";
    }
}

/**
 * Считывание, изменение и запись в новый файл строк
 */

$row = 0;
if (($handle = fopen($input_file, "r")) !== FALSE) {
    $handle2 = fopen($output_file, "w+");
    while (($data = fgetcsv($handle, 1000, $delimiter_delim)) !== FALSE) {
        foreach ($configs as $key => $value) {
            if ($skip_first) {
                $skip_first = false;
                continue;
            }
            $data[$key] = is_callable($value) ? $value($data[$key], $data,
                $row, $faker) : ($value ? $faker->$value() : $value);
        }
        fputcsv($handle2, $data);
        $row++;
    }
    fclose($handle);
    fclose($handle2);
}

function testStrict($input, $delimiter, $config)
{
    if (($testHandle = fopen($input, "r")) !== FALSE) {
        $testData = fgetcsv($testHandle, 1000, $delimiter);
        if (count($testData) < max(array_keys($config))) {
            throw new Exception('Конфигурационный файл ссылается на несуществующие столбцы в исходном файле!');
        }
    }
    return;
}