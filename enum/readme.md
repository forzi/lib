stradivari/enum
====

*All examples you can also see in the examples.php file

1 Simple decorator
```php
	$businesses = array (
		0 => array ( '_id' => '5226f986646d627f050002ad', 'name' => 'Иванов Максим Сергеевич', 'kpgf_entry' => 0, 'kpgf_title' => 'ФОП', 'business_type' => 'individual', 'shared' => '0', ),
		1 => array ( '_id' => '52302078646d62d105000006', 'name' => 'Aaaaaaaas Ssss Ddddddd', 'kpgf_entry' => 0, 'kpgf_title' => 'ФОП', 'business_type' => 'individual', 'shared' => 0, 'block' => 1378760400, ),
		2 => array ( '_id' => '523033da646d62071d000016', 'name' => 'тестттттт', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', 'shared' => 0, 'block' => 1378903272, ),
		3 => array ( '_id' => '5230673b646d62cf05000014', 'name' => 'Testoviy', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', 'shared' => 0, ),
		4 => array ( '_id' => '52315cb9646d62ae05000005', 'name' => 'Третяков и Ко', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', ),
	);
	$data = array(
		'entity' => &$businesses, //don't forgot "&" - it can save a lot of memory
	);
	$enum = new \stradivari\enum\Enum($data);
```
And now `$enum[0]['_id'] == $businesses[0]['_id']`
You can't cnange data in $enum like `$enum[0]['_id'] = 'lol'`, becaus it's a decorator
If you change data in $businesses - it will change $enum data too, becaus it's a decorator

2 Extended

Decorator can add some functions to decorated Entity, which will works like fields:
```php
	$businesses = array (
		0 => array ( '_id' => '5226f986646d627f050002ad', 'name' => 'Иванов Максим Сергеевич', 'kpgf_entry' => 0, 'kpgf_title' => 'ФОП', 'business_type' => 'individual', 'shared' => '0', ),
		1 => array ( '_id' => '52302078646d62d105000006', 'name' => 'Aaaaaaaas Ssss Ddddddd', 'kpgf_entry' => 0, 'kpgf_title' => 'ФОП', 'business_type' => 'individual', 'shared' => 0, 'block' => 1378760400, ),
		2 => array ( '_id' => '523033da646d62071d000016', 'name' => 'тестттттт', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', 'shared' => 0, 'block' => 1378903272, ),
		3 => array ( '_id' => '5230673b646d62cf05000014', 'name' => 'Testoviy', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', 'shared' => 0, ),
		4 => array ( '_id' => '52315cb9646d62ae05000005', 'name' => 'Третяков и Ко', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', ),
	);
	$data = array(
		'entity' => &$businesses,
		'functions' => array(
			'is_legal' => function($value) {
				return isset( $value['kpgf_entry'] ) ? $value['kpgf_entry'] > 0 : false;
			},
		),
	);
	$enum = new \stradivari\enum\Enum($data);
```
And now `$enum[0]['is_legal']` will return value of function 'is_legal'. Functional programming can be useful :)


3 Overloaded

Also you can fully overload some fields, like this:
```php
	$businesses = array (
		0 => array ( '_id' => '5226f986646d627f050002ad', 'name' => 'Иванов Максим Сергеевич', 'kpgf_entry' => 0, 'kpgf_title' => 'ФОП', 'business_type' => 'individual', 'shared' => '0', ),
		1 => array ( '_id' => '52302078646d62d105000006', 'name' => 'Aaaaaaaas Ssss Ddddddd', 'kpgf_entry' => 0, 'kpgf_title' => 'ФОП', 'business_type' => 'individual', 'shared' => 0, 'block' => 1378760400, ),
		2 => array ( '_id' => '523033da646d62071d000016', 'name' => 'тестттттт', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', 'shared' => 0, 'block' => 1378903272, ),
		3 => array ( '_id' => '5230673b646d62cf05000014', 'name' => 'Testoviy', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', 'shared' => 0, ),
		4 => array ( '_id' => '52315cb9646d62ae05000005', 'name' => 'Третяков и Ко', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', ),
	);
	$data = array(
		'entity' => &$businesses,
		'functions' => array(
			'name' => function($value) {
				return isset($value['name']) ? mb_strtolower(trim($value['name']), 'UTF-8') : null;
			},
		),
	);
	$enum = new \stradivari\enum\Enum($data);
```
And now `$enum[0]['name']` will return value of function 'name' instead of field 'name'



4 Indexation, Filtering and Sorting

4.1. Simple index
```php
	$businesses = array (
		0 => array ( '_id' => '5226f986646d627f050002ad', 'name' => 'Иванов Максим Сергеевич', 'kpgf_entry' => 0, 'kpgf_title' => 'ФОП', 'business_type' => 'individual', 'shared' => '0', ),
		1 => array ( '_id' => '52302078646d62d105000006', 'name' => 'Aaaaaaaas Ssss Ddddddd', 'kpgf_entry' => 0, 'kpgf_title' => 'ФОП', 'business_type' => 'individual', 'shared' => 0, 'block' => 1378760400, ),
		2 => array ( '_id' => '523033da646d62071d000016', 'name' => 'тестттттт', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', 'shared' => 0, 'block' => 1378903272, ),
		3 => array ( '_id' => '5230673b646d62cf05000014', 'name' => 'Testoviy', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', 'shared' => 0, ),
		4 => array ( '_id' => '52315cb9646d62ae05000005', 'name' => 'Третяков и Ко', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', ),
	);
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
	echo "kpgf: {$enum->kpgf->count()}\n<br />"; // Method count() returns count of indexed fields (c.o.)
	foreach ( $enum->kpgf as $key => $val ) {
		d($key, 0);
		d($val, 0);
		echo '<br />';
	}
```
$enum will have reference to the $businesses, but `$enum->kpgf` - is simle index, which was created with `$enum` field 'kpgf_entry'. Elements will be groped by that field and your can take any groupe (list of Entities) like this: `$enum->kpgf[240]`

4.2. Unique index

Unique index works like simple index, but returns only one Entity instead of list.
Also you can create any index after object creation.
```php
	// Continuation previous example
	$enum->id = array('type' => 'unique', 'field' => '_id'); // Create index after object creation
	echo "id: {$enum->id->count()}\n<br />";
	foreach ( $enum->id as $key => $val ) {
		d($key, 0);
		d($val, 0);
		echo '<br />';
	}
```

4.3. Sort by index
```php
	// Continuation previous example
	$enum->name = array('type' => 'unique', 'field' => 'name');
	echo "name: {$enum->id->count()}\n<br />";
	foreach ( $enum->name as $key => $val ) {
		d($key, 0);
		#d($val, 0);
		echo '<br />';
	}

	$enum->name->ksort('desc'); // Use method ksort. Params: asc|desc. By default - asc
	echo "name: {$enum->id->count()}\n<br />";
	foreach ( $enum->name as $key => $val ) {
		d($key, 0);
		#d($val, 0);
		echo '<br />';
	}
```
The string compare function is stradivari\enum\AbstractSort::$compareString and can be reloaded.
Actually, this function extended standart php function "strnatcmp".

4.4. Sort index (Multikey sort)

Can sort Entities by many fields and orders.
```php
	$businesses = array (
		0 => array ( '_id' => '5226f986646d627f050002ad', 'name' => 'Иванов Максим Сергеевич', 'kpgf_entry' => 0, 'kpgf_title' => 'ФОП', 'business_type' => 'individual', 'shared' => '0', ),
		1 => array ( '_id' => '52302078646d62d105000006', 'name' => 'Aaaaaaaas Ssss Ddddddd', 'kpgf_entry' => 0, 'kpgf_title' => 'ФОП', 'business_type' => 'individual', 'shared' => 0, 'block' => 1378760400, ),
		2 => array ( '_id' => '523033da646d62071d000016', 'name' => 'тестттттт', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', 'shared' => 0, 'block' => 1378903272, ),
		3 => array ( '_id' => '5230673b646d62cf05000014', 'name' => 'Testoviy', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', 'shared' => 0, ),
		4 => array ( '_id' => '52315cb9646d62ae05000005', 'name' => 'Третяков и Ко', 'kpgf_entry' => 240, 'kpgf_title' => 'ТОВ', 'business_type' => 'legal_entity', ),
	);
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
	unset($enum->functions['name']); // You can also sets/unsets functions after object creation. Becaus index already created - order will not be changed.
	$businesses[0]['name'] = 'lol'; // Becaus index already created - order will not be changed in this case too.
	echo "sortMultikey: {$enum->sortMultikey->count()}\n<br />";
	foreach ( $enum->sortMultikey as $key => $val ) {
		d($key, 0);
		d($val, 0);
		echo '<br />';
	}
```
