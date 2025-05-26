# Laravel REST API для онлайн-магазина

## Описание
REST API для онлайн-магазина с поддержкой корзины, заказов, оплаты и автоматической отмены неоплаченных заказов.

## Основные сущности
- Пользователь (User)
- Товар (Product)
- Корзина (Cart, CartItem)
- Заказ (Order, OrderItem)
- Способ оплаты (PaymentMethod)

## Основной функционал
- Регистрация и авторизация пользователя
- Добавление/удаление товаров в корзину
- Получение списка и информации о товарах (сортировка по цене)
- Оформление заказа (оплата корзины, генерация уникальной ссылки на оплату)
- Callback для обновления статуса заказа на "Оплачен"
- Автоматическая отмена заказа через 2 минуты, если не оплачен
- Просмотр истории заказов, фильтрация и сортировка

## Примеры API
- Регистрация: `POST /api/register`
- Логин: `POST /api/login`
- Список товаров: `GET /api/products?sort=price_asc|price_desc`
- Добавить товар в корзину: `POST /api/cart/add`
- Удалить товар из корзины: `POST /api/cart/remove`
- Оформить заказ: `POST /api/cart/checkout`
- Callback оплаты: `POST /api/payment/callback/{order}`
- Список заказов: `GET /api/orders?status=&sort=date_asc|date_desc`

## Примеры запросов (Postman)

### Регистрация пользователя
**POST** `/api/register`
#### Входные данные:
```
{
  "name": "Test",
  "email": "test@example.com",
  "password": "secret123"
}
```
#### Ответ:
```
{
  "user": {
    "id": 1,
    "name": "Test",
    "email": "test@example.com",
    ...
  }
}
```

---

### Авторизация пользователя
**POST** `/api/login`
#### Входные данные:
```
{
  "email": "test@example.com",
  "password": "secret123"
}
```
#### Ответ:
```
{
  "token": "..."
}
```

---

### Получить список товаров
**GET** `/api/products?sort=price_asc|price_desc`
#### Ответ:
```
[
  {
    "id": 1,
    "name": "Смартфон",
    "description": "Современный смартфон...",
    "price": 29999.99,
    ...
  },
  ...
]
```

---

### Получить товар по id
**GET** `/api/products/1`
#### Ответ:
```
{
  "id": 1,
  "name": "Смартфон",
  "description": "Современный смартфон...",
  "price": 29999.99,
  ...
}
```

---

### Добавить товар в корзину
**POST** `/api/cart/add`
#### Входные данные:
```
{
  "product_id": 1,
  "quantity": 2
}
```
#### Ответ:
```
{
  "message": "Product added to cart"
}
```

---

### Удалить товар из корзины
**POST** `/api/cart/remove`
#### Входные данные:
```
{
  "product_id": 1
}
```
#### Ответ:
```
{
  "message": "Product removed from cart"
}
```

---

### Получить корзину пользователя
**GET** `/api/cart`
#### Ответ:
```
{
  "id": 1,
  "user_id": 1,
  "items": [
    {
      "id": 1,
      "product_id": 1,
      "quantity": 2,
      "product": {
        "id": 1,
        "name": "Смартфон",
        ...
      }
    },
    ...
  ]
}
```

---

### Оформить заказ (оплатить корзину)
**POST** `/api/cart/checkout`
#### Входные данные:
```
{
  "payment_method_id": 1
}
```
#### Ответ:
```
{
  "order": {
    "id": 1,
    "user_id": 1,
    "status": "pending",
    "total": 35999.49,
    ...
  },
  "payment_url": "https://pay.method1.com/pay?order=1&amount=35999.49&callback=https%3A%2F%2Fyourdomain%2Fapi%2Fpayment%2Fcallback%2F1"
}
```

---

### Callback оплаты (меняет статус заказа на "Оплачен")
**POST** `/api/payment/callback/1`
#### Ответ:
```
{
  "message": "Order status updated"
}
```

---

### Получить список заказов
**GET** `/api/orders?status=pending|paid|cancelled&sort=date_asc|date_desc`
#### Ответ:
```
[
  {
    "id": 1,
    "status": "pending",
    "total": 35999.49,
    "payment_method_id": 1,
    "items": [
      {
        "id": 1,
        "product_id": 1,
        "quantity": 2,
        "price": 29999.99,
        "product": {
          "id": 1,
          "name": "Смартфон",
          ...
        }
      },
      ...
    ],
    "payment_method": {
      "id": 1,
      "name": "TestPay",
      ...
    }
  },
  ...
]
```

---

### Получить заказ по id
**GET** `/api/orders/1`
#### Ответ:
```
{
  "id": 1,
  "status": "pending",
  "total": 35999.49,
  "payment_method_id": 1,
  "paid_at": null,
  "items": [ ... ],
  "payment_method": { ... }
}
```