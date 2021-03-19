#### Installation.

To install this framework, just do the command right below:

```
composer create-project mds-id/framework [app-name]
```

#### Configuration.

- [Database configuration](#database-configuration)

#### Database configuration

Configuration file for database are exists in ```config/database.yaml```. Configuration key that supported:

```
database:
	driver: <available-driver>
	schema: <database-schema>
	username: <database-username>
	password: <database-password>
	host: <database-host>
	port: <database-port>
```

Each configuration fields explanation:

- ```database.driver [type: string]```

Supports several database drivers. Visit [doctrine/dbal](https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url) for list of available database drivers.

- ```database.schema [type: string]```

Database engine schema name.

- ```database.username [type: string]```

Database engine user name.

- ```database.password [type: string]```

Database engine password.

- ```database.host [type: string]```

Database engine host name.

- ```database.port [type: int]```

Database engine port number.
