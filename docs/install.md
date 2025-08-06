# Local Docker Developing

## With Docker already installed:

Execute to build the images
```make build```

To install project , composer dependencies etc..
```make init```

To run web server:
```make start```

## Run container

To run docker container. This will land into `/app` container folder where we can find our repository.
```make shell```

## Git Hooks IMPORTANT

Follow documentation [here](docs/githooks.md)

Install with:
``` vendor/bin/captainhook install --only-enabled   ```
or
```make install-hooks```

## Some problems on docker:
If there is problems with the docker setup for the permissions on var/cache or var/logs:
ONLY IN LOCAL:
```
chmod 777 -R var
```

Could happen with the mariadb container, some permissions problems with the mysql volume:
```
sudo chown ${WHOAMI}:${WHOAMI} -R ./mysql
```
And maybe
```
sudo rm -rf ./mysql
```

## Pre-installations advices in case you need to install docker

This is well known issues with windows filesystems and docker. If you expose in a Linux image a volume into the windows filesystem, the performance decreases dramatically, then is not possible to work it in a good conditions.

To fix it we should install WSL2 in Windows 10/11 through the following command:
(Open a PowerShell or Command Prompt as Administrator and run the command, then reboot Windows.)
```wsl --install```

After this, you can access this project by opening with your preferred IDE
the ```\\wsl.localhost\Ubuntu\home\your-project-dir``` or ```\\wsl$\``` locations.
This make you work into Linux filesystem, instead of Windows one, bypassing any filesystem-related issues and offering
the best performance.

## Install docker engine and docker Desktop

We need to install docker engine and the Gui tool Docker Desktop which let us manage docker engine in a visual environment.

- [Install on Windows](https://docs.docker.com/desktop/install/windows-install/)
- [Install on Linux](https://docs.docker.com/desktop/install/linux-install/)

## Install docker environment

Copy .env.local.dist to .env.local and fulfill it with all the environment variables listed in the file.
Do the same with .env.test.dist to .env.test.local for testings.
Also copy and fulfill .docker/auth.json.dist to .docker/auth.json for gitlab and Evolucare composer access.
Then copy .docker/auth.json to your local composer directory (/home/your-user/.composer/)

Docker is deployed under a Bridge network, so we have to setup an IP in .env.local for each exposed docker service in the microservices stack.
Later we can refer to this IP if we need to communicate microservices each other.

```
# Microservices networking. Uses the {DOCKER_HTTP_INTERNAL_IP} variable from where each microservice is exposed.
MSVNTW_EHW_AGENDA_API_IP=172.23.0.3
MSVNTW_EHW_LOG_API_IP=172.23.0.5
...
```````

## Run the app

```make start```
This will make your app running at `http://localhost:{DOCKER_HTTP_PORT}`

## Other commands

You can check the other commands in the [Makefile](Makefile)
Like stop, prune or logs. Type `make help` to see all available commands.

## Install without docker (not recommended)

## Miscellaneous info: Install LTC Php 7.4 in Ubuntu Wsl2

```sudo add-apt-repository ppa:ondrej/php```
```sudo apt install php7.4 php7.4-cli php7.4-fpm php7.4-json php7.4-common php7.4-mysql php7.4-zip php7.4-gd php7.4-mbstring php7.4-curl php7.4-xml php7.4-bcmath php7.4-json```

## Git Hooks

Follow documentation [here](docs/githooks.md)
