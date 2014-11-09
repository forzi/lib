h1. Подключение шрифта для использования в PhantomJS

h2. Подключение шрифтов

при выводе на печать подменять
<pre>
<link href!='/www/assets/bs/css/fonts.css', rel='stylesheet', media='all'>
</pre>
на следующее
<pre>
<link href!='/www/assets/bs/css/fonts-phantomjs.css', rel='stylesheet', media='all'>
                                                                               ^ при указании верного значения 'print' шрифт не загрузится :(
</pre>

h2. Как подключить шрифт в header и footer страниц PDF-документа, генерируемого PhantomJS ?

Никак.
Вы можете использовать только шифты, подключенные на генерируемой странице. Но способ несколько отличается:
*нельзя просто так взять и вызвать файл по определенному ранее в правиле css 'font-family: 'Roboto'*.
Необходимо использовать значение Preferred Family из ttf-шрифта.
<pre>
<div style="width:100%;
	font-family: 'Roboto PDF Light';
	font-size:6pt;
	color: black;
">
</pre>

h2. Матчасть

ТТF-файлы имеют, среди прочих, такие интересующие нас параметры: Family, Preferred Family, Preferred Style, Compatible Full

Рассмотрим их значения на примере разных начертаний шрифта Roboto. например, Light и Bold :
* имя файла : roboto-light.ttf / roboto-bold.ttf
* Family : Roboto Light / Roboto Bold
* *Preferred Family : Roboto / Roboto*
* Preferred Style : Light / Bold
* Compatible Full : Roboto Light / Roboto Bold

------------------

*Использовать системные шрифты при рендере ЗАПРЕЩЕНО!*

Шрифты необходимо брать из проекта, сначала СОГЛАСОВАТЬ с начальством, а потом только ЗАГРУЖАТЬ на страницу. (и так каждый раз :))
Прененбрежение этим правилом грозит созданнию PDF-документа с шрифтами без кернинговых пар, без начертаний для кириллицы, без сглаживаний и без выходных.

Даже если на машине произойдет корректная отрисовка документа, шрифт может быть _немного_ другим. Так, версия Roboto, используемая в проекте Смарт и Справка ОТЛИЧАЕТСЯ от текущей на сервере Гугла.

h2. Особенность работы PhantomJS

Допустим, вы подключаете два шрифта -- тоненький и жирненький:

<pre>
  @font-face {
    font-family: 'Roboto';
    src: url('path/to/font/roboto-light.ttf') format('truetype');
    font-weight: 300;
    font-style: normal;
  }
  @font-face {
    font-family: 'Roboto';
    src: url('path/to/font/roboto-bold.ttf') format('truetype');
    font-weight: 700;
    font-style: normal;
  }
</pre>

результатом будет подключение и использование только ПЕРВОГО шрифта (несмотря на то, что начертание типа жирного текста может быть действительно жирнее в результате примененного правила "font-weight: 600;"). Ситуацию не изменит даже указание различных свойств 'font-family' как 'Roboto-light' и 'Roboto-bold'.

ПРИЧИНА: как оказалось, PhantomJS различает шрифты не по имени файла(src) или задаваемому имени шрифта(font-family), а по свойствам внутри ТТF-файла, а точнее, по полю Preferred Family

h2. Что же делать?

Разумеется, переопределить уникальные значения для каждого файла начертания.
Применение для этой цели различных редакторов шрифтов недопустимо, т.к. при сохранении они пересобирают весь шрифт, при этом теряются существующие кернинговые пары.

Для решения поставленной задачи возможно использовать утилиты организации SIL International. Инструкции по сборке, установке и использованию -- http://scripts.sil.org/FontUtils

Нас интересует утилита *ttfname* -- переименовывает шрифт и сохраняет его в новый файл без пересборки.
Выполним, например:
<pre>
ttfname -n "[новое уникальное имя начертания]" fontfile.ttf [новое уникальное имя файла].ttf
</pre>

Сравним параметры ранее рассмотренных файлов после подобной обработки:
* имя файла : roboto-light.ttf / roboto-bold.ttf
* Family : Roboto PDF Light / Roboto PDF Bold
* *Preferred Family : Roboto PDF Light / Roboto PDF Bold*
* Compatible Full : Roboto PDF Light / Roboto PDF Bold
