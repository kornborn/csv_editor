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
function testException($output, $input, $config, $delimiter)
{
    //Проверка конфигурационного файла
    if (!is_array($config)) {
        throw new Exception('Конфигурационный файл настроен неправильно!');
    }

    //Проверка на доступ к папке с выходным файлом
    if ($output[0] == DIRECTORY_SEPARATOR) {
        $arr = explode(DIRECTORY_SEPARATOR, $output);
        array_pop($arr);
        $out_dir = implode(DIRECTORY_SEPARATOR, $arr);
    } else {
        $out_dir = __DIR__;
    }
    if (!is_dir($out_dir) || !is_writable($out_dir)) {
        throw new Exception('Нет доступа к папке для сохранения нового файла!');
    }

    //Проверка на доступ к исходному файлу
    if (!is_file($input) || !is_readable($input)) {
        throw new Exception('Нет доступа к исходному файлу!');
    }

    //Проверка разделителя
    if (strlen($delimiter) != 1) {
        throw new Exception('Разделитель должен быть одним символом!');
    }
}

function testConfig($config)
{
    //Проверка на доступ к конфигурационному файлу
    if (!is_file($config) || !is_readable($config)) {
        throw new Exception('Нет доступа к конфигурационному файлу!');
    }
}
