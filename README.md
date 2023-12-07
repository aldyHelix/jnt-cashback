

1. `docker build -t  jnt-cashback .`
2.  `docker run -p 8000:80 --name jnt-cashback-container jnt-cashback
3.  ngrok http --host-header=rewrite https://jnt-cashback.localtest:443
4. php artisan queue:work -v --stop-when-empty -> do it in shell / cron job 


To create a shell script that runs php artisan horizon on startup in Linux, you can follow these steps:

Create a new file in the /etc/init.d/ directory called horizon.
Open the file in a text editor and add the following contents:
bash
Download
Copy code
```
#!/bin/bash

### Beginning of horizon init script

NAME=horizon
DESC="PHP Artisan Horizon"

case "$1" in
    start)
        echo "Starting $NAME..."
        cd /path/to/your/project
        php artisan horizon > /dev/null 2>&1 &
        echo "$NAME started."
        ;;
    stop)
        echo "Stopping $NAME..."
        cd /path/to/your/project
        php artisan horizon:stop > /dev/null 2>&1 &
        echo "$NAME stopped."
        ;;
    *)
        echo "Usage: /etc/init.d/$NAME {start|stop}"
        exit 1
esac

exit 0

### End of horizon init script
```
Replace /path/to/your/project with the path to your Laravel project.
Save and close the file.
Make the script executable by running the command sudo chmod +x /etc/init.d/horizon
Enable the script to start at boot time by running the command sudo update-rc.d horizon defaults
Reboot your machine to test if the script works correctly.
This script will start the php artisan horizon command when the system boots up and will also allow you to stop the service using the sudo /etc/init.d/horizon stop command.

You can also use systemd instead of init.d, you can create a .service file in /etc/systemd/system/ with the same configuration as the script above, and enable it to start at boot time using sudo systemctl enable horizon.service
