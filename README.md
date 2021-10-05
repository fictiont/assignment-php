# Lokalise PHP homework
Hello and welcome!

Your task is to create a simple REST API for a mini Translation Management System (TMS) plus the underlying MySQL database structure. You will have a Dockerized development environment as well as a couple of tools provided to get you started, however, you have full creative freedom in choosing the way you want to complete this task.

**Please read the description carefully.**

## Description
The goal of our little TMS is to allow multiple different `Translation` texts in different `Languages` to be linked to a single `Key`. For example, you can have a `Key` with the name `main.title` with `Hello world` and `Hallo Welt` translations for English and German respectively.

In order to perform requests to the API, an authentication token must be provided. A token can have either `read` or `read/write` access.

### Entities
You would need to create the following entities (feel free to add or modify them if you think it would benefit the final result):
#### Language
- Has a name
- Has an ISO code
- Can be LTR (Left To Right by default) or RTL

#### Translation
- Has a text value
- Has a language associated with it
- Belongs to a `Key`

#### Key
- Has a unique name
- Has one translation per Language at all times

#### Token
- Is unique
- Can have `read` or `read/write` access

### API Functionality
- API token authentication
- List available languages
- Manage `Keys`
    - List
    - Retrieve
    - Create
    - Rename
    - Delete
- Manage `Key` `Translations`
    - Ability to update `Translation` value for any language for a given key
- Export all `Keys` and their `Translations` in `.json` or `.yml` format as a `zip` archive
    - `.json` export should have one file per language (e.g. `[language-iso].json`) with the following format:
        ```
        {
            <key.name>: <translation.value>,
            ...
        }
    - `.yaml` export should contain all languages in a single `translations.yaml` file with the following format:
        ```
        <language.iso>:
            <key.name>: <translation.value>
            ...
        <language2.iso>:
            <key.name>: <translation.value>
            ...
### Submitting the task
- This repository should be forked and the final result should be submitted as a link to that fork
- SQL of the final database structure/data should be included
- If you change the development environment, please make sure that you include some instructions on how to run the final result

### Notes
- You can use any PHP framework that you are comfortable with (though using Symfony will be considered a plus)
- The quality and structure of the code is important, so use the best coding practices that you know
- Unit tests will be considered a plus

## Included development environment (updated)
You can find a simple Dockerized development environment included with this assignment. Please follow the steps below to set it up (or use your own environment, however, please make sure to include instructions on how to run it).

#### 1. Install Docker CE & Docker Compose
+ [Mac](https://docs.docker.com/docker-for-mac/)
+ [Debian](https://docs.docker.com/engine/installation/linux/docker-ce/debian/)
+ [Docker Compose](https://docs.docker.com/compose/install/)

Bonus: [Linux Post-install](https://docs.docker.com/engine/installation/linux/linux-postinstall/)

#### 2. Setup environment secrets
- Navigate to the project folder and rename .env.sample to .env
- Open .env file with text editor and replace sample variables values. *PASSWORD variables values are mandatory to be replaced.

#### 3. Bring application up
- Make sure that Docker is running
- Navigate to the project folder using a Terminal
- Run the `sudo ./app-init/init.sh` script to bring the app online. 
Script needed to be run as sudo, since it needs an access to files created by docker compose which have root permissions by default and also it created openssl certs.

#### 4. Accessing the MySQL database
The environment includes the `phpmyadmin` tool. In order to access it, use the http://127.0.0.1:8080 URL. Use `mysql` as the Server name, `root` as the User and password you set before in .env file.

#### 5. Running the application
Use the http://127.0.0.1 URL to access your application, opening it you should be redirected to http://127.0.0.1/docs where API docs are available in OpenAPI format.

#### 6. Executing requests
Upon application deployment two dummy users being created which you may use to authenticate and test api:
- username: `test_readonly@gmail.com`, password: `testreadpassword`. This user has readonly permissions, only GET requests will be available;
- username: `test_full@gmail.com`, password: `testfullpassword`. This user has full permissions, all API requests will be available;