<?
//���������� Yaml parser
require_once('parserYaml/parserYaml.php');

//�������� ����� � yaml �������
$yaml = file_get_contents('template.yaml');

//������� ������ ��� ������ � ������
$parser = new YamlToArray();

//��������������� ������ � ������
$data = $parser->fileParseToArray($yaml);

//������ ������� �� ����� ������������� Yaml ����� � ���� �������
$parser->printYamlAsArray($yaml);

