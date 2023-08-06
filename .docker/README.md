# Massive Docker

> Template files have been last updated on 2023-03-27.

## Available Endpoints

| Service | URI                             | Port     |
| ------- | ------------------------------- | -------- |
| xdebug  | localhost                       | 9003     |

## Usage

### Start

Use the following command to start the project locally:

```zsh
docker compose --project-directory .docker up -d
# Or use XDEBUG_MODE to start a debugging session:
XDEBUG_MODE=debug docker compose --project-directory .docker up -d
```

### Stop

If you don't need the project running anymore you can stop the service with the following command:

```zsh
docker compose --project-directory .docker down
```

### Build

In case the project's docker dependencies changed, and you need the changes locally, ensure the container are stopped and create the new images with the build-file:

```zsh
docker compose --project-directory .docker build
```

## CLI

Attach to container directly:

```zsh
docker compose --project-directory .docker exec cli zsh
```

Or run any other command inside the container's context:

```zsh
docker compose --project-directory .docker exec cli <command>
```

## xdebug

To set up PhpStorm for debugging follow the [following guide](https://medium.com/@sirajul.anik/install-and-configure-xdebug-3-in-a-docker-container-and-integrate-step-debugging-with-phpstorm-5e135bc3290a) from section `Integrate step debugging with PhpStorm`.

Xdebug is configured with the IDE key: `ebs-plutus`.

Either add `XDEBUG_MODE` to the `up` command or set the variable in the docker `.env` file before starting the container. Use `debug` to enable or `off` to disable debugging. More info on the available modes can be found [here](https://xdebug.org/docs/all_settings#mode).
