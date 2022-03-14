## tourist_game - Навигатор целей для мотопоездок


## Запуск проекта
 - Перед запуском нужно установить PHP7.4+, composer, docker, docker-compose, symfony
 - Просим у коллег .env, копируем его в корень проекта
 - Запускаем контейнер (с PostgreSQL) `sudo docker-compose up`
 - Устанавливаем пакеты `composer install`
 - Генерируем сертификаты для шифрования (проверить, что данные перезаписали .env):
      `php bin/console lexik:jwt:generate-keypair`
  - Подготовка БД
    - Накатываем миграции `bin/console doctrine:migrations:migrate`
    - Генерируем тестовые данные `bin/console doctrine:fixtures:load`
  - Запускаем веб-сервер `symfony server:start`
  - устанавливаем js пакеты `yarn install`  
  - Генерим js `yarn watch`


### Команда для проверки работы сервера
`curl -X POST -H "Content-Type: application/json" http://localhost:8000/api/login_check -d '{"username":"admin","password":"idy27ah_*nzn"}'`
Сервер вернет json с токеном. Этот токен нужно отсылать вместе с запросами к бэкенду в хедере "Authorization" в виде "Bearer значение_токена" 