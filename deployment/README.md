# Deploy on Azure Web App Service

## Setup Start up command
- Settings > Configuration
- insert this command in startup command input
```sh
/home/site/wwwroot/deployment/startup.sh && cp /home/site/wwwroot/deployment/default /etc/nginx/sites-available/default && service nginx reload
```
- apply & save
- restart app
