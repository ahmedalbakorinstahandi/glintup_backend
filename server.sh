ssh root@185.170.198.171

cd /
cd home/glintup-backend/htdocs/backend.glintup.ae
git pull


// setup laravel 11 project on vps server

1 . cloen the project
2. install composer
3. create database and user
4. create .env file
 - set database connection
 - set app key // php artisan key:generate
 - set app url
 - set app debug
 - set app timezone
 - set app locale
5. set Domin Root Directory to public folder
6. change permissions of storage/framework and storage/logs
7. run migrations // php artisan migrate
8. run seeders // php artisan db:seed