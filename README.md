SimpleApiCore - модуль для быстрого создания API на PHP.
Методы разделяются по группам для удобства использования (например, auth.signIn, группа  - "auth", метод - "signIn").
Есть возможность проверки целостности данных в запросе с использованием подписи.

Общий вид url запроса:
<domain>/api/method/<method_name>?<parameters>

Ответ от сервера в формате JSON.
Общий вид ответа:
{response: ...} //корректный ответ
{error: {code: ..., description: ...}} //ошибка

Варианты ответа:
{response: {count: ..., items: [...]}} //возвращается список объектов
{response: {success: 0|1}} //возврат статуса
{response: ...} //возврат произвольных данных

Общий вид ошибок:
{"error": <error_code>, "description": <error_description>}

Пример использования:
1. Создать index-файл - точка входа для всех запросов (настроить, например, в .htaccess для apache).
<?php
    include_once("lib/SimpleApiCore/SimpleApiCore.php");
    $api_config = array(
        $_SERVER["DOCUMENT_ROOT"], //рабочая директория для файлов API
        "methods", //имя директории, в которой будут храниться файлы с методами (по умолчанию - "methods")
        array("test"), //список имен груп методов API
        "test_secret" //секретная строка для подписывания запросов
    );
    $api_core = new SimpleApiCore($api_config);
    $api_core->start();
?>

2. Создать директорию methods (или которую задали в конфиге). Добавлять файлы по названию групп методов (my_methods_group.php).
Пишем свой класс для группы методов, наследуем от ApiMethodsBase:
<?php
    include_once("/path_to_lib/SimpleApiCore/ApiMethodsBase.php");
    class my_methods_group extends ApiMethodsBase {
        function __construct(){
            /*
             * $methods_list - массив конфигураций методов
             * name - имя метода
             * s - true или false, требование проверки подписи запроса
             */
            parent::$methods_list = array(["name" => "my_method"/*, "s" => true*/] /* другие методы */);
            parent::__construct();
        }

        function my_method(/* $request */){
            //Параметры запроса доступны через $request

            /*
             * Код метода
             */
        }
    }
?>

Пример вычисления подписи запроса:
1. исходная строка (запрос на авторизацию):
auth.signIn?login=user&password=qwerty
2. вычисление подписи:
s = sha1("auth.signIn?login=user&password=qwerty" + client_secret)
3. url с подписью:
http://app.com/api/method/auth.signIn?login=user&password=qwerty&s=...