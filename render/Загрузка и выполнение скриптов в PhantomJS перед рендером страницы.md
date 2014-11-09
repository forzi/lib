h1. Загрузка и выполнение скриптов в PhantomJS перед рендером страницы

h2. Как это сделать?

просто добавьте на страницу
<pre>
<script>
  var list_of_script_loading = {};
  
  list_of_script_loading.комментарий = 'путь к файлу/имя.js';
  list_of_script_loading.ещё_комментарий = 'путь к другому файлу/имя.js';

  if (typeof window.callPhantom === 'function') {
    var is_callback = window.callPhantom(list_of_script_loading);
  }
</script>
</pre>

h2. setTimeout и setInterval

эти функции, указанные в скриптах на стороне клиента (в странице),
работают только при условии, что аргумент времени *строго меньше* такового на сервере (в файле render.js).

h2. Как это работает?

вызываем на клиенте (cтраница):

<pre>
<script>
  var list_of_script_loading = { . . . };

  if (typeof window.callPhantom === 'function') {
    var is_callback = window.callPhantom(list_of_script_loading);
  }
</script>
</pre>

слушаем на сервере (Фантом, render.js):

<pre>
  var to_load = {};

  page.onCallback = function(data) {
    for (key in data) {
      to_load[key] = data[key];
    }
    return true;
  };

  page.open(url_to_page, function(arguments) {
      for (key in to_load) {
        console.log('LOAD: ' + to_load[key]);
        // including to page and execution
        page.includeJs(to_load[key]);
      }

      window.setTimeout(function () {
         page.evaluate(function() {
	      //you can put some code to execution
         });
     
         page.render(result_file);

      }, 200)

</pre>
