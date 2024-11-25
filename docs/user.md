# User Creation

User details are stored in the database and required for authentication. 

A user can be created in two ways. Environment Variables on startup or on the fly via a console command.

## Environment Variables

There are three optional environment variables that you can attach to the container to create a user:
- `USER_EMAIL`
- `USER_PASSWORD`

The email can be changed at any point and on next container start up the user will be replaced.

> [!NOTE]
>
> Changing the user password will not replace the existing password on startup.

## Command

It is possible to manually run the user creation command. This can be used as an alternative to the container variables.

The following is the equivalent to create a new user with the multi-user flag turned off.

``docker compose run --rm php bin/console user:create {email} {password}``

> [!NOTE]
>Adding `-r` as a flag will remove all other users and related OTP records

> [!NOTE]
>Add `-h` flag for command details

# User Password Reset

It's possible to reset the password of an existing User. This can only be done via a command.

``docker compose run --rm php bin/console user:reset-password {email} {password}``
