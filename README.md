# Helping Hand

This IT help desk named “Helping Hand” will allow users to submit tickets when they face technical issues, which specialists can then respond to and resolve. Management will be able to monitor overall ticket activity, generate reports, and manage user roles such as specialist or new users. This system is designed to be user-friendly.


# Installation:

1) Download and install mySQL, PHP and Apache (or any other web server that supports PHP), if they are not already installed.
   Make sure to include the mySQL extensions for PHP and the PHP module for Apache.
   (Ubuntu example: sudo apt-get install apache2 mysql mysql-server php libapache2-mod-php php8.3-mysql )

2) Import “install.sql” into the mysql database. ( Ex: mysql < install.sql )

3) Extract the remaining files to the root directory of your webserver.

4) Update $server, $username and $password in “db_connect.php” to the appropriate values for your mysql installation.

5) Start your web server. If it is already running, restart it.

6) Login into the system through http://localhost/login.php  or  http://hostname/login.php
      - User: admin@example.com
      - Password: admin

7) Change the default user email and password via the “User Account Management” link on the dashboard. 

8) Add clients, specialists, or managers  via the “User Account Management” link on the dashboard.



# Who Collaborated within this project

Sprint 1

Database Creation - Price

View Tickets - Kasper

Create Tickets - Brevory

Add Comments - Brian

Close Tickets - Kasper

Sprint 2

Add tickets to FAQ - Brian

View Metrics - Brevory

Managers create users - Price

Sprint 3

Specalist and Manager can Generate Reports - Kasper

New Users create accounts - Price

Edit Tickets - Brevory & Brian
