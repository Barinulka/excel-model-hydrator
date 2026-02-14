# Excel Model Hydrator

Минимальная инструкция для локального запуска.

## Требования

- Docker + Docker Compose
- PHP `>=8.4`
- Composer
- Symfony CLI (опционально, если хотите запускать встроенный сервер командой `symfony server:start`)

## Локальный запуск

1. Установить PHP-зависимости:

```bash
composer install
```

Команда `composer install` запускает auto-scripts (включая `assets:install` и `ckeditor:install`).

2. Поднять контейнеры (Go hydrator + PostgreSQL):

```bash
docker compose up -d --build
```

3. Убедиться, что в `.env` или `.env.local` для локального запуска Symfony используется порт `6101`:

```dotenv
DATABASE_URL="postgresql://app:app@127.0.0.1:6101/app?serverVersion=16&charset=utf8"
```

4. Запустить Symfony-приложение:

```bash
symfony server:start -d
```

Если Symfony CLI не установлен:

```bash
php -S 127.0.0.1:8000 -t public
```

Важно: веб-сервер должен обслуживать директорию `public` как document root.

## Проверка

- Приложение: `http://127.0.0.1:8000/`
- Go-сервис (контейнер): `http://127.0.0.1:8081`
- Пример POST-запроса в Symfony:

```bash
curl -X POST http://127.0.0.1:8000/excel \
  -H "Content-Type: application/json" \
  -d '{"sheetData":{"Input":{"H161":9999}}}'
```

## Полезные команды

```bash
docker compose ps
docker compose logs -f excel-hydrator database
docker compose down
symfony server:stop
```

## CKEditor и ассеты

Если в админке CKEditor отображается как обычный `textarea` или в консоли есть `404` на
`/bundles/fosckeditor/*`, переустановите ассеты:

```bash
php bin/console assets:install public
php bin/console cache:clear
```

Проверьте, что файлы существуют:

```bash
ls -la public/bundles/fosckeditor
```
