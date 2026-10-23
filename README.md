# Chist — многолетний электронный журнал

Laravel 9 + Blade + Bootstrap 5 + jQuery + SQLite. Архитектура: учебный год → полугодие → группа → занятия → посещаемость/оценки → итоговые результаты. Старые периоды сохраняются в архиве.

## Первый запуск

Требования: PHP 8.0.2+ с расширениями mbstring, openssl, PDO SQLite, XML/SimpleXML; Composer.

```bash
git clone https://github.com/asbcorp24/Chist.git
cd Chist
composer install
cp .env.example .env
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan key:generate
php artisan migrate --seed
php artisan chist:superadmin
php artisan serve
```

Откройте `http://127.0.0.1:8000`. Без входа доступно публичное расписание. После входа интерфейс зависит от роли.

## Импорт Rector-College

Войдите пользователем с правом `schedule.import`, откройте «Импорт расписания» и загрузите XML. Импорт создаёт/обновляет учебный период, расписание, праздники и календарные занятия; затем применяет распознанные замены. Журнал прошлых импортов не удаляется.

## Роли и безопасность

Системные роли: Super Admin, Admin, заведующий учебной частью, участник расписания, классный руководитель, преподаватель, староста, студент. Закрытый учебный период доступен для просмотра, но запись оценок и посещаемости блокируется сервером.

## Полезные команды

```bash
php artisan migrate --seed
php artisan chist:superadmin
php artisan chist:generate-lessons
php artisan route:list
```
