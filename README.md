

1. `docker build -t  jnt-cashback .`
2.  `docker run -p 8000:80 --name jnt-cashback-container jnt-cashback
3.  ngrok http --host-header=rewrite https://jnt-cashback.localtest:443
4. php artisan queue:work -v --stop-when-empty -> do it in shell / cron job 

macos : ngrok http --host-header=rewrite https://dev-jnt-cashback.test  
