# Markstore-project

## Быстрый запуск через Docker

1. Запустите проект:
   ```bash
   docker compose up --build
   ```
2. Откройте в браузере: `http://localhost:8080`

База данных создаётся автоматически из `database/init.sql`. Доступ к админке:

- Email: `admin@example.com`
- Пароль: `admin123`

## Локальный запуск без Docker

1. Поднимите MySQL и создайте базу:
   ```sql
   CREATE DATABASE ecommerce_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Импортируйте схему:
   ```bash
   mysql -u root -p ecommerce_db < database/init.sql
   ```
3. Укажите параметры подключения через переменные окружения (по желанию):
   ```bash
   export DB_HOST=127.0.0.1
   export DB_NAME=ecommerce_db
   export DB_USER=root
   export DB_PASS=secret
   export DB_CHARSET=utf8mb4
   ```
4. Запустите встроенный PHP-сервер:
   ```bash
   php -S localhost:8000 -t .
   ```
5. Откройте `http://localhost:8000`.

## Примечания

- В проекте используется два режима входа: пользовательский (обычное сравнение пароля) и админский (поддерживает как хэш, так и открытый пароль).
- Если база уже существует, `init.sql` не будет переинициализировать данные. При необходимости удалите том `db_data`.
