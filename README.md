# Laravel Subscriber API

[Laravel Subscriber API](https://github.com/dogukanzorlu/app-subscriber-backend) Example Laravel Api + Background Jobs for Mobile Application

# Table of Contents

* [Pre Requirements](#pre-requirements)
* [Installation](#installation)
* [EndPoints](#endpoints)
    * [Device](#device)
        * [Index](#index)
        * [Show](#show)
        * [Create](#create)
        * [Update](#update)
        * [Delete](#delete)
    * [Subscription](#subscription)
        * [Check](#check)
        * [Add](#add)
    * [Mock](#mock)
        * [Google](#google)

## Pre Requirements

* Redis, php-redis, postgresql.

## Installation

Copy-Paste .env.sample as .env and set postgre conf

```bash
$ composer install && composer update
$ php artisan migrate
$ php artisan db:seed # If you want to work with dummy data, you can run it.
$ php artisan horizon # for background jobs
$ php artisan schedule:run # for background jobs
$ php artisan serve
```

## Endpoints

### Device

#### Index

```HTTP
GET /api/devices?page={ınt}&limit={int} HTTP/1.1
Host: localhost:8000
Content-Type: application/json
```

```json
{
    "meta": {
        "status": 200,
        "page": "1",
        "count": 10000
    },
    "data": [
        {
            "id": 161781,
            "device_uuid": "773bb8f2-589b-4f06-87de-d68e75db65cb",
            "app_id": "UtgfjcpkVg",
            "language": "en",
            "operation_system": "Android",
            "created_at": "2021-02-06 18:16:57",
            "updated_at": "2021-02-06 18:16:57"
        }...
    ]
}
```


#### Show

```HTTP
GET /api/devices/{device_uuid} HTTP/1.1
Host: localhost:8000
Content-Type: application/json
```

```json
{
    "meta": {
        "status": 200
    },
    "data": {
        "id": 161781,
        "device_uuid": "773bb8f2-589b-4f06-87de-d68e75db65cb",
        "app_id": "UtgfjcpkVg",
        "language": "en",
        "operation_system": "Android",
        "created_at": "2021-02-06 18:16:57",
        "updated_at": "2021-02-06 18:16:57"
    }
}
```

#### Create

```HTTP
POST /api/devices HTTP/1.1
Host: localhost:8000
Content-Type: application/json

{
    "device_uuid": {String},
    "app_id": {String},
    "language": {String},
    "operation_system": {String}
}
```

```json
{
    "meta": {
        "status": 200
    },
    "data": {
        "token": "ZTM3YzEyMGEtMDdzZmItNDczOC1hZDY2LTY1ZTU3ZTA5YWQ="
    }
}
```

#### Update

```HTTP
PUT /api/devices/{device_uuid} HTTP/1.1
Host: localhost:8000
Content-Type: application/json

{
    "app_id": {String},
    "language": {String},
    "operation_system": {String}
}
```

```json
{
    "meta": {
        "status": 200
    },
    "data": {
        "id": 770156,
        "device_uuid": "e37c120a-07sfb-4738-ad66-65e57e09ad",
        "app_id": "2D1dsaJkxa2",
        "language": "en",
        "operation_system": "Linux",
        "created_at": "2021-06-02 21:23:42",
        "updated_at": "2021-06-02 21:26:16"
    }
}
```

#### Delete

```HTTP
DELETE /api/devices/{device_uuid} HTTP/1.1
Host: localhost:8000
```

### Subscription

#### Check

```HTTP
POST /api/subscriptions/check HTTP/1.1
Host: localhost:8000
Content-Type: application/json

{
    "token": "ZTM3YzEyMGEtMDdzZmItNDczOC1hZDY2LTY1ZTU3ZTA5YWQ="
}
```

```json
{
    "meta": {
        "status": 200
    },
    "data": {
        "id": 770155,
        "device_id": 770156,
        "status": true,
        "expire_at": "2021-02-06 16:19:23",
        "created_at": "2021-06-02 21:29:23",
        "updated_at": "2021-06-02 21:29:23"
    }
}
```


#### Add

```HTTP
POST /api/subscriptions HTTP/1.1
Host: localhost:8000
Content-Type: application/json
Content-Length: 118

{
    "receipt": "jdHSAD31ABbsa231hsadgy2basbah125",
    "token": "ZTM3YzEyMGEtMDdzZmItNDczOC1hZDY2LTY1ZTU3ZTA5YWQ="
}
```

```json
{
    "meta": {
        "status": 200
    },
    "data": {
        "device_id": 770156,
        "status": true,
        "expire_at": "2021-02-06 16:21:37"
    }
}
```

### Mock

#### Google

```HTTP
POST /api/google/service/subscription HTTP/1.1
Host: localhost:8000
Content-Type: application/json

{
    "receipt": "jdHSAD31ABbsa231hsadgy2basbah125"
}
```

```json
{
    "status": true,
    "expire_at": "2021-02-06 16:24:03"
}
```
