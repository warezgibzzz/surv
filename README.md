### Опросы

symfony 6.x + encore + stimulus

# Требования

* PHP 8.1 (ext-pdo ext-pdo-pgsql ext-intl)
* Postgres 13.x+
* Nodejs 16.x+
* Yarn 1.x

# Запуск проекта
Настроить конфиг
```shell
cp .env .env.local
```
Поправить переменные APP_SECRET и DATABASE_URL в .env.local


Команды выполняются в папке проекта

```shell
composer install
yarn install
yarn build
```

Создание базы (пропускаем этот блок если база уже есть)
```shell
bin/console doctrine:database:create
```
Накатываем миграции
```shell
bin/console doctrine:migrations:migrate
```
Запускаем локальный сервак
```shell
php -S 0.0.0.0:8000 -t ./public
```