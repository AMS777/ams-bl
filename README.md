# ams-bl

This project is a boilerplate of the PHP micro-framework Lumen to start quickly
a project with some functionality added like:
- User register.
- User login.
- User reset password.
- Emailing.

Recomended for JSON API projects.

This backend project is matched with a frontend project on Javascript framework
Ember.js (yet to be developed).


## Packages

- Flipbox Lumen Generator: Command line resources generator extension.
https://github.com/flipboxstudio/lumen-generator


## Installing

Lumen version installed: 5.6.

Take this project as the starting point of your own project.

Run `composer install` on command line.

Create the `.env` file by copying `.env.example`, and set the application key.
On command line (command from Flipbox Lumen Generator package):

`php artisan key:generate`

To serve the project locally, on command line in the project directory (command
from Flipbox Lumen Generator package):

`php artisan serve`
