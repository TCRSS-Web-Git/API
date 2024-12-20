apt-get update && apt-get install -y libfreetype6-dev \
                libjpeg62-turbo-dev \
                libpng-dev \
                libwebp-dev \
        && docker-php-ext-configure gd --with-freetype --with-webp  --with-jpeg
docker-php-ext-install gd &&
mkdir -p /home/site/ini &&
cp /home/site/wwwroot/deployment/startup.sh /home/site/ini/startup.sh &&
cp /home/site/wwwroot/deployment/extensions.ini /home/site/ini/extensions.ini &&
cp /home/site/wwwroot/deployment/default /etc/nginx/sites-available/default && service nginx reload
