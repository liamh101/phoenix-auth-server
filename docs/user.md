# User Creation

User details are stored in the database and required for authentication. 

A user can be created in two ways. Environment Variables on startup or on the fly via a console command.

## Environment Variables

There are three optional environment variables that you can attach to the container to create a user:
- `USER_EMAIL`
- `USER_PASSWORD`
- `MULTI_USER`

The email can be changed at any point and on next container start up the user will be replaced.

> [!NOTE]
>
> Changing the user password will not replace the existing password on startup, currently you must remove the existing user.

The `MULTI_USER` variable can only be `1` or `0`.

`1`: Means if a user change is made to the `USER_EMAIL` variable to the previous user is kept with their password. Meaning it can still be used.

`0`: Means any new user will outright replace the existing user.


## Command

It is possible to manually run the user creation command. This can be used as an alternative to the container variables.

The following is the equivalent to create a new user with the multi-user flag turned off.

``docker compose run --rm php bin/console php bin/console user:create {email} {password}``

> [!NOTE]
>Adding `-m` as a flag will enable multi-user support

> [!NOTE]
>Add `-h` flag for command details
