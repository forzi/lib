<?
//подключить Yaml parser
require_once('parserYaml/parserYaml.php');

//получить текст в yaml формате
$yaml = file_get_contents('template.yaml');

//создать объект для работы с файлом
$parser = new YamlToArray();

//конвертирование данных в массив
$data = $parser->fileParseToArray($yaml);

//просто вывести на экран представление Yaml файла в виде массива
$parser->printYamlAsArray($yaml);

