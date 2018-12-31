# Year-Day-Calendar

> Track your days by assigning a category.

## Install
1. Clone the repository
2. Copy the `.env.example` file to `.env`
3. Update the `.env` file with your database credentials, app url, app name, etc
4. Run `composer install` and `npm i` followed by `npm run prod`
5. Run `php artisan key:generate` to set your app key
6. Run `php artisan migrate` to setup the database tables

## Post-Install Notes
Once your installation is up and running you may want to disable user registration. 
To do this simply disable `ENABLE_REGISTER` in your `.env` file
