#bin/bash
#building images
docker-compose build
#starting containers
docker-compose up -d
#checking composer dependencies
docker-compose exec php bash -c "composer update"
#create databases
docker-compose exec php bash -c "bin/console doctrine:database:create"
docker-compose exec php bash -c "APP_ENV=test bin/console doctrine:database:create"
#apply database migrations
docker-compose exec php bash -c "bin/console doctrine:migrations:migrate --no-interaction"
docker-compose exec php bash -c "APP_ENV=test bin/console doctrine:migrations:migrate --no-interaction"
#configure keys for JWT
docker-compose exec php bash -c "./config/jwt_init.sh"
#create example users
docker-compose exec php bash -c "bin/console doctrine:fixtures:load --append --no-interaction"
docker-compose exec php bash -c "APP_ENV=test bin/console doctrine:fixtures:load --append --no-interaction"