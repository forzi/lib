<?
require_once __DIR__ . "/../../autoload.php";

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
	$result = stradivari\curl\Curl::post(
		array(
			// 'Cookie' is a header too
			// 'Host' and 'Accept' will be added automatically, but you can set them
			// Redirects doesn't works automatically
			'headers' => array(
				'Accept'	=>	'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
				'Accept-Encoding'	=>	'gzip, deflate',
				'Accept-Language'	=>	'en-US,en;q=0.5',
				'Connection'	=>	'keep-alive',
				'Cookie'	=>	'Session_id=noauth:1389798047; yandexuid=4826261311378107166; my=YycCAAEoAYCOAA==; fuid01=52243f22410d31a0.W5-8Crv0uYKVyjfT8qfW1cm9vYWcsqfoIdC-iPDRtgEZ5F5LMiJEmeDSM7MHC7UKHJD7PUKRrTT6eGUx0Llnv6wqwgOlUXvZjN3bDb5MfuWsA-47jsaRW1qu0Yzp9IWs; yabs-frequency=/4/Cm400A5trb800000/hWO05mmN9wSY07WC5o6Z8W1u11TEczS85_WMl3mP2mYx5dm000C0/; z=m-desktop%3A1.774%3Al',
				//'Host'	=>	'yandex.ua',
				'User-Agent' =>	'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:26.0) Gecko/20100101 Firefox/26.0',
			),
			'url' => 'http://yandex.ru/page_1/string_2?ololo=1&trololo=2',
			'params' => 'para1=val1&para2=val2',
			'timeout' => 10
		)
	);
	d($result);
}

main_test();
