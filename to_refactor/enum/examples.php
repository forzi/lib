<?
include_once __DIR__ . "/../../autoload.php";

if ( !function_exists('d') ) {
    function d($message = '', $break = 1) {
        if ( $message !== '' ) {
            echo '<pre>' . print_r($message, 1) . '</pre>' . "\n";
        }
        if ( $break ) {
            die();
        }
    }
}
function main_test() {
	$businesses = array ( 0 => array ( '_id' => '5226f986646d627f050002ad', 'name' => 'Иванов Максим Сергеевич', 'kpgf_entry' => 0, 'kpgf_title' => 'ФОП', 'business_type' => 'individual', 'shared' => '0', ), 1 => array ( '_id' => '52302078646d62d105000006', 'name' => 'Aaaaaaaas Ssss Ddddddd', 'kpgf_entry' => 0, 'kpgf_title' => 'ФОП', 'business_type' => 'individual', 'shared' => 0, 'block' => 1378760400, ), 2 => array ( '_id' => '523033da646d62071d000016', 'name' => 'тестттттт', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', 'shared' => 0, 'block' => 1378903272, ), 3 => array ( '_id' => '5230673b646d62cf05000014', 'name' => 'Testoviy', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', 'shared' => 0, ), 4 => array ( '_id' => '52315cb9646d62ae05000005', 'name' => 'Третяков и Ко', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', ), );
	$data = array(
		'entity' => &$businesses,
	);
	$enum = new \stradivari\enum\Enum($data);
	$businesses[0]['_id'] = 'lol';
	foreach ( $enum as $val ) {
		d($val, 0);
	}
}
function extended_test() {
	$businesses = array ( 0 => array ( '_id' => '5226f986646d627f050002ad', 'name' => 'Иванов Максим Сергеевич', 'kpgf_entry' => 0, 'kpgf_title' => 'ФОП', 'business_type' => 'individual', 'shared' => '0', ), 1 => array ( '_id' => '52302078646d62d105000006', 'name' => 'Aaaaaaaas Ssss Ddddddd', 'kpgf_entry' => 0, 'kpgf_title' => 'ФОП', 'business_type' => 'individual', 'shared' => 0, 'block' => 1378760400, ), 2 => array ( '_id' => '523033da646d62071d000016', 'name' => 'тестттттт', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', 'shared' => 0, 'block' => 1378903272, ), 3 => array ( '_id' => '5230673b646d62cf05000014', 'name' => 'Testoviy', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', 'shared' => 0, ), 4 => array ( '_id' => '52315cb9646d62ae05000005', 'name' => 'Третяков и Ко', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', ), );
	$data = array(
		'entity' => &$businesses,
		'functions' => array(
			'is_legal' => function($value) {
				return isset( $value['kpgf_entry'] ) ? $value['kpgf_entry'] > 0 : false;
			},
		),
	);
	$enum = new \stradivari\enum\Enum($data);
	echo "Original: {$enum->count()}\n<br />";
	#unset($enum->functions['is_legal']);
	foreach ( $enum as $key => $val ) {
		d($key, 0);
		d($val, 0);
		echo '<br />';
	}
}
function filter_test() {
	$businesses = array ( 0 => array ( '_id' => '5226f986646d627f050002ad', 'name' => 'Иванов Максим Сергеевич', 'kpgf_entry' => 0, 'kpgf_title' => 'ФОП', 'business_type' => 'individual', 'shared' => '0', ), 1 => array ( '_id' => '52302078646d62d105000006', 'name' => 'Aaaaaaaas Ssss Ddddddd', 'kpgf_entry' => 0, 'kpgf_title' => 'ФОП', 'business_type' => 'individual', 'shared' => 0, 'block' => 1378760400, ), 2 => array ( '_id' => '523033da646d62071d000016', 'name' => 'тестттттт', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', 'shared' => 0, 'block' => 1378903272, ), 3 => array ( '_id' => '5230673b646d62cf05000014', 'name' => 'Testoviy', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', 'shared' => 0, ), 4 => array ( '_id' => '52315cb9646d62ae05000005', 'name' => 'Третяков и Ко', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', ), );
	$data = array(
		'entity' => &$businesses,
		'keys' => array(
			0 => array(
				'name' => 'kpgf',
				'field' => 'kpgf_entry',
			),
		)
	);
	
	$enum = new \stradivari\enum\Enum($data);
	echo "kpgf: {$enum->kpgf->count()}\n<br />";
	foreach ( $enum->kpgf as $key => $val ) {
		d($key, 0);
		d($val, 0);
		echo '<br />';
	}
	
	$enum->functions['is_legal'] = function($value) {
		return isset( $value['kpgf_entry'] ) ? $value['kpgf_entry'] > 0 : false;
	};
	$enum->legal = array('type' => 'filter', 'field' => 'is_legal');
	echo "legal: {$enum->legal->count()}\n<br />";
	foreach ( $enum->legal as $key => $val ) {
		d($key, 0);
		d($val, 0);
		echo '<br />';
	}
	
	#unset($enum->functions['is_legal']);
	$enum->id = array('type' => 'unique', 'field' => '_id');
	echo "id: {$enum->id->count()}\n<br />";
	foreach ( $enum->id as $key => $val ) {
		d($key, 0);
		#d($val, 0);
		echo '<br />';
	}
	
	$enum->name = array('type' => 'unique', 'field' => 'name');
	echo "name: {$enum->id->count()}\n<br />";
	foreach ( $enum->name as $key => $val ) {
		d($key, 0);
		#d($val, 0);
		echo '<br />';
	}
	
	$enum->name->ksort('asc'); // "asc"|"desc", By default - "asc"
	echo "name: {$enum->id->count()}\n<br />";
	foreach ( $enum->name as $key => $val ) {
		d($key, 0);
		#d($val, 0);
		echo '<br />';
	}
}
function sort_test() {
	$businesses = array ( 0 => array ( '_id' => '5226f986646d627f050002ad', 'name' => 'Иванов Максим Сергеевич', 'kpgf_entry' => 0, 'kpgf_title' => 'ФОП', 'business_type' => 'individual', 'shared' => '0', ), 1 => array ( '_id' => '52302078646d62d105000006', 'name' => 'Aaaaaaaas Ssss Ddddddd', 'kpgf_entry' => 0, 'kpgf_title' => 'ФОП', 'business_type' => 'individual', 'shared' => 0, 'block' => 1378760400, ), 2 => array ( '_id' => '523033da646d62071d000016', 'name' => 'тестттттт', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', 'shared' => 0, 'block' => 1378903272, ), 3 => array ( '_id' => '5230673b646d62cf05000014', 'name' => 'Testoviy', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', 'shared' => 0, ), 4 => array ( '_id' => '52315cb9646d62ae05000005', 'name' => 'Третяков и Ко', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', ), );
	$data = array(
		'entity' => &$businesses,
		'functions' => array(
			'name' => function($value) {
				return isset($value['name']) ? mb_strtolower(trim($value['name']), 'UTF-8') : null;
			},
		),
	);
	$enum = new \stradivari\enum\Enum($data);
	$enum->sortMultikey = array('type' => 'sort', 'order' => array('name' => 'ASC', 'block' => 'ASC', 'kpgf_title' => 'asc'));
	#unset($enum->functions['name']);
	echo "sortMultikey: {$enum->sortMultikey->count()}\n<br />";
	foreach ( $enum->sortMultikey as $key => $val ) {
		d($key, 0);
		d($val, 0);
		echo '<br />';
	}
}

#main_test();
#extended_test();
filter_test();
#sort_test();
