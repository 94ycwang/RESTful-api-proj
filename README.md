# RESTful-api-proj
## Tech Stack 

WampServer Version 3.2.3:

- Apache  2.4.46 

- MySQL 8.0.21 

- PHP 7.3.21

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

API design doc
