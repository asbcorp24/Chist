# Chist — информационная система колледжа

**Chist** — Laravel-система для расписания, электронного журнала, посещаемости, успеваемости, учебной части, ЭДО, библиотеки, воспитательной работы, общежития, практики, столовой, методического отдела, качества образования, обращений, АХЧ и управленческой аналитики.

Технологии: **Laravel 9, PHP 8.0.2+, Blade, Bootstrap 5, JavaScript/jQuery**. Основная структура данных: учебный год → период → группа → дисциплина → занятия → посещаемость/оценки → итоговые результаты. Исторические периоды сохраняются.

---

## ⚡ Timeweb: отдельная пошаговая установка с нуля

Этот раздел можно проходить **строго сверху вниз**. Он учитывает реальные ошибки, обнаруженные при первой установке Chist на виртуальном хостинге Timeweb.

> В примерах ниже проект уже находится в `~/Chist/public_html`. Если у вас другой путь, замените его своим. Проверить текущий путь: `pwd`.

### Шаг 1. Перейти в каталог проекта и получить последнюю версию

```bash
cd ~/Chist/public_html
git pull origin main
```

Проверка:

```bash
git status
git log -1 --oneline
```

### Шаг 2. Обязательно создать служебные каталоги Laravel

Git не хранит пустые каталоги. Поэтому после клонирования может отсутствовать `bootstrap/cache`. В этом случае Composer падает с ошибкой:

```text
The .../bootstrap/cache directory must be present and writable.
```

Исправление:

```bash
cd ~/Chist/public_html
mkdir -p bootstrap/cache
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
chmod -R 770 bootstrap/cache storage
```

Проверить:

```bash
ls -ld bootstrap/cache storage storage/framework storage/logs
```

### Шаг 3. Установить Composer-зависимости

Сначала проверьте PHP:

```bash
/opt/php82/bin/php -v
```

Затем:

```bash
cd ~/Chist/public_html
/opt/php82/bin/php -d memory_limit=1024M $(which composer) install --no-dev --optimize-autoloader
```

Если `which composer` ничего не возвращает:

```bash
composer --version
```

и используйте доступный путь Composer.

Успешная установка должна закончиться без красной ошибки `package:discover`.

### Шаг 4. Если появляется ServiceProvider::defaultProviders()

Ошибка:

```text
Call to undefined method Illuminate\Support\ServiceProvider::defaultProviders()
```

означает, что используется старый `config/app.php`, несовместимый с Laravel 9.

Исправление уже находится в актуальном `main`. Выполните:

```bash
cd ~/Chist/public_html
git pull origin main
rm -f bootstrap/cache/*.php
/opt/php82/bin/php -d memory_limit=1024M $(which composer) dump-autoload --no-dev --optimize
```

После коммита `6a9aedf` эта ошибка в Chist исправлена.

### Шаг 5. Создать .env

Если файла ещё нет:

```bash
cd ~/Chist/public_html
cp .env.example .env
nano .env
```

Минимальный production-вариант:

```dotenv
APP_NAME="Chist"
ORGANIZATION_NAME="Название колледжа"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://ВАШ-ДОМЕН
APP_TIMEZONE=Europe/Moscow

LOG_CHANNEL=stack
LOG_LEVEL=warning

DB_CONNECTION=sqlite
DB_DATABASE=/home/ВАШ_ЛОГИН/Chist/public_html/database/database.sqlite
DB_FOREIGN_KEYS=true

SESSION_DRIVER=file
SESSION_LIFETIME=120
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
```

Точный абсолютный путь можно узнать:

```bash
cd ~/Chist/public_html
pwd
```

### Шаг 6. Создать SQLite и права

```bash
cd ~/Chist/public_html
mkdir -p database
touch database/database.sqlite
chmod 770 database
chmod 660 database/database.sqlite
chmod -R 770 storage bootstrap/cache
```

### Шаг 7. Сгенерировать ключ приложения

```bash
cd ~/Chist/public_html
/opt/php82/bin/php artisan key:generate
```

Проверка:

```bash
grep APP_KEY .env
```

Если команда снова падает, сначала выполнить:

```bash
rm -f bootstrap/cache/*.php
/opt/php82/bin/php artisan optimize:clear
```

### Шаг 8. Создать структуру базы

```bash
cd ~/Chist/public_html
/opt/php82/bin/php artisan migrate --force
/opt/php82/bin/php artisan db:seed --force
```

Проверить миграции:

```bash
/opt/php82/bin/php artisan migrate:status
```

### Шаг 9. Создать главного администратора

```bash
/opt/php82/bin/php artisan chist:superadmin
```

Сохраните созданные логин/пароль в безопасном месте.

### Шаг 10. Оптимизировать production

```bash
/opt/php82/bin/php artisan optimize:clear
/opt/php82/bin/php artisan config:cache
/opt/php82/bin/php artisan route:cache
/opt/php82/bin/php artisan view:cache
```

### Шаг 11. Проверить Laravel из консоли

```bash
/opt/php82/bin/php artisan about
/opt/php82/bin/php artisan route:list
```

Если эти команды работают без исключений, Laravel загружается корректно.

### Шаг 12. Настроить корень сайта

В браузер должен отдаваться **каталог Laravel `public`**, а не корень Chist.

Если проект действительно установлен непосредственно в `~/Chist/public_html`, предпочтительнее в панели Timeweb изменить корневую директорию сайта на:

```text
Chist/public_html/public
```

Если конфигурация Timeweb использует отдельный `public_html` как web-root, проект лучше держать вне него и сделать `public_html` ссылкой на `public`, как описано в основном разделе ниже.

Критически важно: файл `.env` не должен открываться через браузер.

### Шаг 13. Проверить журнал Laravel при HTTP 500

```bash
cd ~/Chist/public_html
tail -n 100 storage/logs/laravel.log
```

Для наблюдения в реальном времени:

```bash
tail -f storage/logs/laravel.log
```

Остановить просмотр: `Ctrl+C`.

### Шаг 14. Демо-данные — только при необходимости

Для тестового сайта:

```bash
/opt/php82/bin/php artisan db:seed --class=RectorAutumn2026DemoSeeder --force
/opt/php82/bin/php artisan optimize:clear
```

На рабочем сайте колледжа эту команду не выполняйте, если демонстрационные записи не нужны.

### Шаг 15. Команды последующего обновления

```bash
cd ~/Chist/public_html
cp database/database.sqlite ~/chist-backup-$(date +%Y%m%d-%H%M).sqlite
git pull origin main
mkdir -p bootstrap/cache storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs
chmod -R 770 bootstrap/cache storage
/opt/php82/bin/php -d memory_limit=1024M $(which composer) install --no-dev --optimize-autoloader
/opt/php82/bin/php artisan migrate --force
/opt/php82/bin/php artisan db:seed --force
/opt/php82/bin/php artisan optimize:clear
/opt/php82/bin/php artisan config:cache
/opt/php82/bin/php artisan route:cache
/opt/php82/bin/php artisan view:cache
```

### Ошибка миграции: table student_achievements already exists

На чистой установке ранняя версия могла остановиться на `000029_create_upbringing_department`:

```text
SQLSTATE[HY000]: General error: 1 table "student_achievements" already exists
```

Причина: таблица уже создавалась миграцией `000014_create_student_private_profiles`, а воспитательный модуль пытался создать её повторно с дополнительными полями. Исправлено: `000029` теперь расширяет существующую таблицу и сохраняет совместимость с академическим модулем.

Если ошибка возникла во время первой установки пустой базы:

```bash
cd ~/Chist/public_html
git pull origin main
rm -f database/database.sqlite
touch database/database.sqlite
chmod 660 database/database.sqlite
/opt/php82/bin/php artisan migrate --force
```

Продолжайте к `db:seed` только после того, как все миграции завершатся `DONE`.

---

### Ошибка миграции: no such table practice_placements

На ранней версии проекта чистая установка могла остановиться на:

```text
2026_10_23_000012_expand_practice_and_attestation ... FAIL
SQLSTATE[HY000]: General error: 1 no such table: practice_placements
```

Причина: отсутствовала базовая миграция таблиц практики. Исправлено в репозитории добавлением миграции `000011_create_practice_core`.

Если это произошло **во время первой установки на пустую SQLite-базу**, не продолжайте `db:seed`: миграции завершились не полностью. После `git pull` пересоздайте пустую базу:

```bash
cd ~/Chist/public_html
git pull origin main
rm -f database/database.sqlite
touch database/database.sqlite
chmod 660 database/database.sqlite
chmod 770 database
/opt/php82/bin/php artisan migrate --force
```

Только если все миграции завершились `DONE`, выполняйте:

```bash
/opt/php82/bin/php artisan db:seed --force
/opt/php82/bin/php artisan chist:superadmin
/opt/php82/bin/php artisan optimize:clear
```

**Важно:** удалять `database.sqlite` этой командой можно только при первой установке, когда в базе ещё нет рабочих данных. На действующей системе миграции исправляются без удаления базы.

---

### Текущая ошибка на скриншоте

Если видите:

```text
In PackageManifest.php line 178:
The /home/.../Chist/public_html/bootstrap/cache directory must be present and writable.
```

выполните прямо сейчас:

```bash
cd ~/Chist/public_html
mkdir -p bootstrap/cache
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs
chmod -R 770 bootstrap/cache storage
/opt/php82/bin/php -d memory_limit=1024M $(which composer) install --no-dev --optimize-autoloader
/opt/php82/bin/php artisan key:generate
```

Если `key:generate` завершился сообщением `Application key set successfully`, переходите к миграциям:

```bash
/opt/php82/bin/php artisan migrate --force
/opt/php82/bin/php artisan db:seed --force
/opt/php82/bin/php artisan chist:superadmin
```

---

## Установка на виртуальный хостинг Timeweb

Инструкция предназначена именно для **обычного виртуального хостинга Timeweb**, а не VDS/VPS.

### 1. Создать сайт и привязать домен

В панели Timeweb откройте **Сайты → Мои сайты → Создать сайт**.

Например, создайте каталог:

```text
chist
```

Привяжите к нему нужный домен или поддомен, например:

```text
journal.example.ru
```

В настройках сайта включите HTTPS/SSL.

### 2. Выбрать PHP

Chist требует **PHP 8.0.2 или новее**. Для новой установки рекомендуется PHP **8.2 или 8.3**.

В Timeweb:

**Сайты → Мои сайты → шестерёнка сайта → Версия PHP → PHP 8.2/8.3**

Для консольных команд далее в примерах используется PHP 8.2:

```bash
/opt/php82/bin/php -v
```

Проверьте наличие расширений:

```bash
/opt/php82/bin/php -m | grep -Ei 'mbstring|openssl|pdo|sqlite|xml|simplexml'
```

Для SQLite должны быть доступны `pdo_sqlite` и `sqlite3`.

### 3. Подключиться по SSH

Данные SSH находятся в панели Timeweb.

После подключения:

```bash
cd ~
ls
```

Далее в инструкции предполагается, что каталог созданного сайта называется `chist`.

### 4. Загрузить проект из GitHub

Если Git доступен:

```bash
cd ~
rm -rf ~/chist/public_html
rm -rf ~/chist/*
git clone https://github.com/asbcorp24/Chist.git ~/chist
cd ~/chist
```

Если репозиторий закрытый, используйте GitHub Deploy Key или другой разрешённый способ авторизации. Не сохраняйте GitHub-токен в README или в исходниках.

Если Git на тарифе недоступен, скачайте архив проекта и загрузите его через файловый менеджер Timeweb в каталог сайта.

### 5. Правильно настроить public_html

У Laravel наружу должна быть доступна **только папка `public`**. Нельзя направлять домен на корень проекта, иначе можно случайно открыть `.env` и служебные файлы.

Для Timeweb:

```bash
cd ~
rm -rf ~/chist/public_html
ln -s ~/chist/public ~/chist/public_html
ls -la ~/chist
```

Должно получиться примерно:

```text
public_html -> /home/.../chist/public
```

Это рекомендуемая Timeweb схема размещения Laravel.

### 6. Установить Composer-зависимости

На Timeweb Composer доступен через SSH.

Перейдите в проект:

```bash
cd ~/chist
```

Рекомендуемый production-вариант:

```bash
/opt/php82/bin/php -d memory_limit=1024M /usr/local/bin/composer install --no-dev --optimize-autoloader
```

Если на конкретном сервере путь к Composer отличается:

```bash
which composer
composer --version
```

и затем:

```bash
composer install --no-dev --optimize-autoloader
```

### 7. Создать .env

```bash
cd ~/chist
cp .env.example .env
nano .env
```

Для production с SQLite рекомендуется:

```dotenv
APP_NAME="Chist"
ORGANIZATION_NAME="Название колледжа"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://journal.example.ru
APP_TIMEZONE=Europe/Moscow

LOG_CHANNEL=stack
LOG_LEVEL=warning

DB_CONNECTION=sqlite
DB_DATABASE=/home/LOGIN/chist/database/database.sqlite
DB_FOREIGN_KEYS=true

SESSION_DRIVER=file
SESSION_LIFETIME=120
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
```

Замените:

- `journal.example.ru` на реальный домен;
- `LOGIN` на логин аккаунта Timeweb;
- `ORGANIZATION_NAME` на название организации.

Узнать абсолютный путь:

```bash
cd ~/chist
pwd
```

В `DB_DATABASE` укажите полный путь из `pwd` плюс `/database/database.sqlite`.

**Никогда не коммитьте production-файл `.env` в Git.**

### 8. Создать SQLite

```bash
cd ~/chist
touch database/database.sqlite
chmod 660 database/database.sqlite
```

Каталог базы должен быть доступен PHP на запись:

```bash
chmod 770 database
```

### 9. Сгенерировать APP_KEY

```bash
cd ~/chist
/opt/php82/bin/php artisan key:generate
```

Проверка:

```bash
grep APP_KEY .env
```

### 10. Права Laravel

Laravel должен иметь возможность писать в `storage`, `bootstrap/cache` и SQLite.

```bash
cd ~/chist
chmod -R 770 storage bootstrap/cache
chmod 770 database
chmod 660 database/database.sqlite
```

Не устанавливайте `777` без необходимости.

### 11. Выполнить миграции

Для чистой production-базы:

```bash
cd ~/chist
/opt/php82/bin/php artisan migrate --force
```

Затем загрузить системные роли/права:

```bash
/opt/php82/bin/php artisan db:seed --force
```

### 12. Создать супер-администратора

```bash
/opt/php82/bin/php artisan chist:superadmin
```

Следуйте запросам команды и создайте первую административную учётную запись.

После этого войдите через веб-интерфейс и создавайте остальных пользователей уже средствами Chist.

### 13. Очистить и оптимизировать Laravel

```bash
cd ~/chist
/opt/php82/bin/php artisan optimize:clear
/opt/php82/bin/php artisan config:cache
/opt/php82/bin/php artisan route:cache
/opt/php82/bin/php artisan view:cache
```

После изменения `.env` обязательно выполните как минимум:

```bash
/opt/php82/bin/php artisan optimize:clear
/opt/php82/bin/php artisan config:cache
```

### 14. Проверить сайт

Откройте:

```text
https://journal.example.ru
```

Проверьте:

1. главную страницу;
2. вход администратора;
3. публичное расписание;
4. административную панель;
5. создание пользователя;
6. загрузку файла;
7. импорт Rector XML;
8. журнал;
9. аналитику;
10. отображение мобильной версии.

Если CSS/JS не загружаются, сначала проверьте `APP_URL`, HTTPS и что `public_html` действительно указывает на `public`.

---

## Демо-база по Rector «Осень 2026»

На **тестовом экземпляре**, а не на рабочей базе, можно загрузить подготовленный demo seed:

```bash
cd ~/chist
/opt/php82/bin/php artisan db:seed --class=RectorAutumn2026DemoSeeder --force
/opt/php82/bin/php artisan optimize:clear
```

Seeder создаёт демонстрационные данные на основе структуры Rector XML «Осень 2026». Демонстрационные ФИО студентов являются синтетическими.

Не запускайте этот seeder на production-базе колледжа, если демонстрационные записи там не нужны.

---

## Импорт Rector-College

Войдите пользователем с правом `schedule.import`, откройте раздел импорта расписания и загрузите XML Rector-College.

Импорт создаёт/обновляет учебный период, группы, преподавателей, дисциплины, кабинеты, виды занятий и расписание. Исторические журнальные данные не должны удаляться при последующих импортах.

После импорта при необходимости сформируйте занятия:

```bash
cd ~/chist
/opt/php82/bin/php artisan chist:generate-lessons
```

---

## Хранение загружаемых файлов

Production-файлы приложения не следует складывать вручную в Git.

Если модуль использует Laravel storage и публичный диск, создайте ссылку:

```bash
cd ~/chist
/opt/php82/bin/php artisan storage:link
```

Проверьте:

```bash
ls -la public/storage
```

Приватные документы студентов, ЭДО и другие закрытые материалы должны отдаваться через авторизованный контроллер, а не прямой публичной ссылкой.

Размер загружаемых файлов на Timeweb регулируется PHP-параметром `upload_max_filesize`. При необходимости увеличьте его настройками PHP Timeweb.

---

## Альтернатива: MySQL вместо SQLite

Текущая конфигурация Chist рассчитана на SQLite и это самый простой вариант установки. Для более нагруженной production-системы можно использовать MySQL/MariaDB после проверки всех миграций и SQL-запросов проекта на совместимость.

В панели Timeweb создайте БД и пользователя, затем измените `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ИМЯ_БАЗЫ
DB_USERNAME=ПОЛЬЗОВАТЕЛЬ
DB_PASSWORD=СЛОЖНЫЙ_ПАРОЛЬ
```

После этого:

```bash
/opt/php82/bin/php artisan optimize:clear
/opt/php82/bin/php artisan migrate --force
/opt/php82/bin/php artisan db:seed --force
```

Не переносите рабочую SQLite-базу в MySQL простым копированием файла.

---

## Cron на Timeweb

В текущей версии Chist постоянный cron для базового запуска не обязателен. Но при использовании запланированных уведомлений, автоматических задач, контроля сроков и дальнейшего развития Laravel Scheduler настройте его в панели **Timeweb → Crontab**.

На виртуальном хостинге Timeweb `crontab -e` недоступен — задания создаются через панель.

Для Laravel Scheduler команда должна запускаться каждую минуту:

```bash
cd /home/LOGIN/chist && /opt/php82/bin/php artisan schedule:run
```

Замените `LOGIN` реальным логином и проверьте путь командой `pwd`.

Не добавляйте scheduler, пока в приложении нет задач, которые должны выполняться по расписанию.

---

## Обновление Chist на Timeweb

Перед обновлением рабочей системы рекомендуется сделать резервную копию базы.

Для SQLite:

```bash
cd ~/chist
cp database/database.sqlite ~/chist-backup-$(date +%Y%m%d-%H%M).sqlite
```

Затем:

```bash
cd ~/chist
git pull origin main
/opt/php82/bin/php -d memory_limit=1024M /usr/local/bin/composer install --no-dev --optimize-autoloader
/opt/php82/bin/php artisan migrate --force
/opt/php82/bin/php artisan optimize:clear
/opt/php82/bin/php artisan config:cache
/opt/php82/bin/php artisan route:cache
/opt/php82/bin/php artisan view:cache
```

Если обновление содержит новые системные роли/права:

```bash
/opt/php82/bin/php artisan db:seed --force
```

### Короткая команда обновления

После резервного копирования:

```bash
cd ~/chist && git pull origin main && \
/opt/php82/bin/php -d memory_limit=1024M /usr/local/bin/composer install --no-dev --optimize-autoloader && \
/opt/php82/bin/php artisan migrate --force && \
/opt/php82/bin/php artisan db:seed --force && \
/opt/php82/bin/php artisan optimize:clear && \
/opt/php82/bin/php artisan config:cache && \
/opt/php82/bin/php artisan route:cache && \
/opt/php82/bin/php artisan view:cache
```

---

## Резервное копирование

### SQLite

Минимальная ручная резервная копия:

```bash
cd ~/chist
cp database/database.sqlite ~/backup-chist-$(date +%Y%m%d-%H%M).sqlite
```

Также сохраняйте каталог загруженных документов, если они находятся в `storage/app`.

Рекомендуется использовать резервное копирование самого Timeweb и периодически хранить отдельную копию базы вне хостинга.

---

## Типовые ошибки

### HTTP 500

Временно посмотрите Laravel log:

```bash
cd ~/chist
tail -n 100 storage/logs/laravel.log
```

Также проверьте:

```bash
/opt/php82/bin/php artisan about
/opt/php82/bin/php artisan config:show database
```

Не включайте `APP_DEBUG=true` надолго на публичном production-сайте.

### 403 Forbidden

Проверьте:

- `public_html -> public`;
- права каталогов;
- наличие `public/.htaccess`;
- права на файлы проекта.

### 404 на всех маршрутах кроме главной

Проверьте наличие:

```bash
ls -la ~/chist/public/.htaccess
```

и правильность ссылки:

```bash
ls -la ~/chist/public_html
```

### SQLite: attempt to write a readonly database

```bash
cd ~/chist
chmod 770 database
chmod 660 database/database.sqlite
chmod -R 770 storage bootstrap/cache
```

### No application encryption key has been specified

```bash
cd ~/chist
/opt/php82/bin/php artisan key:generate
/opt/php82/bin/php artisan optimize:clear
```

### Class / package not found

```bash
cd ~/chist
/opt/php82/bin/php -d memory_limit=1024M /usr/local/bin/composer install --no-dev --optimize-autoloader
/opt/php82/bin/php artisan optimize:clear
```

### После git pull сайт работает со старой конфигурацией

```bash
cd ~/chist
/opt/php82/bin/php artisan optimize:clear
/opt/php82/bin/php artisan config:cache
/opt/php82/bin/php artisan route:cache
/opt/php82/bin/php artisan view:cache
```

---

## Production checklist

Перед передачей системы в эксплуатацию убедитесь:

- `APP_ENV=production`;
- `APP_DEBUG=false`;
- используется HTTPS;
- `APP_URL` содержит правильный HTTPS-домен;
- `.env` не доступен из браузера и не находится в Git;
- домен смотрит только на Laravel `public`;
- создан супер-администратор;
- настроены роли и права;
- выполнены все миграции;
- production не содержит ненужный demo seed;
- проверено резервное копирование базы и файлов;
- проверена загрузка и скачивание документов;
- проверены импорт Rector, расписание, журнал и аналитика.

---

## Полезные команды

```bash
/opt/php82/bin/php artisan about
/opt/php82/bin/php artisan migrate:status
/opt/php82/bin/php artisan route:list
/opt/php82/bin/php artisan chist:superadmin
/opt/php82/bin/php artisan chist:generate-lessons
/opt/php82/bin/php artisan optimize:clear
```

## Локальный первый запуск

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

Откройте `http://127.0.0.1:8000`.
