<?
include 'AbstractQueryBuilder.php';
include 'SelectQueryBuilder.php';

$query =new \stradivari\query_builder\SelectQueryBuilder();

$query2 = clone $query;
$query2->from = 'ololo_table';

$query->fields['count(*)'] = 'field_count';
$query->fields['ololo.trololo'] = 'trololo';
$query->from = 'some_table';
$query->join($query2, 'ololo', array('or' => 
    array(
        'ololo.Ref = some_table.Ref',
        'and' => array(
            'ololo.kkk = some_table.Ref',
            'ololo.kkk is not Null'
        )
    )
));
$query->limit = 1;
$query->groupBy = array('lil');
$query->having = array('count(*) > 2', 'count(*) < 1');

var_dump((string)$query); die();