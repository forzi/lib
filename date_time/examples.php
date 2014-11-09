<?

	header('Content-Type: text/plain;');

	include_once __DIR__ . "/../../../autoload.php";
	use stradivari\date_time\DateTimeExtended as DTE;
	
	$test = new DTE(); // By default creates with current system timestamp and timezone
	echo "DTE default: {$test->format('Y-m-d H:i:s P')}\n";
	echo "\n";
	
	$test = new DTE('18.02.2014 18:17:00 -02:00', 'd.m.Y H:i:s P'); // Can work with custom value
	echo "DTE: {$test->format('Y-m-d H:i:s P')}\n";
	$test = new DTE(date_create_from_format('d.m.Y H:i:s P', '18.02.2014 18:17:00 -02:00')); // With std DateTime will works too 
	echo "With Std object: {$test->format('Y-m-d H:i:s P')}\n";
	echo "\n";
	
	$test = new DTE('18.02.2014 18:17:00', 'd.m.Y H:i:s'); // If timezone not set UTC timezone will use by default
	echo "DTE default timezone: {$test->format('Y-m-d H:i:s P')}\n";
	$test = new DTE(date_create_from_format('d.m.Y H:i:s', '18.02.2014 18:17:00')); // std DateTime use system timezone by default
	echo "Std timezone: {$test->format('Y-m-d H:i:s P')}\n";
	echo "\n";
	
	#Attributes:
	echo "Attributes: \n";
	echo "timestamp: " . $test->timestamp . " \n"; #Unix timestamp in seconds
	echo "timestamp: " . $test . " \n"; #__toString returns Unix timestamp too
	echo "timestamp: " . $test->getTimestamp() . " \n"; #Std way to get timestamp 
	echo "\n";
	
	echo "timezone: " . $test->timezone . " \n"; #Timezone in format "00:00"
	echo "timezone: " . var_export($test->getTimezone(), 1) . " \n"; #Std way to get timezone as DateTimeZone object
	# You can change timezone:
	
	$timezone = 'America/Denver'; // You don't need creation DateTimeZone object
	$timezoneObject = new \DateTimeZone('+10:00'); // Byt you can
	$test->setTimezone($timezone); // Std way
	echo "new timezone: " . $test->format(DTE::BASE_FORMAT) . " \n"; #Timezone in format "00:00"
	$test->timezone = $timezoneObject;
	echo "newest timezone: " . $test->format(DTE::BASE_FORMAT) . " \n"; #Timezone in format "00:00"
	echo "\n";
	
	echo "offset: " . $test->offset . " \n"; #Timezone offset in seconds (readonly)
	echo "\n";
	
	echo "year: " . $test->year++ . " \n"; #Can be changed
	echo "new year: " . $test->year . " \n";
	echo "\n";
	
	echo "month: " . $test->month . " \n";  #Can be changed
	$test->month -= 13;
	echo "new month: " . $test->month . " \n";  #Can be changed
	echo "newest year: " . $test->year . " \n";
	echo "\n";
	
	echo "day: " . $test->day . " \n";  #Can be changed
	echo "hours: " . $test->hours . " \n";  #Can be changed
	echo "minutes: " . $test->minutes . " \n";  #Can be changed
	echo "seconds: " . $test->seconds . " \n";  #Can be changed
	echo "wday: " . $test->wday . " \n";
	echo "weekday: " . $test->weekday . " \n";
	echo "yday: " . $test->yday . " \n";
	echo "Number of current week: {$test->week} \n";
	echo "\n";
	
	echo "be ceafull with month changing: \n";
	$test->day = 31;
	$test->month += 1;
	echo $test->format(DTE::BASE_FORMAT);
	echo "\n";
	echo "\n";
	
	#Other usefull methods:
	echo "Other usefull methods: \n";
	echo "begin of day: {$test->beginOfDay()->format(DTE::BASE_FORMAT)} \n"; # By default creates new object
	echo "end of day: {$test->endOfDay()->format(DTE::BASE_FORMAT)} \n"; # By default creates new object
	echo "first day of month: {$test->firstDayOfMonth()->format(DTE::BASE_FORMAT)} \n"; # By default creates new object
	echo "last day of month: {$test->lastDayOfMonth()->format(DTE::BASE_FORMAT)} \n"; # By default creates new object
	echo "Count days in current month: {$test->daysInMonth()} \n";
	echo "Is current year leap (true/false): " . var_export($test->isLeapYear(), 1) . " \n";
	
	var_dump(DTE::validate('31.12.1994', 'd.m.Y'));
	var_dump(DTE::validate('32.12.1994', 'd.m.Y'));
	
	echo $test->lastDayOfQuarter(DTE::PREVIOUS)->format(DTE::BASE_FORMAT) . "\n";
	echo $test->firstDayOfQuarter(DTE::NEXT)->format(DTE::BASE_FORMAT) . "\n";
	