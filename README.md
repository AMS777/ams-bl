# ams-bl

This project is a quick-start boilerplate of the PHP micro-framework 
[Lumen](https://lumen.laravel.com/) to start an API backend project 
with the common functionality out of the box like user management, authentication
and email notifications.

It's recomended for API projects that implement the JSON API specification (http://jsonapi.org/).

**ams-bl is a backend project that matches the [frontend project ams-be](https://github.com/AMS777/ams-be) 
developed with the Javascript framework [Ember.js](https://www.emberjs.com/)** 
(though other frontend may be used).


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
- JSON API. API request and responses follow JSON API v1.0 specification:
http://jsonapi.org/format/
- JWT. Authentication with JSON Web Tokens: https://tools.ietf.org/html/rfc7519
- PHP7. This project uses PHP7 features. The
[Lumen version](https://lumen.laravel.com/docs/5.6#installation)
of this project requires PHP7.
- Database migrations for MySQL.


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

Lumen version installed: 5.6.

Download or fork this project and take it as the starting point of your own project.

On command line inside the project directory:

```
$ composer install
```

Create the `.env` file by copying `.env.example`, and set the application key with
the command (from Flipbox Lumen Generator package):

```
$ php artisan key:generate
```

Set the application secret to hash the signature of the JSON Web Tokens (JWT):

```
$ php artisan jwt:secret
```

To serve the project locally, on command line in the project directory (command
from Flipbox Lumen Generator package):

```
$ php artisan serve
```


## Usage

After creating your own project from this one, add your own files to the
existing ones.

You can modify the existing files on the project you've created. The key
files are:

- `tests/UserTest.php`
- `app/Http/Controllers/UserController.php`
- `app/Models/UserModel.php`


## Tests

```
$ phpunit
```

If error "No tests executed!" or some other is produced, global installed
`phpunit` package may be being used and having an unmatching version for this
project. Local installed `phpunit` must be used instead:

```
$ vendor/phpunit/phpunit/phpunit
```


## Emailing Functionality

Lumen does not have emailing functionality. It's one of the features lost when 
stripping down Laravel to obtain a micro framework.

To add emailing functionality I've followed the steps described on:

https://stackoverflow.com/questions/47124070/easiest-way-to-send-emails-with-lumen-5-4-and-mailgun/47124071#47124071

Email sending and exception handling is done on `app/Helpers/MailHelper.php`.

More info on Laravel documentation:

https://laravel.com/docs/5.6/mail

### Checking and testing emails

Emails may be previewed on browsers enabling specific routes as you can see on [`routes/web.php`](routes/web.php).

To test the emailing functionality, the mail driver is set to `log` so that emails
are not sent but written on the app log (`storage/logs/lumen.log`).

Thus, use `config(['mail.driver' => 'log']);` as you can see on [`tests/MessagingTest.php`](tests/MessagingTest.php).


## License

MIT License. Please see [LICENSE file](LICENSE) for more information.
