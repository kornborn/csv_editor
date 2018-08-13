<?php

//Считывание, изменение и запись в новый файл строк
function csvEditor($input_file, $output_file, $configs, $faker, $delimiter_delim = ',', $skip_first = false)
{
    $eol = detectEOL($input_file);
    $encoding = checkEncoding($input_file, $delimiter_delim) ? 'UTF-8' : 'Windows-1251';
    $row = 0;
    $columns = countColumns($input_file, $delimiter_delim);
    ini_set('auto_detect_line_endings', true);
    if (($handle = fopen($input_file, "r")) !== false) {
        $handle2 = fopen($output_file, "w+");
        while (($data = fgetcsv($handle, 1000, $delimiter_delim)) !== false) {
            if (count($data) != $columns) {
                if ($data[0] == null && count($data) == 1) {
                    continue;
                }
                throw new Exception('Неверные данные в исходном файле!');
            }
            foreach ($configs as $key => $value) {
                if ($skip_first) {
                    $skip_first = false;
                    fputcsvEol($handle2, $data, $eol, $delimiter_delim);
                    $row++;
                    continue;
                }
                $data[$key] = mb_convert_encoding(is_callable($value) ? $value($data[$key], $data, $row, $faker) :
                    ($value ? $faker->$value() : $value), $encoding);
            }
            fputcsvEol($handle2, $data, $eol, $delimiter_delim);
            $row++;
        }
        fclose($handle);
        fclose($handle2);
    }
}

//Проверяет кодировку. Если кодировка UTF-8, то возвращает true, иначе - false
function checkEncoding($input, $delimiter)
{
    if (($testHandle = fopen($input, "r")) !== false) {
        $testData = fgetcsv($testHandle, 1000, $delimiter);
        return mb_check_encoding(implode('', $testData), 'UTF-8');
    }
}

//Функция определяет окончание строки в исходном файле
function detectEOL($input)
{
    if (($fo = fopen($input, "r")) !== false) {
        $row = fgets($fo);
        $eol = substr($row, -2);
        if ($eol == "\r\n") {
            return "\r\n";
        } elseif ($eol == "\n\r") {
            return "\n\r";
        } elseif (substr($eol, -1) == "\n") {
            return "\n";
        }
        return PHP_EOL;
    }
}

//Функция fputcsv с параметром end of line (окончание строки)
function fputcsvEol($fp, $array, $eol, $delimiter = ',', $enclosure = '"', $escape_char = "\\")
{
    if (!fputcsv($fp, $array, $delimiter, $enclosure, $escape_char)) {
        throw new Exception('При попытке записи произошла ошибка!');
    }
    if ("\n" != $eol && 0 === fseek($fp, -1, SEEK_CUR)) {
        fwrite($fp, $eol);
    }
}


function countColumns($input, $delimiter)
{
    if (($countHandle = fopen($input, "r")) !== false) {
        $data = fgetcsv($countHandle, 1000, $delimiter);
        return count($data);
    }
}
