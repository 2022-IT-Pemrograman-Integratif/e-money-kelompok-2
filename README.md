# BuskidiStore by Kelompok 4 Pemrograman Integratif 2022

### Kelompok 2

| Achmad Aushaf Amrega | 5027201036 |
| --- | --- |
| Ariel Daffansyah Aliski | 5027201058 |
| Danish Putra Dandi | 5027201048 |

## API E-Money BuskidiCoins

### Register

dengan method POST, Register berfungsi untuk membuat akun

![Untitled](Readme%20Web%20356a4/Untitled.png)

Response

```json
{
    "status": 201,
    "message": {
        "success": "berhasil membuat akun"
    }
}
```

### Login

dengan method POST, Login berfungsi untuk masuk kedalam akun dan mengenerate sebuah token

![Untitled](Readme%20Web%20356a4/Untitled%201.png)

Response

```json
{
    "status": 201,
    "message": {
        "success": "berhasil login",
        "data": {
            "account_id": "15",
            "account_username": "userbuskidi",
            "account_password": "password",
            "account_pin": "12345",
            "nomer_hp": "087812345678",
            "account_money": "0",
            "account_role": "0"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE2NTAzNTg0NTIsImlhdCI6MTY1MDM1NDg1MiwiZGF0YSI6eyJhY2NvdW50X2lkIjoiMTUiLCJhY2NvdW50X3VzZXJuYW1lIjoidXNlcmJ1c2tpZGkiLCJhY2NvdW50X3Bhc3N3b3JkIjoicGFzc3dvcmQiLCJhY2NvdW50X3BpbiI6IjEyMzQ1Iiwibm9tZXJfaHAiOiIwODc4MTIzNDU2NzgiLCJhY2NvdW50X21vbmV5IjoiMCIsImFjY291bnRfcm9sZSI6IjAifX0.9bU_T6_0TGGZLyBvnpECMDhaT7bZXz22JeRDAJWf_4w"
    }
}
```

### GetUserData

dengan method GET, berfungsi untuk mengambil informasi akun (sesuai dengan credential IDnya masing - masing)

![Untitled](Readme%20Web%20356a4/Untitled%202.png)

Response

```json
{
    "status": 201,
    "message": {
        "data": {
            "account_id": "15",
            "account_username": "userbuskidi",
            "account_password": "password",
            "account_pin": "12345",
            "nomer_hp": "087812345678",
            "account_money": "0",
            "account_role": "0"
        }
    }
}
```

### Topup

dengan method POST, berfungsi untuk menambahkan saldo ke akun e-money

![Untitled](Readme%20Web%20356a4/admintopup.PNG)

Response

```json
{
    "status": 201,
    "message": {
        "success": "topup berhasil"
    }
}
```

Lalu bila kita cek di getUserData, saldo akan terupdate

```json
{
    "status": 201,
    "message": {
        "data": {
            "account_id": "12",
            "account_username": "bisa",
            "account_password": "bisa",
            "account_pin": "12334",
            "nomer_hp": "081111111120",
            "account_money": "5000",
            "account_role": "0"
        }
    }
}
```

### Transfer

dengan method POST, berfungsi untuk mengirimkan saldo dari akun satu ke akun lain dengan mencocokan akun ke nomor telepon yang sesuai

![Untitled](Readme%20Web%20356a4/admintransfer.PNG)

Response

```json
{
    "status": 201,
    "message": {
        "success": "transfer berhasil"
    }
}
```

Dan bila dicek lagi akunnya, saldo akan terlihat berkurang dari 60000 menjadi 599000

```json
{
    "status": 201,
    "message": {
        "data": {
            "account_id": "2",
            "account_username": "oke",
            "account_password": "oke",
            "account_pin": "321",
            "nomer_hp": "081111111112",
            "account_money": "599000",
            "account_role": "0"
        }
    }
}
```

### see_all_data

dengan method GET, bisa melihat keseluruhan akun yang ada, namun dikhususkan untuk admin (sehingga harus menggunakan token admin)

Response

```json
[
    {
        "account_id": "1",
        "account_username": "admin",
        "account_password": "admin",
        "account_pin": "123",
        "nomer_hp": "081111111111",
        "account_money": "0",
        "account_role": "1"
    },
    {
        "account_id": "2",
        "account_username": "oke",
        "account_password": "oke",
        "account_pin": "321",
        "nomer_hp": "081111111112",
        "account_money": "500000",
        "account_role": "0"
    },
    {
        "account_id": "4",
        "account_username": "coba",
        "account_password": "coba",
        "account_pin": "3211",
        "nomer_hp": "081111111113",
        "account_money": "0",
        "account_role": "0"
    },
    {
        "account_id": "8",
        "account_username": "amreganteng",
        "account_password": "amregresik",
        "account_pin": "364858",
        "nomer_hp": "081111111114",
        "account_money": "0",
        "account_role": "0"
    },
    {
        "account_id": "9",
        "account_username": "ahha",
        "account_password": "ahsiap",
        "account_pin": "111",
        "nomer_hp": "081111111115",
        "account_money": "0",
        "account_role": "0"
    },
    {
        "account_id": "10",
        "account_username": "pemrograman integratif",
        "account_password": "hihihi",
        "account_pin": "2147483647",
        "nomer_hp": "081111111116",
        "account_money": "0",
        "account_role": "0"
    },
    {
        "account_id": "11",
        "account_username": "asd",
        "account_password": "asd",
        "account_pin": "1233",
        "nomer_hp": "081111111119",
        "account_money": "2000",
        "account_role": "0"
    },
    {
        "account_id": "12",
        "account_username": "bisa",
        "account_password": "bisa",
        "account_pin": "12334",
        "nomer_hp": "081111111120",
        "account_money": "3000",
        "account_role": "0"
    },
    {
        "account_id": "13",
        "account_username": "integratif19april",
        "account_password": "gantengsekali",
        "account_pin": "1111111",
        "nomer_hp": "0888888888",
        "account_money": "0",
        "account_role": "0"
    },
    {
        "account_id": "14",
        "account_username": "bismillah",
        "account_password": "tampanberani",
        "account_pin": "13232",
        "nomer_hp": "08111111",
        "account_money": "500000",
        "account_role": "0"
    },
    {
        "account_id": "15",
        "account_username": "userbuskidi",
        "account_password": "password",
        "account_pin": "12345",
        "nomer_hp": "087812345678",
        "account_money": "4000",
        "account_role": "0"
    }
]
```

# CodeIgniter 4 Development

[![Build Status](https://github.com/codeigniter4/CodeIgniter4/workflows/PHPUnit/badge.svg)](https://github.com/codeigniter4/CodeIgniter4/actions?query=workflow%3A%22PHPUnit%22)
[![Coverage Status](https://coveralls.io/repos/github/codeigniter4/CodeIgniter4/badge.svg?branch=develop)](https://coveralls.io/github/codeigniter4/CodeIgniter4?branch=develop)
[![Downloads](https://poser.pugx.org/codeigniter4/framework/downloads)](https://packagist.org/packages/codeigniter4/framework)
[![GitHub release (latest by date)](https://img.shields.io/github/v/release/codeigniter4/CodeIgniter4)](https://packagist.org/packages/codeigniter4/framework)
[![GitHub stars](https://img.shields.io/github/stars/codeigniter4/CodeIgniter4)](https://packagist.org/packages/codeigniter4/framework)
[![GitHub license](https://img.shields.io/github/license/codeigniter4/CodeIgniter4)](https://github.com/codeigniter4/CodeIgniter4/blob/develop/LICENSE)
[![contributions welcome](https://img.shields.io/badge/contributions-welcome-brightgreen.svg?style=flat)](https://github.com/codeigniter4/CodeIgniter4/pulls)
<br>

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](http://codeigniter.com).

This repository holds the source code for CodeIgniter 4 only.
Version 4 is a complete rewrite to bring the quality and the code into a more modern version,
while still keeping as many of the things intact that has made people love the framework over the years.

More information about the plans for version 4 can be found in [the announcement](http://forum.codeigniter.com/thread-62615.html) on the forums.

### Documentation

The [User Guide](https://codeigniter4.github.io/userguide/) is the primary documentation for CodeIgniter 4.

The current **in-progress** User Guide can be found [here](https://codeigniter4.github.io/CodeIgniter4/).
As with the rest of the framework, it is a work in progress, and will see changes over time to structure, explanations, etc.

You might also be interested in the [API documentation](https://codeigniter4.github.io/api/) for the framework components.

## Important Change with index.php

index.php is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

CodeIgniter is developed completely on a volunteer basis. As such, please give up to 7 days
for your issues to be reviewed. If you haven't heard from one of the team in that time period,
feel free to leave a comment on the issue so that it gets brought back to our attention.

We use GitHub issues to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

If you raise an issue here that pertains to support or a feature request, it will
be closed! If you are not sure if you have found a bug, raise a thread on the forum first -
someone else may have encountered the same thing.

Before raising a new GitHub issue, please check that your bug hasn't already
been reported or fixed.

We use pull requests (PRs) for CONTRIBUTIONS to the repository.
We are looking for contributions that address one of the reported bugs or
approved work packages.

Do not use a PR as a form of feature request.
Unsolicited contributions will only be considered if they fit nicely
into the framework roadmap.
Remember that some components that were part of CodeIgniter 3 are being moved
to optional packages, with their own repository.

## Contributing

We **are** accepting contributions from the community!

Please read the [*Contributing to CodeIgniter*](https://github.com/codeigniter4/CodeIgniter4/blob/develop/contributing/README.md).

## Server Requirements

PHP version 7.3 or higher is required, with the following extensions installed:


- [intl](http://php.net/manual/en/intl.requirements.php)
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- xml (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php)

## Running CodeIgniter Tests

Information on running the CodeIgniter test suite can be found in the [README.md](tests/README.md) file in the tests directory.
