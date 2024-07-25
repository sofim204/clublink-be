# Cron Configuration
Cronjob consists of:
1. Daily
2. Per 15 minute(s)

## Use PHP native execution
here is crontab example for php execution

```
0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48 8,9,10,11,12,13,14,15,16,17,18,19 * * * /usr/bin/php -q /var/www/html/index.php api_cron five_min index >/dev/null 2>&1
0,10,20,30,40,50 0,1,2,3,4,5,6,7,20,21,22,23 * * * /usr/bin/php -q /var/www/html/index.php api_cron five_min index >/dev/null 2>&1
0 1 * * *	/usr/bin/php -q /var/www/html/index.php api_cron daily index >/dev/null 2>&1
```

## cURL execution
here is crontab example for curl

```
0,3,6,9,12,15,18,21,24,27,30,33,36,39,42,45,48 8,9,10,11,12,13,14,15,16,17,18,19 * * * /usr/bin/curl -m 120 -s https://cms-sgmaster.sellon.net/api_cron/five_min/ &>/dev/null
0,10,20,30,40,50 0,1,2,3,4,5,6,7,20,21,22,23 * * * /usr/bin/curl -m 120 -s https://cms-sgmaster.sellon.net/api_cron/five_min/ &>/dev/null
0 1 * * *	/usr/bin/curl -m 120 -s https://cms-sgmaster.sellon.net/api_cron/daily/ &>/dev/null
```

## cURL execution
here is crontab example for curl per two minutes

```
* */2 * * * /usr/bin/curl -m 120 -s https://cms-sgmaster.sellon.net/api_cron/five_min/ &>/dev/null
0 1 * * *	/usr/bin/curl -m 120 -s https://cms-sgmaster.sellon.net/api_cron/daily/ &>/dev/null
```
