Docker RUN :

1. `docker build -t  jnt-cashback .`
2.  `docker run -p 8000:80 --name jnt-cashback-container jnt-cashback
3.  ngrok http --set-header=rewrite https://jnt-cashback.localtest:443
