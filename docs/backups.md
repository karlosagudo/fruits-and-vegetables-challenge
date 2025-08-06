# Backups

A full backup implementation for mariadb database is implemented.
This is implemented with command backup and it is scheduled in a cron job to execute a daily basis backup.


## Get a backup from a server
If you need to create an automated backup do the following steps:
1. ssh into the machine:
    ```shell
    ssh evolucare@10.80.58.56
    ```
2. create backup in the docker
    ```shell
    docker exec -ti vitalsigns-php /bin/sh
    php bin/console app:backup-db
    ```
3. Localize the file:
    ```shell
    ls data/storage/backup # For example: "2024-09-30_07-12-35_backup.sql"
    ```
4. exit the docker , and copy the file into the server machine:
    ```shell
    docker cp vitalsigns-php:/srv/app/data/storage/backup/2024-09-30_07-12-35_backup.sql ./
    ```
    This will create the file 2024-09-30_07-12-35_backup.sql into the folder of the server

5. Copy the file in your local environment: (look where it is located, in this case in /home/evolucare)
    ```shell
     scp -r -C -P 22 evolucare@10.80.58.56:/home/evolucare/2024-09-30_07-12-35_backup.sql ./
    ```
6. In your local machine restore the backup:
    ```shell
    `make shell`
    `mysql -uroot -hmariadb -p vital-signs < 2024-09-30_07-12-35_backup.sql`
    ```
In case your database is different from vital-signs change it.


## Create backup
Command `php bin/console app:backup-db` will create a new mysql backup file in data/storage/backup.

### Backup schedule (cron)

Cronjobs are set up in ``.docker\cronjobs.txt`` file. The [syntax](https://en.wikipedia.org/wiki/Cron) is the same as
a crontab setup. These files must have an empty line at bottom to work correctly.

### Clean up backups

A command to clean up older backups is available under `php bin/console app:backup-remove-older`
and is configured by .env in BACKUP_OLDER_LIMIT. This command is also added to cronjobs to be executed
daily.

### Current backup lifecycle

Currently, a cronjob file is set in `./cronjobs.txt` to create a daily backup at 4:00AM and to clean up all older
backup files following the `BACKUP_OLDER_LIMIT` .env variable.