## How to config timezone for MySQL

เนื่องจากมีการใช้ MySQL function convert_tz ซึ่งอาจจะใช้ไม่ได้ ถ้ายังไม่ได้ config timezone ไว้ก่อน

<a href="https://stackoverflow.com/questions/14454304/convert-tz-returns-null/14454465#14454465" target="_blank">stackoverflow link</a>

เมื่อลงสำเร็จ จะมี table time_zone_* อยู่ใน database ชื่อ mysql

ลอง run query `SELECT CONVERT_TZ('2004-01-01 12:00:00','UTC','Asia/Bangkok');`
ถ้าได้ค่า วันเวลา ไม่เป็น null ถือว่า install timezone ถูกต้อง
