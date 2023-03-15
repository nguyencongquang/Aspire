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

- Login as admin
```bash
  POST /api/auth/login
  
```
form-data
```bash
  email:admin@aspire.com
  password:admin@#$%
  
```

- Login as customer
```bash
  POST /api/auth/login
  
```
form-data
```bash
  email:customer@aspire.com
  password:customer@#$%  
```
- Authenticate with bearer token generated from login

example Authorization
```
curl --location --request POST '127.0.0.1:8000/api/customer/payment/6/pay' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 2|4SEf5FSHo6TaE24pRBbWKZpT9Wgaz0EbkgnouNGR' \
--form 'amount="3337.34"'
```

- Customer creates loan
```bash
  POST /api/customer/createLoan
  ```
example form-data
```bash
  term:3
  amount:10000  
```

- Customer view loan
```bash
  GET /api/customer/loan/{loanId}  
```

- Customer add repayment
```bash
  POST /api/customer/payment/{scheduleRepaymenId}/pay  
```

example form-data
```bash
  amount:3337.34
```

- Customer view loan
```bash
  GET /api/customer/loan/{loanId}  
```

- Admin approves loan
```bash
  POST /api/admin/approveLoan
```

example form-data
```bash
 loanId:1
```

## Unit Test
- Create a database named aspire_test 
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