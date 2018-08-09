<?php

require_once '../App/csvEditor.php';

function makeCsvFile($file, $content = [], $encoding = 'UTF-8', $delimiter = ',', $eol = PHP_EOL, $enclosure = '"', $escape_char = "\\")
{
    $handle = fopen($file, "w+");
    foreach ($content as $row) {
        for ($i = 0; $i < count($row); $i++) {
            $row[$i] = mb_convert_encoding($row[$i], $encoding);
        }
        fputcsv_eol($handle, $row, $eol, $delimiter, $enclosure, $escape_char);
    }
    fclose($handle);
}
