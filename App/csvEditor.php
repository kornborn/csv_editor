<?php

//Считывание, изменение и запись в новый файл строк
function csvEditor($input_file, $output_file, $delimiter_delim, $configs, $skip_first, $faker)
{
    $row = 0;
    if (($handle = fopen($input_file, "r")) !== false) {
        $handle2 = fopen($output_file, "w+");

        while (($data = fgetcsv($handle, 1000, $delimiter_delim)) !== false) {
            foreach ($configs as $key => $value) {
                if ($skip_first) {
                    $skip_first = false;
                    continue;
                }
                //            var_dump(mb_detect_encoding(implode('', $data), array('utf-8', 'cp1251')));
                //            if($row == 0) {
                //                $encoding = mb_detect_encoding(implode('', $data), array('utf-8', 'cp1251'));
                //            }
                $data[$key] = is_callable($value) ? $value($data[$key], $data, $row, $faker) :
                    ($value ? $faker->$value() : $value);
            }
            //        fputcsv_eol($handle2, $data, $eol, $delimiter = ',', $enclosure = '"', $escape_char = "\\")
            fputcsv($handle2, $data, $delimiter_delim);
            $row++;
        }
        fclose($handle);
        fclose($handle2);
    }
}