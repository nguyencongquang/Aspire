## Installation

- Change db connection in .env
```
DB_CONNECTION=mysql
DB_HOST=your_db_host
DB_PORT=your_db_port
DB_DATABASE=aspire
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```
- Run
```bash
  composer install
```
- Run migration db
```bash
  php artisan migrate
```
- Run seeder to initialize data
```bash
  php artisan db:seed
```

- Start the local server
```bash
  php artisan serve
```
## APIs

- Login
```bash
  GET /api/auth/login
```

- Follow users
```bash
  POST /api/v1/user/{id}/follow
  Body {
    "follows" : [id1,id2] // seperated ids by comma
  }
```

- Unfollow users
```bash
  POST /api/v1/user/{id}/unfollow
  Body {
    "unfollows" : [id1,id2] // seperated ids by comma
  }
```

- Get followers, search by name
```bash
  GET /api/v1/user/{id}/followers?q=name
  
```

## Unit Test
- Change db connection in .env.testing
```
DB_CONNECTION=mysql
DB_HOST=your_db_host
DB_PORT=your_db_port
DB_DATABASE=aspire_test
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

- Run unit test
```bash
  ./vendor/bin/phpunit
  
```