# RESTful-api-proj

A simple RESTful API Demo in PHP.

## Tech Stack 

WampServer Version 3.2.3:

- Apache  2.4.46 

- MySQL 8.0.21 

- PHP 7.3.21

## Environment Setup



C:\\Windows\System32\drivers\etc\hosts
```
127.0.0.1 api.local.com
::1 localhost
```

<br/>

wamp64\bin\apache\apache2.4.46\conf\extra\httpd-vhosts.conf

```
<VirtualHost *:80>
  ServerName api.local.com
  ServerAlias localapi
  DocumentRoot "${INSTALL_DIR}/www/api"
  <Directory "${INSTALL_DIR}/www/api">
    Options +Indexes +Includes +FollowSymLinks +MultiViews
    AllowOverride All
    Require local
  </Directory>
</VirtualHost>
```

## API Design

URL: http://api.local.com/1.0

### User Module

-	Register

    - post
    
    - http://api.local.com/1.0/users/register 

-	Login

    - post

    - http://api.local.com/1.0/users/login 

###  Article Module

-	Create

    - post

    - http://api.local.com/1.0/articles/ 

-	View

    - get

    - http://api.local.com/1.0/articles/:id 

-	Edit

    - put
    - http://api.local.com/1.0/articles/:id

-	Delete

    - delete

    - http://api.local.com/1.0/articles/:id

-	List - to be done

    - get

    - http://api.local.com/1.0/articles/ 

###  Database Tables 

- User

|Attribute | Type |Description |
| :-----| :----| :----|
| id | int | surrogate key |
| name | varchar(10) | username |
| password | char(32) | password (MD5) |
| create_time | timestamp | user creation time |

- Article 

|Attribute | Type | Description |
| :-----| :----| :----|
| id | int | surrogate key |
| title | varchar(60) | article title |
| content| text | article content |
| user_id | int | author |
| create_time | timestamp | article creation time |

Reference：

https://www.bilibili.com/video/BV1jb411J7zK?p=17
