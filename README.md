# CSV Editor 
CSV Editor - это консольная программа для преобразования данных в файле формата csv. Данные изменяются в  
соответствии с конфигурационным файлом. Программа принимает на вход 3 обязательных параметра.  
Первый - путь до исходного csv-файла с данными, второй - путь до конфигурационного файла, в котором определено,  
в каком столбце и по какой схеме заменять значения. Третий - путь до файла для сохранения результата.  
Результат работы - csv-файл с тем же форматированием, что и исходный.  
```
-i|--input file - путь до исходного файла
-c|--config file - путь до файла конфигурации
-o|--output file - путь до файла с результатом
-d|--delimiter delim - задать разделитель (по умолчанию “,”)
--skip-first - пропускать модификацию первой строки исходного csv
--strict - проверять, что исходный файл содержит необходимое количество описанных в конфигурационном
файле столбцов. При несоответствии выдавать ошибку.
-h|--help - вывести справку
```

# Установка:   
(для установки необходим composer)  
1. Скопировать файлы в произвольную папку и перейти в эту папку  
    ```
    git clone https://github.com/kornborn/csv_editor DirectoryName
    cd DirectoryName
    ```
2. Установить зависимости
    ```
    composer install
    ```
3. Пример запуска программы (используются тестовые файлы, результат записывается в output.csv):
    ```
    php script.php -i tests/files/test_utf-8.csv -c tests/files/test_conf.php -o output.csv
    ```
Результат будет записан в файл output.csv.

4. Для запусков тестов необходимо перейти в папку tests и выполнить команду запуска тестов:
    ```
    cd tests
    make test
    ```
