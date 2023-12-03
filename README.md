# test_cloudvps

Чтобы изменить данные для подключения БД нужно перейти 'api/db/db.php', там проблем нет разобраться.
Запускать можно через любой ресурс, например, у меня через OpenServer. БД развертывается сама.

## API
Встроен в работу самого ресурса.

### Получить список адресов со всеми данными:
POST запрос ```/api/urls/getUrls``` с пустым телом

Ответ:

```
{
    listUrls: [{ 
            id,
            url,
            shortUrl,
            count
        },
        ...
    ]
}
```


### Создать новый адрес:
POST запрос ```/api/urls/createUrl``` с телом:

```
{
    url,
    shortUrl
}
```

Ответ:

```
{
    ?error,
    ?message,
    ?shortUrl
}
```


### Удалить адрес:
POST запрос ```/api/urls/deleteUrl``` с телом:

```
{
    id
}
```

Ответ:

```
{
    ?error,
    ?message,
    ?result
}
```



### Обновить адрес:
POST запрос ```/api/urls/updateUrl``` с телом:

```
{
    id,
    url,
    shortUrl
}
```

Ответ:

```
{
    ?error,
    ?message,
    ?status
}
```
