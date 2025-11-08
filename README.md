# Simple Article Application
A simple article app that allows users to login, create articles, and post comments to articles

</br></br>

# installation
## Setting up the database
Within psql command-line tool
1. Create the database if it does not exist: </br>
`createdb app_database`

2. Load schema: </br>
`psql -U your_username -d app_database -f schema.sql`

3. Load default data: </br>
`psql -U your_username -d app_database -f default_data.sql`
