# Tell Molly (_tellmolly.com_)

> Improve your mental health by journaling using comments, categories and tags.

## Setup with Docker

1. Clone the repository
2. Change into the new directory
3. Copy the `.env.example` file to `.env`
4. Run `docker compose up -d` to build the Docker image and run the Docker container.
5. Show the running containers `docker ps`
6. Connect into the container `docker exec -it <app-container-name> bash`
   1. Run `php artisan key:generate` to set your app key
   2. Run `php artisan migrate` to set up the database tables
   3. Run `php artisan db:seed --class BaseSeeder` to finish the base database setup

## Post-Install Notes

Once your installation is up and running you may want to disable user registration. 
To do this simply disable `ENABLE_REGISTER` in your `.env` file

## License

See [LICENSE](LICENSE).
