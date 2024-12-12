## Как запустить

- Запуск в Windows 10 22H2
    1. Установите PHP 8.3.11
    1. Установите в системную переменную Path путь до PHP (`D:\webbox\php83`), чтобы можно было вызывать php через консоль
    1. Перед запуском убедитесь, что существует файл `env.php`.
    1. Для того, чтобы работали include в PHP, установите переменную среды (`ENV_CRON_CPANEL_OZON_SELLER__HOME`) через свойства компьютера
        ```
        ENV_CRON_CPANEL_OZON_SELLER__HOME=D:/_git/dp_cpanel_cron__ozon_seller
        ```
    1. Запустите через cmd php скрипт
        ```
        php index.php
        ```

- В cPanel установите переменную среды через команду export сразу в команде CRON
    ```
    export ENV_CRON_CPANEL_OZON_SELLER__HOME=/home/user/_git/dp_cpanel_cron__ozon_seller && php index.php
    ```
