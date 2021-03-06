#  Restfull php Symfony API photo-service

### Описание сервиса

сервис предназначен для хранения фотографий 1С и сайта.

1С по коду товара добавляет фотографии товара по API используя base64 формат фотографий.

После внесения фотографий на сервис, 1С или сайт запрашивает по коду товара фотографии.

В сервис встроен Liip imagine bundle для кеширования фотографий, изменения размеров, создания превью.

Сервис решает проблему хранения фотографий в одном месте для 1С, сайта, мобильного приложения и др.
Чтобы все системы имели единое хранилище фотографий и могли использовать всегда актуальные фотографии.

Сервис сделан на основе [Symfony 5.4](https://symfony.com/doc/5.4/)

### Принцип работы

во всех учетных системах есть уникальное поле "код товара". Основным является код товара в 1С. 
Поэтому на сайте или в мобильном приложении этот код товара будет являться связующим.
При создании товара на сайте или в 1С надо заполнить поле Код товара, а при записи товара в базу
надо обратиться по API к этому сервису, который примет в качестве параметра код товара и фотографии.

Для фотографий есть поле "позиция" - означающее порядок фотографий.

Если обратиться для получения фотографий по коду товара, то в ответ будет выдан код товара, массив
фотографий этого товара и превью изображение (первое изображение).

Все кеши изображений настраиваются через Liip imagine bundle.

### Получение фото по коду товара GET-запросом

формируем GET запрос на маршрут /api/product/{code} , например

```
GET http://photo.local/api/product/<код_товара>
Accept: application/json
```

ответ будет такого вида:
```
{
  "code": "221",
  "images": [
    {
      "id": 13,
      "path": "media\/images",
      "filename": "fb1d68d10752781101d02ca633bf762a3f33f2e9.jpg",
      "position": 20,
      "cachedImage": "http:\/\/photo.local\/media\/cache\/resolve\/thumb_1920_1080\/media\/images\/fb1d68d10752781101d02ca633bf762a3f33f2e9.jpg"
    },
    {
      "id": 14,
      "path": "media\/images",
      "filename": "0b4023461374e0e86f43a655faa8d32bb341e4c7.jpg",
      "position": 555,
      "cachedImage": "http:\/\/photo.local\/media\/cache\/resolve\/thumb_1920_1080\/media\/images\/0b4023461374e0e86f43a655faa8d32bb341e4c7.jpg"
    },
    {
      "id": 12,
      "path": "media\/images",
      "filename": "e3a34764c9878a6735d4f571a23e6d0459d7bbde.jpg",
      "position": 2222,
      "cachedImage": "http:\/\/photo.local\/media\/cache\/resolve\/thumb_1920_1080\/media\/images\/e3a34764c9878a6735d4f571a23e6d0459d7bbde.jpg"
    }
  ],
  "preview": "http:\/\/photo.local\/media\/cache\/resolve\/thumb_1920_1080\/media\/images\/fb1d68d10752781101d02ca633bf762a3f33f2e9.jpg"
}

```

если запрошен товар, которого нет в базе, ответ будет следующий
```
GET http://photo.local/api/product/223
Accept: application/json

Response:
{
  "code": 404,
  "message": "Товар не найден."
}
```

### Создание нового товара по коду товара POST-запросом

формируем POST запрос на маршрут /api/product/{code}

Для создания нового товара надо обязательно передать код-товара (уникальный).
Если код товара будет неуникальный, то будет ошибка.
При создании можно сразу передать массив с фотографиями в формате base64, либо потом.

пример POST-запроса создания товара без фотографий:
```
POST http://photo.local/api/product
Content-Type: application/json

{
  "code": "2361"
}
```
Если все успешно создается, то выдается ответ
```
"{\"status\":\"ok\"}"
```
Если код не уникальный, то будет следующий ответ с сообщением об ошибке
```
"{\"code\":[\"This value is already used.\"]}"
```

пример POST-запроса создания товара с фотографиями (в поле base64 должно быть полное фото, в примере просто ...):
```
POST http://photo.local/api/product
Content-Type: application/json

{
  "code": "221",
  "images": [
    {
      "position": 2222,
      "base64": "data:image/jpeg;base64,..."
    }
  ,
    {
      "id": 8,
      "position": 20,
      "base64": "data:image/jpeg;base64,..."
    }
    ,
    {
      "position": 555,
      "base64": "data:image/jpeg;base64,..."
    }
  ]
}
```

### Обновление карточки товара PUT-запросом
Для обновления товара используется PUT-запрос на маршрут /api/product/{code} с телом запроса,
аналогично созданию товара.

Важно обязательно передавать все изображения, иначе они просто удалятся. 

Данный метод перезатирает все изменения. Поэтому использовать надо очень осторожно.

Если хотим изменить позиции фотографий, то надо передать и все данные, а именно code, images[]

Пример запроса на изменение порядка фотографий

```
POST http://photo.local/api/product/221
Content-Type: application/json

{
  "code": "221",
  "images": [
    {
      "position": 1,
      "base64": "data:image/jpeg;base64,..."
    }
  ,
    {
      "id": 8,
      "position": 2,
      "base64": "data:image/jpeg;base64,..."
    }
    ,
    {
      "position": 3,
      "base64": "data:image/jpeg;base64,..."
    }
  ]
}
```

### Установка сервиса

клонируем репозиторий и запускаем 
```
composer install
```

Далее подключаем свой GIT и настраиваем в соответствие с документацией [Symfony 5.4](https://symfony.com/doc/5.4/)
(создаем базу, вносим строку подключения, подключаем веб-сервер и т.д.)