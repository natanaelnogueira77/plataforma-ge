# Read me

This is a stockage management system for GPA.

## Configuration

Firstly, go to the env.sample.ini file, copy its contents and create a new file called env.ini, and paste there. 
Then, complete the variables (app information, database, smtp configurations, etc.).

Then, open the terminal on the project's folder and execute the 2 following commands, in order, for database population: 

- php migrations.php
- php seeders.php