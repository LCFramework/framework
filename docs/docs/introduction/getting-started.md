# Getting Started

This section will help you with the base installation of LCFramework. An installation wizard 
comes included in LCFramework to automatically setup and install your website based on your configuration.

If you're intending to develop modules and themes, I highly recommend reading through the
[Laravel](https://laravel.com/docs) documentation first.

::: warning
LCFramework is currently in `alpha` status. This means there may be bugs,
and the API may still change between minor versions.
:::

## Prerequisites

This is everything you need to know before attempting to install LCFramework.

::: info
Since LastChaos is based on MySQL, LCFramework only officially supports MySQL database servers.
:::

### Requirements

PHP often comes pre-installed with most of the below extensions.

- PHP >= 8.0
- BCMath PHP Extension
- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PCRE PHP Extension
- PDO MySQL Extension
- PDO PHP Extension
- PDO SQLite Extension
- Tokenizer PHP Extension
- XML PHP Extension
- ZIP PHP Extension

## Step 1: Download

Download the latest release of LCFramework from [GitHub](#).

Then extract this release into the root of your website.
For example, on Ubuntu it'd be similar to `/var/www/<your-site-domain>`.

You will need to ensure that PHP has read and write access to the folder.
An example of how to do this on Linux based systems:

```shell
cd /var/www/example.com/html
sudo chmod -R 755 .
sudo chown $USER:www-data -R .
```

## Step 2: Web server configuration

You must ensure your web server is configured to direct all requests to the `public/index.php`
file. You should never attempt to move the `index.php` file to your projects root, 
as serving the application from the project root risks exposing many sensitive 
configuration files to the public.

If you use Apache or Nginx, you may use the following configuration files as 
a starting point. It will likely need to be customised depending on your server configuration:

::: tip
You may need to restart your web server after making any configuration changes.
:::

### Apache

```apache
<VirtualHost *:80>
    ServerName example.com
    DocumentRoot /var/www/html/example/public
    
    <Directory /var/www/html/example>
        AllowOverride All
    </Directory>
</VirtualHost>
```

### Nginx

```nginx{5}
server {
    listen 80;
    listen [::]:80;
    server_name example.com;
    root /var/www/example.com/public;
 
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
 
    index index.php;
 
    charset utf-8;
 
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
 
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
 
    error_page 404 /index.php;
 
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
 
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Step 3: Installation

You should now be ready to run through our installer. You'll tell the installer how you
want your website configured, and the installer will do the rest.

The installation steps are outlined below:

::: tip
All these configurable options can be easily changed at anytime. See the [Configuration](/docs/introduction/configuration) page.
:::

### Requirements~~~~

If you followed the [Prerequisites](#prerequisites) section, this step should be as easy
as confirming everything looks good, and moving to step 2.

### Application Settings

These are the global website settings that control how it behaves when handling requests.

::: warning
If you've enabled `Require email verification`, you must ensure you configure your email server 
on the `Email Settings` page.
:::

### Database Settings

These are the details to allow the website to connect to your database.

::: info
These settings are   for the website database, not your LastChaos server
(although they technically can be in the same database). This database will hold tables specific
to the website.
:::

### LastChaos Settings

These are the details to your LastChaos server. Your server does not have to be running when installing LCFramework.
But your server databases should already be setup for LCFramework to use.

### Email Settings

These are the details to your email server. This page is entirely optional unless you enabled `Require email verification`
on the `Application Settings` page.

### Administrator

This is where you setup your first user. This user will automatically be granted administrator privileges.
This user will also be allowed to login to your LastChaos server.

::: warning
If the provided username/email already exists in the database, the password will be updated with the hash algorithm and salt (if provided).
:::
