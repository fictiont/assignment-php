#!/bin/bash
if [ "$EUID" -ne 0 ]
  then echo "[ERROR] Please run this script as root with 'sudo ./app-init/init.sh'. This is needed to modify config files created by docker-compose"
  exit
fi
#set app/.env variables
if [ -f .env ]
then
  export $(cat .env | sed 's/#.*//g' | xargs)
else
	echo "Missing .env file. Possibly you forgot to rename .env.sample one and change environments there?"
	exit
fi

if [ -z "${MYSQL_ROOT_PASSWORD}" ]
	then echo "[ERROR]MYSQL_ROOT_PASSWORD variable missing in .env"
	exit
fi
if [ -z "${MYSQL_DB_NAME}" ]
	then echo "[ERROR]MYSQL_DB_NAME variable missing in .env"
	exit
fi
if [ -z "${MYSQL_PASSWORD}" ]
	then echo "[ERROR]MYSQL_PASSWORD variable missing in .env"
	exit
fi
if [ -z "${MYSQL_USER}" ]
	then echo "[ERROR]MYSQL_USER variable missing in .env"
	exit
fi
#building images
docker-compose build
#starting containers
docker-compose up -d
#checking composer dependencies
docker-compose exec php bash -c "yes | composer update --no-interaction"

if [ -n "${MYSQL_ROOT_PASSWORD}" ]
then
	sed -i "s|^DATABASE_URL=.*$|DATABASE_URL=mysql\:\/\/${MYSQL_USER}\:${MYSQL_PASSWORD}\@mysql\:3306\/${MYSQL_DB_NAME}\?serverVersion=5.7\&charset=utf8mb4|g" app/.env
	#put root credentials into test env to create test db
	echo "" >> app/.env.test
	echo "DATABASE_URL=mysql://root:${MYSQL_ROOT_PASSWORD}@mysql:3306/${MYSQL_DB_NAME}?serverVersion=5.7&charset=utf8mb4" >> app/.env.test
fi


echo "Composer finished, applying DB structure"
mysql_status=$(docker-compose exec mysql bash -c "service mysql status")
echo "$mysql_status"
while [[ ! "$mysql_status" =~ "is running" ]];
do
	echo "Waiting until mysql starts up.."
	sleep 3
	mysql_status=$(docker-compose exec mysql bash -c "service mysql status")
done
docker-compose exec mysql bash -c "service mysql status"
echo "Waiting until mysql server loads.."
sleep 15

#create databases
docker-compose exec php bash -c "bin/console doctrine:database:create --if-not-exists"
docker-compose exec php bash -c "APP_ENV=test bin/console doctrine:database:create --if-not-exists"
#apply database migrations
docker-compose exec php bash -c "bin/console doctrine:migrations:migrate --no-interaction"
docker-compose exec php bash -c "APP_ENV=test bin/console doctrine:migrations:migrate --no-interaction"
#configure keys for JWT
docker-compose exec php bash -c "./config/jwt_init.sh"
#create example users
docker-compose exec php bash -c "bin/console doctrine:fixtures:load --append --no-interaction"
docker-compose exec php bash -c "APP_ENV=test bin/console doctrine:fixtures:load --append --no-interaction"
#execute tests
docker-compose exec php bash -c "bin/phpunit"