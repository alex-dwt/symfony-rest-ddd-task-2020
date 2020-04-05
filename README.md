# symfony-rest-ddd-task-2020


## How to launch the project
* make up
* make composer-install
* make init-database
* make reload-fake-data



## How to test
make run-concurrency-test  
OR  
make run-concurrency-test-2



## Examples of endpoints:

**Registration**  
curl -d "name=Name&city=Minsk&country=BLR&currency=eur" -X POST "http://localhost:8061/api/users"

**Deposit**  
curl -d "userId=5&currency=eur&amount=123.13" -X POST "http://localhost:8061/api/transactions/deposit"

**Transfer**  
curl -d "userFromId=5&currencyFrom=eur&userToId=3&currencyTo=gbp&amount=5.55" -X POST "http://localhost:8061/api/transactions/transfer"

**Get balance**  
curl "http://localhost:8061/api/users/5/balance?currency=eur"

**Get transactions list**  
curl "http://localhost:8061/api/reports/5/period?currency=eur&csv=0&dateFrom=2010-04-07 10:10:10&dateTill=2030-04-07 10:10:10"  
curl "http://localhost:8061/api/reports/5/period?currency=eur&csv=1"

**Get transactions summary**  
curl "http://localhost:8061/api/reports/5/summary?currency=eur&csv=0&dateFrom=2010-04-07 10:10:10&dateTill=2030-04-07 10:10:10"



## Where to see live SQL queries

http://localhost:8061/_profiler/latest  
"Doctrine" TAB



## DB credentials

* Port 3306
* Database name: example
* Database user: example
* Database password: example
