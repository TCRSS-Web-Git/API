## For reviewer

Make sure these boxes are checked before approving pull request:

- [ ] Code Style (PSR-2, Vue Code Style)
- [ ] Testing ครบ และ Test ยังเร็วอยู่
- [ ] Data Dictionary
- [ ] Clean Code (SOLID)
- [ ] Pull มาลองรันในเครื่อง แล้วใช้งานได้จริง
- [ ] ไม่มี Query ที่ไม่ optimized
- [ ] เช็คว่ากัน SQL Injection ถ้ามีใช้ raw query
- [ ] Purify Output ที่มาจาก User (to prevent XSS)
- [ ] ไม่มี debug code หลงเหลืออยู่ เช่น (Log::info, dd, echo)
- [ ] Optimized static assets (รูป, svg) เพื่อลดขนาดรูป
