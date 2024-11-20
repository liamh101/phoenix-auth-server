# Storing JWT Certs

By default, phoenix automatically generates a JWT passphrase and certificates. This comes with the drawback of a container restart; any existing JWT tokens will be invalid and need to be regenerated. While this is very unlikely, setting your passphrase and storing your key files locally is possible.

This is done by passing a 64-character alphanumeric string to the `JWT_PASSPHRASE` environment variable and adding a volume linking to `/app/config/jwt`.
