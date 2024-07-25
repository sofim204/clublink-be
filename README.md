# SellOn Online Marketplace

SellOn is one of project by GITS Indonesia and CSI. For creating API and CMS. This Documentation are classified for use only with `GITS`, `CSI`, and `SellOn team`.

## Specification

SellOn Online Marketplace production environment:

- Nginx
- PHP v7.1
- MariaDB v10.10 or MySQL > 5.X
- Centos v7.1 with 50GB minimal 2 CPUs and 4GB RAM minimum
- CDN provider for Images mirroring

SellOn Online Marketplace development:

- XAMPP with PHP 7
- MariaDB or MySQL
- GIT

## Framework

This project using Seme Framework for Admin CMS and API. Here is the resources links:

- Github: https://github.com/drosanda/seme-framework-v3
- Documentation: https://seme.nyingspot.com/
- Atom Snippet: https://atom.io/packages/SemeFramework

## Instalation (Development)

For installation in development mode, please follow this step:

- Clone this source relative to `localhost/sellon` directory. E.g. `d:/xampp/htdocs/sellon`. Please don't change this :D Or if you have knowledge for configuring virtual host, you can put this source into HTTP_HOST relative directory.
- Database file are imported from sql directory.
- Make sure the database name is The SQL file you can get from this source in sql directory named `s3demo_sellon.sql`.

### Instalation (Production)

Same as installation for development, you can customize the configuration to fit your production environment. Please check the following:

- `app/config/config.php` has valid base_url and method. For Linux server we use REQUEST_URI method for uri routing.
- `app/config/database.php` has valid database configuration.

## Admin CMS

Admin CMS url by default is mastermind, unless someone has changed it :D. Example: `localhost/sellon/mastermind`. To logged in please input:

- username: `mimind`
- password: `admin123`

## Interfaces

SellOn online Marketplace if classified by interface, consist of:

- API for Mobile
- Admin CMS
- User Interface for reseting password, activating account, and so on..
- Landing Page

### API Mobile Modules

Here is the list of modules:

- nation
- Products
  - Product Category
  - Weight Class
  - Condition Class
  - Wishlist
  - Product Images
- Free Products
  - Product Images
- Buy It All / Bulk Sale
  - Visit / Leaved
  - Property Agents
- User
  - Buyer
  - Seller
- Cart
- Order / Checkout
- Setting / Configuration
- Chat
- Review and Ratings

#### More about API Mobiles

Here is the guidelines for using API.

- Endpoint will be {{base_url}}api_mobile/
- API requires nation_code, apikey and apisess for some api that required authentification.
- Default values: `nation_code: 65`, `apikey: kmz373ac`, and `apisess: 65KMZDS` unless someone changed it.

#### Special Notes for API_MOBILE

- If you want to force logout all mobile user you can delete api_mobile_token in b_user table. Example: `UPDATE b_user SET api_mobile_token = '' WHERE id > 2`.
- If you want to change APIKEY, you can found it on a_apikey table.
- API Mobile documentation can be found in `postman.json` file in the root of this folder.

#### Runner / Unit Test

We have include the RUNNER for testing the APIs automatically. You can found it in `app/controller/runner`. For using runner, simply open the browser and point to runner controller.

Example: `http://localhost/sellon/runner/akun_buyer/list`.

If you want to develop new runner or add some assertion to runner, simply edit the runner file and fit to your purpose. Our runner has many example.

### Third Party / External API

Here is the list of third party that inclided in SellOn Online Marketplace project:

- Payment Method:
  - 2c2p
- Shipping Method:
  - Gogovan
  - QXpress

## 2C2P Special Notes

The library of 2c2p location in `kero/lib/2c2p`. We only use Payment Inquiry API for payment verification. Please make sure the following:

- If in sandbox mode please fill the base url API in SANBOX constant
- If in production model please fill the base url API in PRODUCTION constant. For further information you can refer to 2c2p documentation.
- We use CRON job for validating the payments. Example: `/usr/bin/php /home/sellon/sites/index.php api_cron payment check`.
- Before cron activated, please make sure the properties of `$mwurl` in `app/controller/api_cron/payment`.php has valid static url.

## Database Schema

In this section we will introduce you the database schema that you can find out the purpose of each table.

We have use `PhpMyAdmin` for Database management.

Each table has 2 primary keys at least. `nation_code` and `id` for sequence.

If you wan to joined the tables, you have to join this 2 primary key to another table.

In the another table, you have to joined keys with more than 2 key. Example joined `d_order_alamat` with `b_user_alamat`, you have to joined columns `b_user_id`, `b_user_alamat_id`, and `nation_code`.

Here is the list of tables:

- `a_pengguna` is for administrator user.
- `a_modules` is for modules user. Admin CMS lookup this table for sidebar menus.
- `b_user` is for user purpose. Sellon has 2 different user, there is Buyer and Seller. Buyer and Seller are saved in this table with no specific identifier.
- `b_user_alamat` is for user addresses list.
- `c_produk is` for seller products
- `d_order` is for buyer ordering information. Contain about payment and order status.
- `d_order_detail` is for order detail information. Contain about ordered product, shipment Status, seller status and buyer status. Can be joined
- `d_order_alamat` is for billing and shipping address.
- `d_order_proses` is for order history list.

### Database Special Notes

- If you want to reset the password of admin, simply use this query to reset password admin in ID 1. `UPDATE a_pengguna SET password = '$2y$10$OHXcEgcWzHZSxsAkJ9xKRe8x7cHGIoKas0HSXIcpTdNB.4TlxtMqC' WHERE nation_code = 65 AND id = 1`
- For reseting the privileges, simply use `DELETE FROM a_pengguna_module WHERE nation_code=65 AND id=1` and then `INSERT INTO a_pengguna_module(nation_code,id,a_pengguna_id,rule,tmp_active) VALUES('65','1','allowed_except','N')`
- For add modules simply add the new module into a_modules table.

## Transaction Modules

In this section will describe about order (transaction) status. Order has divided into 2 point of view:

- by Buyer. Transaction in buyer perspective one order have many product from many sellers, transaction has invoice code.
- by Seller. Transaction in seller perspective only view that products that they have been ordered by buyer.

### Technical information

Order transaction has saved into `d_order` table and `d_order_detail` table.

- `d_order` table has 4 statuses there is: `pending`,`waiting for payment`,`payment verification` and `completed`
- `d_order_detail` table has 4 different status such as:
  - `seller_status`
  - `shipment_status`
  - `buyer_status`
  - `settlement_status`

#### Transaction Status Step

Here is the list of transaction status step in normal mode:

- Pending
- Waiting For payment
- Payment verification
- Forward to seller / Seller confirmation
- Confirmed By seller or Rejected by Seller. If rejected will proceed to Order Completed.
- Deliver Product (shipment process)
- Shipment delivered. Contain about pickup, warehouse, deliver.
- Shipment succeed or shipment rejected or shipment cancelled. If cancelled or rejected system will obtain new pickup order.
- Buyer Accept or Buyer Reject. If buyer rejected will triggered complain and will proceed to completed order.
- Completed Order. Can be achieved if all ordered product achieve its final state.

### Transaction Status Table

| Status                                             | d_order.order_status | d_order.payment_status | d_order_detail.seller_status | d_order_detail.shipment_status | d_order_detail.buyer_status | d_order_detail.settlement_status | Notes                                                                               |     |
| -------------------------------------------------- | -------------------- | ---------------------- | ---------------------------- | ------------------------------ | --------------------------- | -------------------------------- | ----------------------------------------------------------------------------------- | --- |
| Pending                                            | pending              |                        |                              |                                |                             |                                  |                                                                                     |     |
| Waiting For Payment                                | waiting_for_payment  |                        |                              |                                |                             |                                  |                                                                                     |     |
| Payment Verification                               |                      | paid                   |                              |                                |                             |                                  | If failed, order status will back to waiting_for_payment                            |     |
| Forward to Seller (Waiting for seller confirmation | forward_to_seller    | paid                   | unconfirmed                  |                                |                             |                                  |                                                                                     |     |
| Rejected by Seller                                 | forward_to_seller    | paid                   | rejected                     |                                |                             |                                  | Will marked as completed order and add to refund list                               |     |
| Process                                            | foward_to_seller     | paid                   | confirmed                    | wait                           |                             |                                  |                                                                                     |     |
| Delivered                                          | forward_to_seller    | paid                   | confirmed                    | process                        |                             |                                  |                                                                                     |     |
| Shipment Process (Pickup, Warehouse or Sent)       | foward_to_seller     | paid                   | confirmed                    | delivered                      | wait                        |                                  |                                                                                     |     |
| Shipment Fail                                      | foward_to_seller     | paid                   | confirmed                    | rejected or cancelled          | wait                        |                                  | System have to recreate pickup order and change ordered product status into process |     |
| Shipment Delivered                                 | foward_to_seller     | paid                   | confirmed                    | suceed                         | wait                        |                                  |                                                                                     |     |
| Buyer Accept                                       | forward_to_seller    | paid                   | confirmed                    | delivered or succeed           | Accept                      |                                  |                                                                                     |     |
| Buyer Rejected                                     | foward_to_seller     | paid                   | confirmed                    | succeed                        | rejected                    |                                  | System have to proceed the current ordered product into return and refund list      |     |
| Order Completed                                    | completed            |                        |                              |                                |                             |                                  |                                                                                     |     |
