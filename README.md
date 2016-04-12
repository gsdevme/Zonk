# WIP: Zonk
A single purpose tool for cleaning MySQL databases for use outside of production

## Todo
- ~~Truncate Support~~
- ~~Wildcard Table Truncate~~
- ~~Obfuscation of fields within tables~~
- ~~Support https://github.com/fzaninotto/Faker~~
- Tests?

## Installing
``` bash
wget https://github.com/gsdevme/Zonk/releases/download/0.1.0-Alpha2/zonk.phar
chmod +x zonk.phar
sudo mv zonk.phar /usr/local/bin/zonk
```

## Configuration
``` yml
database:
  # See Zonk\Database\ConnectionBuilder for a complete list
  user: root
  password: password
  dbname: my_production_database
  host: 127.0.0.1
  
operations: ~
```

## Operations
Zonk has multiple operations it can perform, none are required by default.

### Obfuscate
Obfuscate will use the current value within the database to create a new value. Since performance could be a problem a very fast hash is used `md5` therefore we randomly salt the value to provide extra security in terms of reserve engineering the original value. 

`FakerAwareStrategy` will use a https://github.com/fzaninotto/faker to construct a new value, this however can have downfalls, for example if you have a large dataset you may get dupicates, also the original value is lost which could have an impact if you have data copied around the database in a flat file setup. 

``` yml
  obfuscate:
    strategies:
      email: Zonk\Obfuscation\Strategies\EmailAddress
      string: Zonk\Obfuscation\Strategies\BasicString
    tables:
      customer:
        username: email
        email: email
        name: string
```

### Truncate
Truncate will do as suggested and truncate the table to zero length, this operation makes use of `DisabledForeignKeyConstraintsTrait` to disable foreign keys to prevent errors when truncating. Its upto the end user to ensure all constraints are resolved.

``` yml
operations:
  truncate:
    - users
    # Wildcard to which any table with appends after the star
    - users_*
```

### Delete
Delete will delete all where the condition is met

``` yml
operations:
  delete:
    tables:
      customer: 'customerId <> 1'
```

## Packages Used

- Symfony/console
- Symfony/ymal
- doctrine/doctrine-dbal
- monolog/monolog
