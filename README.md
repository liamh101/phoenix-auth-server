# Phoenix Auth Server

A syncing server for the [Phoenix Auth Desktop Application](https://github.com/liamh101/phoenix-auth).

## Getting Started (Development)

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --no-cache` to build fresh images
3. Run `docker compose up --pull always -d --wait` to set up and start a fresh Symfony project
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
5. Run `docker compose down --remove-orphans` to stop the Docker containers.

## Docs

1. [Deploying in production](docs/production.md)
2. [Users](docs/user.md)
3. [Storing JWT Certs](docs/jwt.md)
4. [Options available](docs/options.md)
5. [Debugging with Xdebug](docs/xdebug.md)
6. [TLS Certificates](docs/tls.md)
7. [Using Alpine Linux instead of Debian](docs/alpine.md)
8. [Using a Makefile](docs/makefile.md)
9. [Troubleshooting](docs/troubleshooting.md)

## License

Phoenix Auth and Phoenix Auth Server is available under the MIT License.

## Credits

Created by [Liam Hackett](https://github.com/liamh101). 

Special Thanks to [Kévin Dunglas](https://dunglas.dev) and [Maxime Helias](https://twitter.com/maxhelias) for creating the Symfony docker skeleton.
