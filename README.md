# Simple Article Application
A simple article app that allows users to login, create articles, and post comments to articles

</br></br>

# installation
## Setting up the database
Within command line tool as user postgres (default admin account for postgres databases)
1. Create the database if it does not exist: </br>
`createdb app_database`

2. CD to diretory </br>
`cd sql/`

3. Load schema: </br>
`psql -d app_database -f schema.sql`

4. Connect to database </br>
`psql -d app_database `

5. Set search path to correct schema. Useful for future operations if we want to view the DB through console </br>
`SET search_path TO article_app;`

6. Run setup Admin script </br>
`php setup_admin.php`

7. Load default data: </br>
`psql -d app_database -f default_data.sql`


## Troubleshooting
> Could not find driver

Environment does not have the necessary extension to communicate with PostgreSQL using PDO. </br> Solution: 
`sudo apt install phpX.X-pgsql` </br>
Where X.X is your php version 

> Password authentication failed for user

PSQL server is rejecting the login attempt. </br> Solution: </br>
1. Log into database `sudo -u postgres psql`
2. Change password `ALTER USER your_user WITH PASSWORD 'new_password';`

