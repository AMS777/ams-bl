# ams-bl

This project is a boilerplate of the PHP micro-framework Lumen to start quickly a project with some functionality added like:
- User register.
- User login.
- Emailing.

Recomended for JSON API projects.

## Installing

Lumen version installed: 5.6.

Take this project as the starting point of your own API project.

Create the `.env` file by copying `.env.example`, and set the application key. 
You can generate a random key on Linux with:

`openssl rand -base64 32`

To serve the project locally, on command line in the project directory:

`php -S localhost:8000 -t public`

More info about installation:

https://lumen.laravel.com/docs/master/installation
