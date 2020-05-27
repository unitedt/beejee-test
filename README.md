# beejee-test

## Test task for BeeJee

Выбрал [RoadRunner](https://github.com/spiral/roadrunner) в качестве сервера приложений, так как не хотелось поднимать
"классическую" инфраструктуру nginx + php-fpm и хотелось бы показать современное использование высокопроизвоизводителных
и масштабируемых решений на PHP.

По условиям задания, никакой фреймворк не использовался и бутстраппинг приложения и цикл обработки запросов описан в
двух файлах - [app/bootstrap.php](./app/bootstrap.php) и [app/psr-worker.php](./app/psr-worker.php) (точка входа 
приложения). Для упрощения реализации конфигурирование выполнено в основном файле bootstrap.php, в отдельные файлы 
конфига не выносилось. Аналогично работа с сессиями, запросами/ответами сервера и т.д. организована внутри воркера 
(выделять в отдельные классы подобно микро-ядру фреймворка в рамках тестового задания показалось излишним).

Применена парадигма MVC, по условиям тестового задания. Использован ряд библиотек, практически для всего, они 
организованы через контейнер внедрения зависимостей, таким образом, что несмотря на то, что не использован никакой
фреймворк за основу, мы всё равно имеем в результате не монолит, а практически очень легко масштабируемое и готовое
к интеграции в любую инфраструктуру production класса приложение.

Т.к. приложение построено по принципам [GRASP](https://ru.wikipedia.org/wiki/GRASP), особенно High Cohesion / Low 
Coupling, то основное время было затрачено на интеграцию компонентов, не всегда однозначную и отладку их взаимодействия.
Зато получилось вероятно меньше кода, чем в случае написания монолита и он куда более поддерживаемый, без "велосипедов".

Чистого времени ушло три вечера, или примерно чистых 12 часов. Монолит, возможно, был бы написан быстрее, но не лучше.
