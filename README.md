# ams-bl

**Disclaimer: This project is under development, intended functionality it's
not yet finished.**

This project is a quick-start boilerplate of the PHP micro-framework Lumen to
start a project with some useful functionality out of the box like:
- User register.
- User login.
- User reset password.
- Emailing.

Recomended for API projects that implement the JSON API specification
(http://jsonapi.org/).

**This backend project is matched with a frontend project on Javascript framework
Ember.js** (yet to be developed).


## Features

- TDD. This project uses [Test Driven Development](https://www.agilealliance.org/glossary/tdd/)
as development methodology with unit tests.
- JSON API. Responses follow JSON API v1.0 specification:
http://jsonapi.org/format/
I haven't found an existing package that fulfills the complete specification
of JSON API, but the Tobscure JSON-API package is pretty clean and easy to use.
It lacks some implementations, like JSON API error specification, but I've
extended it with custom functions adapted to Lumen that build error objects
following the JSON API specification.
- PHP7. This project uses PHP7 features. The
[Lumen version](https://lumen.laravel.com/docs/5.6#installation)
of this project requires PHP7.


## Packages

This project uses following packages:

- Flipbox Lumen Generator: Command line resources generator extension.
https://github.com/flipboxstudio/lumen-generator
- Tobscure JSON-API: Build objects following the JSON API specification.
https://github.com/tobscure/json-api


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


## License

MIT License. Please see [LICENSE file](LICENSE) for more information.
