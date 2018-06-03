
# ams-bl

This project is a quick-start boilerplate of the PHP micro-framework 
[Lumen](https://lumen.laravel.com/) to start an API backend project 
with the common functionality out of the box like user management, authentication
and email notifications.

It's recomended for API projects that implement the JSON API specification (http://jsonapi.org/).

**ams-bl is a backend project that matches the [frontend project ams-be](https://github.com/AMS777/ams-be) 
developed with the Javascript framework [Ember.js](https://www.emberjs.com/) and
may be set up with the [ams-bel architecture](https://github.com/AMS777/ams-bel)** 
(though other frontend and architecture may be used).


## Demo

**You can see a working demo of the ams-be frontend project and the ams-bl 
backend project with the ams-bel architecture at:**

**http://ams-bel.mas.gallery/**


## Features

- User create, get, update and delete.
- Authentication token.
- Authorization to get, update and delete user.
- User password reset with token.
- Email confirmation for register and delete.
- User email verification on register.
- Contact message email.
- API request data validation.


## Technologies

- TDD. This project uses [Test Driven Development](https://www.agilealliance.org/glossary/tdd/)
as development methodology with unit tests.
- JSON API. API requests and responses follow the JSON API v1.0 specification:
http://jsonapi.org/format/
- JWT. Authentication with JSON Web Tokens: https://tools.ietf.org/html/rfc7519
- PHP7.1. This project uses 
[PHP7.1 features](http://php.net/manual/en/migration71.new-features.php).
- MySQL. [Database migrations](https://lumen.laravel.com/docs/5.6/database#migrations) for MySQL.
- Lumen 5.6. This project is developed with the
  <a href="https://lumen.laravel.com/docs/5.6/releases#5.6.0" target="_blank">5.6 version of Lumen</a>.


## Packages

This project uses following packages:

- Flipbox Lumen Generator: Command line resources generator extension.  
  https://github.com/flipboxstudio/lumen-generator
- Tobscure JSON-API: Build objects following the JSON API specification.  
  https://github.com/tobscure/json-api  
  I haven't found a package that fulfills the complete specification
  of JSON API, but the Tobscure JSON-API package is pretty clean and easy to use.
  It lacks some implementations, like JSON API error specification, but I've
  extended it with custom functions adapted to Lumen that build error objects
  following the JSON API specification.
- illuminate/mail, guzzlehttp/guzzle. Emailing functionality.  
  See section [Emailing Functionality](#emailing-functionality).
- tymondesigns/jwt-auth. JSON Web Token (JWT) Authentication for Laravel & Lumen.  
  https://github.com/tymondesigns/jwt-auth


## Install

Download or fork this project and take it as the starting point of your own project.

Set file and directory permissions:

```
$ sudo chown -R <user-name>:<group-name> ams-bl/
$ find ams-bl/ -type d -exec chmod 755 {} \;
$ find ams-bl/ -type f -exec chmod 644 {} \;
```

Files and directories within "storage/" directory need to be writable for the web server user. 

```
$ sudo chgrp -R www-data ams-bl/storage/
$ find ams-bl/storage/ -type d -exec chmod 775 {} \;
$ find ams-bl/storage/ -type f -exec chmod 664 {} \;
```

On command line inside the project directory:

```
$ composer install
```

Create the `.env` file by copying `.env.example`:

```
$ cp .env.example .env
```

Set the application key with the command (from Flipbox Lumen Generator package):

```
$ php artisan key:generate
```

Set the application secret to hash the signature of the JSON Web Tokens (JWT):

```
$ php artisan jwt:secret
```

Configure `.env` file with the Mailgun data.

Create MySQL database and user:

```
$ mysql -u <user> -p
mysql> create database ams_bel;
mysql> create user '<user_name>'@'localhost' identified by '<password>';
mysql> grant all on ams_bel.* to '<user_name>'@'localhost';
mysql> show grants for '<user_name>'@'localhost';
```

Configure `.env` file with the database data and run migrations:

```
$ php artisan migrate
```


## Run Development Server

To serve the project locally, on command line in the project directory (command
from Flipbox Lumen Generator package):

```
$ php artisan serve
```


## Usage

After creating your own project from this one, add your own files to the
existing ones.

You can modify the existing files on the project you've created. The key
files are located in:

- `tests/`
- `app/Helpers/`
- `app/Http/Controllers/`
- `app/JsonApi/`
- `app/Mail/`
- `app/Models/`
- `resources/views/mail/`


## Tests

```
$ vendor/phpunit/phpunit/phpunit --verbose # --verbose shows incomplete and skipped tests (if @depends is used)
```


## Emailing Functionality

Lumen does not have emailing functionality. It's one of the features lost when 
stripping down Laravel to obtain a micro framework.

The steps followed to add emailing functionality are described on:

https://stackoverflow.com/questions/47124070/easiest-way-to-send-emails-with-lumen-5-4-and-mailgun/47124071#47124071

Email sending and exception handling are performed on `app/Helpers/MailHelper.php`.

More info on Laravel documentation:

https://laravel.com/docs/5.6/mail

### Checking emails

Emails may be previewed on browsers enabling specific routes as you can see on 
[`routes/web.php`](routes/web.php).

The email routes are available for testing purposes but on production
environments remove them from [`routes/web.php`](routes/web.php),
or at least comment them if you expect to be needing them often.

### Testing emails

To test the emailing functionality, the mail driver is set to `log` so that emails
are not sent but written on the app log (`storage/logs/lumen.log`).

Thus, use `config(['mail.driver' => 'log']);` as you can see on 
[`tests/MessagingTest.php`](tests/MessagingTest.php).


## License

MIT License. Please see [LICENSE file](LICENSE) for more information.
