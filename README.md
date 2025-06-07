### Page Analyzer
Приложение на базе фреймворка Slim. Здесь отрабатываются базовые принципы построения современных сайтов на MVC-архитектуре: работа с роутингом, обработчиками запросов и шаблонизатором, взаимодействие с базой данных.

## Установка:
1. Клонируйте репозиторий проекта на локальное устройство:
```
git clone git@github.com:StanislavSol/pageAnalyzer.git
```

2. Перейдите в каталог проекта и установите зависимости с помощью Composer:
```
cd pageAnalyzer && make install
```
3. Создайте файл .env, который будет содержать ваши конфиденциальные настройки:
```
DATABASE_URL = postgresql://{user}:{password}@{host}:{port}/{db}
```
4. Дальше мы загружаем в базу наш sql-файл с таблицами:
```
export DATABASE_URL=postgresql://janedoe:mypassword@localhost:5432/mydb
psql -a -d $DATABASE_URL -f database.sql
```
## Использование
1. Чтобы запустить сервер, выполните команду:
```
make start
```
