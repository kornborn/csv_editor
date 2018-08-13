<?php

require 'vendor/autoload.php';

$help = "CSV Editor - это конслоньная программа для преобразования данных в файле формата csv. Данные изменяются в 
соответствии с конфигурационным файлом. Программа принимает на вход 3 обязательных параметра.
Первый - путь до исходного csv-файла с данными, второй - путь до конфигурационного файла, в котором определено,
в каком столбце и по какой схеме заменять значения. Третий - путь до файла для сохранения результата. 
Результат работы - csv-файл с тем же форматированием, что и исходный." .
    PHP_EOL . "-c|--config file - путь до файла конфигурации" .
    PHP_EOL . "-o|--output file - путь до файла с результатом" .
    PHP_EOL . "-d|--delimiter delim - задать разделитель (по умолчанию “,”)" .
    PHP_EOL . "--skip-first - пропускать модификацию первой строки исходного csv" .
    PHP_EOL . "--strict - проверять, что исходный файл содержит необходимое количество описанных в конфигурационном" .
    PHP_EOL . "файле столбцов. При несоответствии выдавать ошибку." .
    PHP_EOL . "-h|--help - вывести справку." . PHP_EOL;

$faker = Faker\Factory::create();
$delimiter_delim = ',';
$skip_first = false;
$strict = false;
$input_file = '';
$config_file = '';
$output_file = '';

//Считывание параметров
if ($argc > 1) {
    for ($i = 1; $i < $argc; $i++) {
        switch ($argv[$i]) {
            case "-i":
            case "--input":
                $i++;
                $input_file = $argv[$i];
                break;

            case "-c":
            case "--config":
                $i++;
                $config_file = $argv[$i];
                break;

            case "-o":
            case "--output":
                $i++;
                $output_file = $argv[$i];
                break;

            case "-d":
            case "--delimiter":
                $i++;
                if (isset($argv[$i])) {
                    $delimiter_delim = $argv[$i];
                }
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
                exit(0);

            default:
                echo "Неизвестный параметр: {$argv[$i]}\n";
                exit(1);
                break;
        }
    }
}

if (!$input_file || !$config_file || !$output_file) {
    echo "Необходимо ввести обязательные параметры:" .
        PHP_EOL . "-i|--input file - путь до исходного файла" .
        PHP_EOL . "-c|--config file - путь до файла конфигураци" .
        PHP_EOL . "-o|--output file - путь до файла с результатом" .
        PHP_EOL . "Полный список параметров можно увидеть в справке (-h|--help)." . PHP_EOL;
    exit(1);
}


//Проверка на исключения, запуск функции, которая изменяет данные и записывает в новый файл
try {
    testConfig($config_file);

    $configs = include($config_file);

    if ($strict) {
        testStrict($input_file, $delimiter_delim, $configs);
    }

    testException($output_file, $input_file, $configs, $delimiter_delim);

    csvEditor($input_file, $output_file, $configs, $faker, $delimiter_delim, $skip_first);
} catch (Exception $e) {
    echo 'Error: ', $e->getMessage(), "\n";
    exit(1);
}
