# Phalconmerce events list

use https://stackedit.io/app# to edit MarkDown online

| Component | Event | Params | Description |
|--|--|--|--|
| StockeService | **stock-service:afterStockUpdate** | Product | after saving new quantity to a product |
| StockeService | **stock-service:productStockEmpty**| Product | after saving quantity=0 to a product |
| StockeService | **frontend:beforeRegisterServices**| Product | before registering all services in frontend Module |
| StockeService | **frontend:afterRegisterServices**| Product | after registering all services in frontend Module |