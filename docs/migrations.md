# Migrations

## Definition
Migrations are a set of SQL statements that are executed in order to alter the database schema in some way.

## How do we use them

### Location
We have the migrations in the [migrations](../migrations) folder.

### When
They are normally executed before the fixtures tosymfony console make:migration -n prepare the database.

### How to create and run them

Our migrations will be created to reflect the [ORM mapping](../src/Infrastructure/DB/Mapping) structure.

To create a migration, run:
```shell
symfony console make:migration -n
```
Also, to just run the migrations in mariadb, add this:
```php
final class Version202411111111 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->skipIf(!str_contains(get_class($this->connection->getDatabasePlatform()), 'MariaDb'));
        // code here
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(!str_contains(get_class($this->connection->getDatabasePlatform()), 'MariaDb'));
        // code here
    }
}
```
Now it should appear in the migrations folder. To run a migration, run:
```shell
symfony console doctrine:migrations:migrate -n
```

## How to unify migrations
From time to time you may need to reduce the migration number and unify all the changes in one migration.

### In local environment
1. Delete all the migration files
2. Delete the database with one of these alternatives:
    ```sql
    drop database your_database;
    ```
    ```shell
    symfony console doctrine:database:drop --force
    ```
3. Create a migration (note, you may need to create the database before with ``create database your_database`` or ``symfony console doctrine:database:create -n``:
    ```shell
    symfony console make:migration -n
    ```
4. Run the migration:
    ```shell
    symfony console doctrine:migrations:migrate -n
    ```
5. Populate the database with the [fixtures](fixtures.md).
6. Check all the tests pass and the app still works

### In the development server
After you have pushed the branch with the migration changes, follow these steps in the development server:
1. ssh into the development machine.
    ```shell
    ssh evolucare@10.80.58.56
    ```
2. If you don't want to run the fixtures, generate a backup of the existing data (follow the steps in [backups.md](backups.md)).
    - Remove the inserts in the ``doctrine_migration_versions`` table if there are any.
    - Add an insert with the current migration.
   ```sql
    insert into doctrine_migration_versions (`version`) values ('Doctrine\\Version28041999');
    ```
3. Enter the database container.
    ```shell
    docker exec -ti wound-db sh
    ```
4. Connect to the database.
    ```shell
    mariadb -u{root_user} -p{root_password}
    ```
5. Drop the database.
    ```sql
    drop database your_database;
    ```
6. Wait and check that the container logs of the application has correctly run the migrations.
    ```shell
    docker logs your-app-container --follow
    ```
7. If you did not use a backup, run the fixtures as indicated in [fixtures.md](fixtures/fixtures.md).
8. Insert the new migration:
    ```sql
    insert into doctrine_migration_versions (`version`) values ('Doctrine\\Version28041999');
    ```
