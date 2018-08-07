<?php

require 'vendor/autoload.php';
require_once 'App/csvEditor.php';
require_once 'App/exceptions.php';

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

$faker = Faker\Factory::create();
$delimiter_delim = ',';
$skip_first = false;
$strict = false;

//Считывание параметров

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
                    exit;
                }
                break;
        }
    }
}

if (!$input_file || !$config_file || !$output_file) {
    echo "Необходимо ввести обязательные параметры:
    \n-i|--input-file - путь до исходного файла
    \n-c|--config-file - путь до файла конфигураци
    \n-o|--output-file - путь до файла с результатом
    \nПолный список параметров можно увидеть в справке (-h|--help)\n";
    exit;
}

$configs = include($config_file);

//Проверка на исключения, запуск функции, выполняющей изменения данных и запись в новый файл
try {
    if ($strict) {
        testStrict($input_file, $delimiter_delim, $configs);
    }
    testException($output_file, $input_file, $delimiter_delim);
    
    csvEditor($input_file, $output_file, $delimiter_delim, $configs, $skip_first, $faker);
} catch (Exception $e) {
    echo 'Error: ', $e->getMessage(), "\n";
    exit;
}

//var_dump($encoding);

//function fputcsv_eol($fp, $array, $eol, $delimiter = ',', $enclosure = '"', $escape_char = "\\")
//{
//    fputcsv($fp, $array, $delimiter, $enclosure, $escape_char);
//    if ("\n" != $eol && 0 === fseek($fp, -1, SEEK_CUR)) {
//        fwrite($fp, $eol);
//    }
//}
