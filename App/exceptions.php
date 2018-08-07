<?php

//Функция для проверки содержит ли исходный файл необходимое количество описанных в конфигурационном файле столбцов.
function testStrict($input, $delimiter, $config)
{
    if (($testHandle = fopen($input, "r")) !== false) {
        $testData = fgetcsv($testHandle, 1000, $delimiter);
        if (count($testData) < max(array_keys($config))) {
            throw new Exception('Конфигурационный файл ссылается на несуществующие столбцы в исходном файле!');
        }
    }
}

//Функция для проверки на исключения
function testException($output, $input, $delimiter)
{
    if ($output[0] == DIRECTORY_SEPARATOR) {
        $arr = explode(DIRECTORY_SEPARATOR, $output);
        array_pop($arr);
        $out_dir = implode(DIRECTORY_SEPARATOR, $arr);
    } else {
        $out_dir = __DIR__;
    }

    $input_arr = explode('.', $input);
    $input_format = array_pop($input_arr);

    if ($input_format != 'csv' && $input_format != 'dsv') {
        throw new Exception('Формат исходного файла должен быть CSV или DSV!');
    }

    if (!is_dir($out_dir) || !is_writable($out_dir)) {
        throw new Exception('Нет доступа к папке для сохранения нового файла!');
    }

    if (!is_file($input) || !is_readable($input)) {
        throw new Exception('Нет доступа исходному файлу!');
    }

    if (($testHandle = fopen($input, "r")) !== false) {
        $testData = fgetcsv($testHandle, 1000, $delimiter);
        if (mb_detect_encoding(implode('', $testData)) != 'UTF-8' &&
            mb_detect_encoding(implode('', $testData)) != 'cp1251') {
            throw new Exception('Кодировка исходного файла должна быть UTF-8 или cp1251');
        }
    }
}
