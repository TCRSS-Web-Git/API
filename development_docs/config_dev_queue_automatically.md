## How to automatically run Laravel queue worker using PhpStorm

API จะใช้ Queue เพื่อลด เวลาในการทำ task ที่เสียเวลา เช่น ส่ง email, optimize รูปภาพ และอื่นๆ

สามารถ Config ให้ PhpStorm รัน queue worker อัตโนมัติได้ โดยการเพิ่ม Run/Debug Configuration ใหม่ดังนี้

1. ไปที่ `Run` > `Edit Configurations...`
2. คลิกที่ `+` แล้วเลือก `Shell Script`
3. ใส่ชื่อ `Laravel Queue'
4. เลือก Execute เป็น `Script text`
5. ใส่ Command (Script Text): `php artisan queue:work --queue=default,low`
6. Uncheck `Execute in the terminal` เพื่อให้ script ไปรันในหน้าต่าง Run แทนที่จะรันใน Terminal
7. ลองปิด PhpStorm แล้วเปิดใหม่ เพื่อทดสอบดูว่า Queue Worker ทำงานหรือไม่

<a href="https://gilbitron.me/blog/automatically-running-laravel-queue-worker-and-scheduler-using-phpstorm" target="_blank">ที่มา</a>
