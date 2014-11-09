h1. phantomJS-сервис генерации pdf и изображений

h2. Внимание!

для использования локального render-smart в проекте smart изменить в файле /application/config/app.php значение 
<pre>
$render_domain = getenv('RENDER_APP_DOMAIN') ?: 'render.smart.dev';
</pre>
на *render.smart.lan*

перед финальным коммитом в ветке не забудьте вернуть прежнее значение!

h2. Install

Поместить composer.json в нужную папку
Выполнить:
sudo apt-get update
sudo apt-get install build-essential chrpath git-core libssl-dev libfontconfig1-dev
curl -sS https://getcomposer.org/installer | php
COMPOSER_PROCESS_TIMEOUT=3000 php ./composer.phar install
sudo chmod 777 vendor/scs/render-smart/tmp -R
sudo chmod 777 vendor/scs/render-smart/render -R

h2. 1. Архитектура

# Поднимаем новый сервис: nginx + phantomJS (как вариант - nginx + php + phantomJS)
# Сервис принимает на входе post-параметрами html + ресурсы (css, картинки ...)
# nginx вычисляет хеш из url, если есть кеш по этому хешу - возвращает его, иначе:
# Складывает ресурсы во временные файлы
# Вызывает phantomJS и передаёт ему в качестве параметров пути ко временным файлам
# phantomJS генерирует pdf и возвращает его nginx
# nginx кеширует его с уже известным хешем и возвращает pdf

h2. 2. API запроса

get-запрос:
*type*: pdf | gif | jpg | jpeg | png
*no_сache*: 1 | 0 // Не использовать кеширование на стороне рендера, 0 by default
*url*: <url>
*format*: <'A3' | 'A4' | 'A5' | 'Legal' | 'Letter' | 'Tabloid'> // 'A4' by default
*orientation*: < portrait | landscape > // portrait by default

post-запрос:
*type*: pdf | gif | jpg | jpeg | png
*no_сache*: 1 | 0 // Не использовать кеширование на стороне рендера, 0 by default
*params*: <json_encode>
<pre>
{
	body: <b64_content> | url: '<url>',
	parameters: {
		'viewportSize': {width: <...>, height: <...>},
		'paperSize': {
			width: <...>,
			height: <...>,
			format: < 'A3' | 'A4' | 'A5' | 'Legal' | 'Letter' | 'Tabloid' >,
			orientation: < portrait | landscape >,
			border: <...>,
			margin: <...> | {top: <...>, left: <...>, right: <...>, bottom: <...>}
		},
		'clipRect': {top: <...>, left: <...>, width: <...>, height: <...>},
		'zoomFactor': 0.92  // определено эмпирическим путем 
	},
	header: {
		height: <...>,
		first: <b64_content>,
		main: <b64_content>,
		even: <b64_content>,
		odd: <b64_content>
	},
	footer: {
		height: <...>,
		first: <b64_content>,
		main: <b64_content>,
		even: <b64_content>,
		odd: <b64_content>
	},
	resources: {
		<file_name1>: <b64_content>, 
		<file_name2>: <b64_content>, 
		...
	}
}
</pre>

h2. 3. Суперглобальные переменные

*##files_path##* - Все локальные пути к файлам должны быть заменены на эту переменную: ##files_path##1.jpg, ##files_path##main.css ...

При необходимости вставить номера страниц в *footer* или *header*:
*##current_page##* - текущая страница
*##total_pages##* - всего страниц

h2. 4. Пример работы с сервисом из системы smart:

<pre>
public function test_render()
{
	$content = file_get_contents('/home/user/projects/render-smart/test/body.htm');
	$type = 'pdf';
	
	$this->load->library('Curl_Adapter');
	$curl_lib = $this->curl_adapter;
	
	$params = array(
		'body' => base64_encode(file_get_contents('/home/user/projects/render-smart/test/body.htm')),
		'footer' => array(
			'main' => base64_encode(file_get_contents('/home/user/projects/render-smart/test/footer.htm')),
		),
		'resources' => array(
			'default_mpdf.css' => base64_encode(file_get_contents('/home/user/projects/render-smart/test/default_mpdf.css')),
			'document_pdf.css' => base64_encode(file_get_contents('/home/user/projects/render-smart/test/document_pdf.css')),
			'fonts.css' => base64_encode(file_get_contents('/home/user/projects/render-smart/test/fonts.css')),
			'logo.jpg' => base64_encode(file_get_contents('/home/user/projects/render-smart/test/logo.jpg'))
		),
		'parameters' => array(
			'viewportSize' => array(
				'height' => 800,
				'width' => 600,
			),
			'paperSize' => array(
				'format' => 'A4',
				'orientation' => 'portrait'
			)
	   ),
	);
	$params = json_encode($params);
	$curl_params = array(
		'url' => 'http://render.smart.lan',
		'headers' => array(),
		'options' => array(
			'type' => $type,
			'params' => $params
		),
		'timeout' => 10
	);
	$result = $curl_lib->post($curl_params);
	
	
		#echo '<pre>';
		#d($result);
	
	
	$this->_throw_file(array('filename' => '1.' . $type), $result['response']['body']);
}
