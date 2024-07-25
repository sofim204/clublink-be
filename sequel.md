# Sequel

List of any sequel for administration purpose.

## Truncate order related table

This is sequel for truncate order related table.

TRUNCATE `d_cart`;
TRUNCATE `d_order`;
TRUNCATE `d_order_alamat`;
TRUNCATE `d_order_detail`;
TRUNCATE `d_order_detail_item`;
TRUNCATE `d_order_proses`;
TRUNCATE `d_pemberitahuan`;
TRUNCATE `e_chat`;
TRUNCATE `e_chat_attachment`;
TRUNCATE `e_complain`;
TRUNCATE `e_rating`;

### Rollback to Unconfirmed by admin

This is sequel for Rollback rejected item by buyer to Unconfirmed by admin

UPDATE `d_order_detail_item` SET `settlement_status` = 'solved_to_buyer' WHERE `buyer_status` = 'rejected' AND `settlement_status` = 'paid_to_buyer'

SELECT d_order_id, d_order_detail_id FROM d_order_detail_item WHERE `buyer_status` = 'rejected' AND `settlement_status` = 'solved_to_buyer'

### Fill Up Stock

This sequel for fill up all product stock to 999.

UPDATE c_produk SET stok = 999 WHERE 1

### Rollback to Unconfirmed by buyer

This is sequel for Rollback confirmed item(s) by buyer to Unconfirmed by buyer

UPDATE d_order_detail SET is_calculated=0 WHERE is_calculated=0
UPDATE `d_order_detail_item` SET buyer_status = 'wait' WHERE 1

UPDATE d_order_detail SET buyer_confirmed="unconfirmed",is_calculated=0 WHERE 1

### Global Mode SQL

View sql_mode:
`SHOW VARIABLES LIKE 'sql_mode';`

This is unstrict mode:
`SET sql_mode = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';`

set Strict Mode:
`SET sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';`

### Delete product related dummy data

this is query for delete product related dummy data, please make sure you have disable foreignkey check before running this query.

```
SET FOREIGN_KEY_CHECKS=0;
TRUNCATE `c_bulksale`;
TRUNCATE `c_bulksale_foto`;
TRUNCATE `c_freeproduk`;
TRUNCATE `c_freeproduk_foto`;
TRUNCATE `c_produk`;
TRUNCATE `c_produk_foto`;
TRUNCATE `c_produk_lapor`;
TRUNCATE `c_produk_laporan`;
TRUNCATE `c_promo`;
TRUNCATE `d_cart`;
TRUNCATE `d_order`;
TRUNCATE `d_order_alamat`;
TRUNCATE `d_order_detail`;
TRUNCATE `d_order_detail_item`;
TRUNCATE `d_order_detail_pickup`;
TRUNCATE `d_order_proses`;
TRUNCATE `d_pemberitahuan`;
TRUNCATE `d_wishlist`;
TRUNCATE `e_chat`;
TRUNCATE `e_chat_attachment`;
TRUNCATE `e_chat_participant`;
TRUNCATE `e_complain`;
TRUNCATE `e_rating`;
SET FOREIGN_KEY_CHECKS=1;
```

### Delete user related dummy data

this is query for user related dummy data, please make sure you have disable foreignkey check before running this query.

```
SET FOREIGN_KEY_CHECKS=0;
TRUNCATE `b_user`;
TRUNCATE `b_user_alamat`;
TRUNCATE `b_user_bankacc`;
TRUNCATE `b_user_card`;
TRUNCATE `b_user_productwanted`;
TRUNCATE `b_user_setting`;
TRUNCATE `c_bulksale`;
TRUNCATE `c_bulksale_foto`;
TRUNCATE `c_freeproduk`;
TRUNCATE `c_freeproduk_foto`;
TRUNCATE `c_produk`;
TRUNCATE `c_produk_foto`;
TRUNCATE `c_produk_lapor`;
TRUNCATE `c_produk_laporan`;
TRUNCATE `c_promo`;
TRUNCATE `d_cart`;
TRUNCATE `d_order`;
TRUNCATE `d_order_alamat`;
TRUNCATE `d_order_detail`;
TRUNCATE `d_order_detail_item`;
TRUNCATE `d_order_detail_pickup`;
TRUNCATE `d_order_proses`;
TRUNCATE `d_pemberitahuan`;
TRUNCATE `d_wishlist`;
TRUNCATE `e_chat`;
TRUNCATE `e_chat_attachment`;
TRUNCATE `e_chat_participant`;
TRUNCATE `e_complain`;
TRUNCATE `e_rating`;
SET FOREIGN_KEY_CHECKS=1;
```

## Update encryption (convert)

here is sql for converting plain text to encrypted text

### b_user_bankacc

Please make sure the column name mentioned in this query are changing from varchar to BLOB or VARBINARY.

```
UPDATE b_user_bankacc SET `nama` = AES_ENCRYPT(`nama`,'ENCRYPTION_KEY'), `nomor` = AES_ENCRYPT(`nomor`,'ENCRYPTION_KEY') WHERE COALESCE(AES_DECRYPT(`nama`,'ENCRYPTION_KEY'),'-') LIKE '-';
```

### b_user

Please make sure the column name mentioned in this query are changing from varchar to BLOB or VARBINARY.

```
UPDATE b_user SET `fnama` = AES_ENCRYPT(`fnama`,'ENCRYPTION_KEY'), `telp` = AES_ENCRYPT(`telp`,'ENCRYPTION_KEY'), `email` = AES_ENCRYPT(`email`,'ENCRYPTION_KEY') WHERE 1;
```

### b_user_alamat

Please make sure the column name mentioned in this query are changing from varchar to BLOB or VARBINARY.

```
UPDATE b_user_alamat SET `penerima_nama` = AES_ENCRYPT(`penerima_nama`,'ENCRYPTION_KEY'), `penerima_telp` = AES_ENCRYPT(`penerima_telp`,'ENCRYPTION_KEY'), `alamat2` = AES_ENCRYPT(`alamat2`,'ENCRYPTION_KEY') WHERE 1;
```

### d_order_alamat

Please make sure the column name mentioned in this query are changing from varchar to BLOB or VARBINARY.

```
UPDATE d_order_alamat SET `nama` = AES_ENCRYPT(`nama`,'ENCRYPTION_KEY'), `telp` = AES_ENCRYPT(`telp`,'ENCRYPTION_KEY'), `alamat2` = AES_ENCRYPT(`alamat2`,'ENCRYPTION_KEY') WHERE 1;
```

### d_order_detail_pickup

Please make sure the column name mentioned in this query are changing from varchar to BLOB or VARBINARY.

```
UPDATE d_order_detail_pickup SET `nama` = AES_ENCRYPT(`nama`,'ENCRYPTION_KEY'), `telp` = AES_ENCRYPT(`telp`,'ENCRYPTION_KEY'), `alamat2` = AES_ENCRYPT(`alamat2`,'ENCRYPTION_KEY') WHERE 1;
```

### b_user_card

Please make sure the column name mentioned in this query are changing from varchar to BLOB or VARBINARY.

```
UPDATE b_user_card SET `nomor` = AES_ENCRYPT(`nomor`,'ENCRYPTION_KEY') WHERE 1;
```
