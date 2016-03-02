# WIP: Zonk
A single purpose tool for cleaning MySQL databases for use outside of production

## Todo
- ~~Truncate Support~~
- ~~Wildcard Table Truncate~~
- Obfuscation of fields within tables
- Support https://github.com/fzaninotto/Faker
- Tests?

## Configuration
``` yml
database:
  # See Zonk\Database\CapsuleBuilder for a complete list
  username: root
  password: password
  database: my_production_database
  host: 127.0.0.1
  
operations: ~
```

## Operations
Zonk has multiple operations it can perform, none are required by default.

### Truncate
Truncate will do as suggested and truncate the table to zero length, this operation makes use of `DisabledForeignKeyConstraintsTrait` to disable foreign keys to prevent errors when truncating. Its upto the end user to ensure all constraints are resolved.

``` yml
operations:
  truncate:
    - users
    # Wildcard to which any table with appends after the star
    - users_*
```

## Packages Used

- Symfony/console
- Symfony/ymal
- illuminate/database
- monolog/monolog
