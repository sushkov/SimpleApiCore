## SimpleApiCore - модуль для быстрого создания API на PHP
Методы разделяются по группам для удобства использования (например, `auth.signIn`, группа  - `auth`, метод - `signIn`).
Есть возможность проверки целостности данных в запросе с использованием подписи.
#### Общий вид url запроса
`<domain>/api/method/<method_name>?<parameters>`
#### Общий вид ответа
Ответ от сервера в формате JSON.
```json
{response: ...} //корректный ответ
{error: {code: ..., description: ...}} //ошибка
```
#### Варианты ответа
```json
{response: {count: ..., items: [...]}} //возвращается список объектов
{response: {success: ...}} //возврат статуса, значение 0 или 1
{response: ...} //возврат произвольных данных
```
#### Общий вид ошибок
```json
{"error": ..., "description": ...} //error - код ошибки, description - описание
```
### Пример использования
**1.** Создаем `index.php` - точка входа для всех запросов (настроить, например, в `.htaccess` для apache):
```php
<?php
    include_once("lib/SimpleApiCore/SimpleApiCore.php");
    $api_config = array(
        $_SERVER["DOCUMENT_ROOT"], //рабочая директория для файлов API
        "methods", //имя директории с методами (по умолчанию - "methods")
        array("test"), //список имен груп методов API
        "test_secret" //секретная строка для подписывания запросов
    );
    $api_core = new SimpleApiCore($api_config);
    $api_core->start();
?>
```
**2.** Создаем директорию `methods` (или которую задали в конфиге). Добавляем файлы по названию групп методов (`my_methods_group.php`). Пишем свой класс для группы методов, наследуем от `ApiMethodsBase`:
```php
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
```
#### Встроенные коды ошибок
Код|Описание|Комментарий
:-:|:-:|:-:
1|Access denied|Нет доступа
2|Method does not exist|Метод не существует
3|Required parameters of method|Требуются параметры метода
4|Required parameter|Требуется параметр (указать какой)
5|Incorrect parameter|Неправильный параметр метода
6|Incorrect signature|Неверная подпись запроса
7|Internal server error|Ошибка сервера
8|Required signature|Требуется подпись запроса
#### Пример генерации ошибок
```php
<?php
    /*
    * $code - код ошибки
    * $type - тип ошибки (по умолчанию "common" - общие ошибки описанные выше)
    * $message - дополнительный текст к описанию ошибки
    *
    * throw new ApiError($code, $type, $message);
    */

    // Генерируем ошибку "Нет доступа"
    throw new ApiError(1);

    // Добавим дополнительный текст к ошибке, чтобы получить "Нет доступа к базе данных"
    throw new ApiError(1, "common", "к базе данных");

    // Сгенерируем ошибку с описанием "Required parameter email"
    throw new ApiError(4, "common", "email");

    // Генерируем свою ошибку с заданным кодом и описанием
    throw new ApiError("my_error_code_1", "user", "my_error_description");
?>
```
### Пример вычисления подписи запроса
**1.** Исходная строка (запрос на авторизацию):<br>
`auth.signIn?login=user&password=qwerty`<br>
**2.** Вычисление подписи:<br>
`s = sha1("auth.signIn?login=user&password=qwerty" + client_secret)`<br>
**3.** Url с подписью:<br>
`app.com/api/method/auth.signIn?login=user&password=qwerty&s=...`