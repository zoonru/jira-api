# jira-api
Working with jira - create tasks and reports, using badoo jira api tool

HOW TO START:

* Collect dependencies from composer.json
* Add file "config.php", it should be look like:
"<?php
 
 $login = '{your login in jira}';
 $api_key = '{your jira api key}';"
* Script is ready to use!

USAGE:

for create tasks:
php api.php -a create -k MAIN-15528 [-d 1] [-f 2] -s 360 

to get report:
php api.php -a report -s 360 -t a

Option list:

* -a action     It's an action, that script should to do. Where are two actions - "create" or "report"

-a create OPTIONS:

* -k feature key                    Feature key from jira, example MAIN-15528
* -d dev tasks count, optional      Count of dev tasks we want to create, example 1
* -f front tasks count, optional    Count of front tasks we want to create, example 2
* -s sprint id                      Sprint id from jira, example 360
