# Center for Public Integrity -- WordPress Theme

## Local

Admin login credentials for all environments can be found in 1Password -- `CPI - Dotenv (Local)`.

-   **url**: https://cpi.ups.dock
-   **wordpress admin**: https://cpi.ups.dock/wp-admin

## Development

-   **url**: https://dev-public-integrity.pantheonsite.io/
-   **wordpress admin**: https://dev-public-integrity.pantheonsite.io/wp-admin/
-   **username/password**: cpi / sun$hin3w33k

## Staging

-   **url**: https://test-public-integrity.pantheonsite.io/
-   **wordpress admin**: https://test-public-integrity.pantheonsite.io/wp-admin/
-   **username/password**: cpi / sun$hin3w33k

## Prerequisites

1.  Install [Docker for Mac](https://www.docker.com/docker-mac)
1.  Install [ups-dock](https://github.com/upstatement/ups-dock)
1.  Ensure [NVM](https://github.com/creationix/nvm) and [NPM](https://yarnpkg.com/en/) are installed globally.
1.  Ensure you are running > PHP 7.0 locally (this is needed for PHP linting and testing purposes)

## Installation

1.  Run `nvm install` to ensure you're using the correct version of Node. If you are switching to projects with other versions of Node, we recommend using something like [Oh My Zsh](https://github.com/robbyrussell/oh-my-zsh) which will automatically run `nvm use`
1.  Create a file called `.env` in this directory and populate it with configuration from the `CPI - Dotenv (Local)` file in 1Password
1.  Run the install command:

    ```
    ./bin/install.sh
    ```

1.  Install composer and npm packages

    ```
    composer install
    npm install
    ```

Once completed, you should be able to access your WordPress installation via `ups.dock`.

If you need to SSH into your container, from your project root run `docker-compose exec wordpress /bin/bash`

## Development Workflow

1.  Run `nvm use` to ensure you're using the correct version of Node
1.  Pull down changes and, if necessary, run `composer install` and `npm install`
1.  Run `./bin/start.sh` to start the WordPress backend / static build server
1.  Open the `Local` URL that appears below `[Browsersync] Access URLs:` in your browser (https://localhost:3000/)

Quitting this process (`Ctrl-C`) will shut down the container.

## Database Synchronization

## wp-cli

`docker-compose exec wordpress wp [command]`
