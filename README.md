## Laravel - Showcase

### Требования
- PHP 8.2+
- Composer
- MySQL
### Установка
1. Скачиваем проект:
    - Вариант 1: скачайте [архив](https://github.com/TheKompreso/laravel-showcase-06-07/archive/refs/heads/master.zip) с проектом  и разархивируйте в нужную папку.
    - Вариант 2: с помощью git clone:
```
git clone https://github.com/TheKompreso/laravel-showcase-06-07
```
2. Устанавливаем зависимости:
```
composer install
```
3. Настраиваем базу данных:
копируем файл <b>.env.example</b>, переименовываем его в <b>.env</b> и настраиваем связь с нашей базой данных. Пример:
```
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```
4. Выполняем миграции:
```
php artisan migrate
```
5. Заполняем базу данных:
```
php artisan db:seed
```
6. Тестируем АПИ.
    1. Прогоняем через тесты
    ```
    php artisan test --filter=BookingAPITest
    ```
    2. Запускаем сервер и отправляем запросы
    ```
    php artisan serve 
    ```

## АПИ
Для доступа к АПИ требуется указать заголовок Authorization: Bearer {token}, где token - api_token из модели user.

### GET: /api/bookings
Получить список бронирований пользователя.

| Query Parameter | Type  | Description                                     |
|-------------------|-------|-------------------------------------------------|
| ``page``          | int   | Номер страницы (по умолчанию 1).                |
| ``per_page``      | int   | Количество элементов на страницу (от 10 до 100). |

Пример запроса:<br>
```
GET: /api/v1/bookings?per_page=3&page=1
```

Ответ:
```json
{
    "data": [
        {
            "id": 3,
            "user_id": 6,
            "slots": [
                {
                    "id": 9,
                    "start_time": "2025-06-25 12:00:00",
                    "end_time": "2025-06-25 13:00:00"
                },
                {
                    "id": 10,
                    "start_time": "2025-06-25 13:30:00",
                    "end_time": "2025-06-25 14:30:00"
                }
            ]
        },
        {
            "id": 4,
            "user_id": 6,
            "slots": [
                {
                    "id": 11,
                    "start_time": "2025-06-25 12:00:00",
                    "end_time": "2025-06-25 13:00:00"
                },
                {
                    "id": 12,
                    "start_time": "2025-06-25 13:30:00",
                    "end_time": "2025-06-25 14:30:00"
                }
            ]
        },
        {
            "id": 5,
            "user_id": 6,
            "slots": [
                {
                    "id": 13,
                    "start_time": "2025-06-25 12:00:00",
                    "end_time": "2025-06-25 13:00:00"
                },
                {
                    "id": 14,
                    "start_time": "2025-06-25 13:30:00",
                    "end_time": "2025-06-25 14:30:00"
                }
            ]
        }
    ],
    "links": {
        "first": "http://127.0.0.1:8000/api/v1/bookings?page=1",
        "last": "http://127.0.0.1:8000/api/v1/bookings?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "http://127.0.0.1:8000/api/v1/bookings?page=1",
                "label": "1",
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "active": false
            }
        ],
        "path": "http://127.0.0.1:8000/api/v1/bookings",
        "per_page": 10,
        "to": 3,
        "total": 3
    }
}
```

---

### POST: /api/bookings
Создать бронирование с несколькими слотами.

Пример тела запроса:
```json
{
  "slots": [
    {
      "start_time": "2025-06-25T12:00:00",
      "end_time": "2025-06-25T13:00:00"
    },
    {
      "start_time": "2025-06-25T13:30:00",
      "end_time": "2025-06-25T14:30:00"
    }
  ]
}
```

Пример ответа:
```json
{
    "data": {
        "id": 6,
        "user_id": 6,
        "slots": [
            {
                  "id": 15,
                  "start_time": "2025-06-25T12:00:00",
                  "end_time": "2025-06-25T13:00:00"
            },
            {
                 "id": 16,
                 "start_time": "2025-06-25T13:30:00",
                 "end_time": "2025-06-25T14:30:00"
            }
        ]
    }
}
```

---

### PATCH /api/bookings/{booking}/slots/{slot}
Обновить конкретный слот бронирования.

Пример тела запроса:
```json
{
    "start_time": "2025-06-25 12:00:00",
    "end_time": "2025-06-25 13:00:00"
}
```

Пример ответа:
```json
{
    "data": {
        "id": 3,
        "user_id": 6,
        "slots": [
            {
                "id": 9,
                "start_time": "2025-06-25 12:00:00",
                "end_time": "2025-06-25 13:00:00"
            },
            {
                "id": 10,
                "start_time": "2025-06-25 13:30:00",
                "end_time": "2025-06-25 14:30:00"
            }
        ]
    }
}
```

---

### POST /api/bookings/{booking}/slots
Добавить новый слот к существующему бронированию.

Пример тела запроса:
```json
{
    "start_time": "2025-06-25 12:00:00",
    "end_time": "2025-06-25 13:00:00"
}
```

Пример ответа:
```json
{
    "data": {
        "id": 3,
        "user_id": 6,
        "slots": [
            {
                "id": 16,
                "start_time": "2025-06-21 09:00:00",
                "end_time": "2025-06-21 09:59:00"
            },
            {
                "id": 9,
                "start_time": "2025-06-25 12:00:00",
                "end_time": "2025-06-25 13:00:00"
            },
            {
                "id": 10,
                "start_time": "2025-06-25 13:30:00",
                "end_time": "2025-06-25 14:30:00"
            }
        ]
    }
}
```

---

### DELETE /api/bookings/{booking}
Удалить весь заказ.

Ответ:
Код ответа — `200`.
