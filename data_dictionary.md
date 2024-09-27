# Data Dictionary

ทุก table มี timestamp: created_at, updated_at ละไว้

## blogs

บทความ

| Column       | Description                                            | Type                 | Attributes            |
|--------------|--------------------------------------------------------|----------------------|-----------------------|
| id           |                                                        | unsigned big integer | primary key           |
| category_id  | foreign key to [categories](#categories) table         | unsigned big integer | foreign key, nullable |
| slug         | URL slug                                               | varchar              | nullable              |
| published_at | วันเวลาที่ publish (null หรือ published < now = draft) | datetime             | nullable              |


## blog_translations

เก็บ Localized content สำหรับบทความ

| Column           | Description                                            | Type                 | Attributes  |
|------------------|--------------------------------------------------------|----------------------|-------------|
| id               |                                                        | unsigned big integer | primary key |
| item_id          | foreign key to [blogs](#blogs) table                   | unsigned big integer | foreign key |
| locale           | ภาษา                                                   | varchar              |             |
| title            | URL slug                                               | varchar              | nullable    |
| body             | วันเวลาที่ publish (null หรือ published < now = draft) | medium text          | nullable    |
| meta_title       | Meta title สำหรับ SEO                                  | varchar              | nullable    |
| meta_description | Meta description สำหรับ SEO                            | varchar              | nullable    |


## categories

หมวดหมู่ (general purpose)

| Column | Description                                      | Type                   | Attributes  |
|--------|--------------------------------------------------|------------------------|-------------|
| id     |                                                  | unsigned big integer   | primary key |
| type   | Enum บอกว่าเป็นหมวดหมู่ของ model ไหน เช่น `blog` | varchar                |             |
| slug   | URL slug                                         | varchar                | nullable    |
| sort   | เรียงลำดับหมวดหมู่                               | unsigned small integer | nullable    |


## category_translations

เก็บ Localized content สำหรับหมวดหมู่

| Column      | Description                                    | Type                 | Attributes  |
|-------------|------------------------------------------------|----------------------|-------------|
| id          |                                                | unsigned big integer | primary key |
| item_id     | foreign key to [categories](#categories) table | unsigned big integer | foreign key |
| locale      | ภาษา                                           | varchar              |             |
| name        | ชื่อหมวดหมู่                                   | varchar              | nullable    |
| description | คำอธิบายหมวดหมู่                               | text                 | nullable    |


## countries

ประเทศ

| Column         | Description                 | Type                 | Attributes  |
|----------------|-----------------------------|----------------------|-------------|
| id             |                             | unsigned big integer | primary key |
| name_th        | ชื่อประเทศภาษาไทย           | varchar              |             |
| name_en        | ชื่อประเทศภาษาอังกฤษ        | varchar              |             |
| code           | รหัสประเทศ 2 หลัก (Alpha-2) | varchar              |             |     
| phone          | เบอร์โทรระหว่างประเทศ       | varchar              |             |                                                                       | big integer           | nullable    |
| continent      | ชื่อทวีป                    | varchar              |             |                                                                       | big integer           | nullable    |
| continent_code | รหัสทวีป                    | varchar              |             |                                                                       | big integer           | nullable    |
| alpha_3        | รหัสประเทศ 3 หลัก (Alpha-3) | varchar              |             |                                                                       | big integer           | nullable    |


## districts

อำเภอ/ตำบลในประเทศไทย

| Column      | Description                                                        | Type                 | Attributes  |
|-------------|--------------------------------------------------------------------|----------------------|-------------|
| id          |                                                                    | unsigned big integer | primary key |
| province_id | foreign key to [provinces](#provinces) table                       | unsigned big integer | foreign key |
| name_th     | ชื่ออำเภอภาษาไทย                                                   | varchar              |             |
| name_en     | ชื่ออำเภอภาษาอังกฤษ                                                | varchar              |             |
| sid         | sid จาก json seed ช่วยในการ map ระหว่าง seed ข้อมูล ไม่ได้เอามาใช้ | varchar              | nullable    |                                                                        | big integer           | nullable    |


## invites

Invite ผู้ใช้งาน

| Column  | Description        | Type                 | Attributes  |
|---------|--------------------|----------------------|-------------|
| id      |                    | unsigned big integer | primary key |
| email   | email ที่ invite   | unsigned big integer | foreign key |
| token   | Unique token       | varchar              |             |
| user_id | User id ที่ invite | unsigned big integer | foreign key |


## media

สำหรับเก็บ media เช่นรูปภาพ (จาก package [spatie/laravel-medialibrary](https://spatie.be/docs/laravel-medialibrary))

## incoming_request_logs

สำหรับเก็บ log API ที่ให้ 3rd Party ยิง

| Column          | Description                               | Type                   | Attributes  |
|-----------------|-------------------------------------------|------------------------|-------------|
| id              |                                           | unsigned big integer   | primary key |
| method          | http method เช่น `GET`, `POST`            | varchar                |             |
| ip              | ip ที่เรียก API                           | varchar                |             |
| uri             | uri ของ API ที่โดนเรียก                   | varchar                | nullable    |
| header          | header ของ request                        | json                   | nullable    |
| body            | payload ของ request                       | text                   | nullable    |
| response_status | response status เช่น: `200`, `404`, `500` | unsigned small integer | nullable    |
| response_header | header ที่ API MyWaWa ตอบกลับไป           | json                   | nullable    |
| response        | response body ที่ API MyWaWa ตอบกลับไป    | text                   | nullable    |

## outgoing_request_logs

สำหรับเก็บ log เวลายิง request หา 3rd party API

| Column          | Description                               | Type                   | Attributes  |
|-----------------|-------------------------------------------|------------------------|-------------|
| id              |                                           | unsigned big integer   | primary key |
| class           | ชื่อ class ที่ยิง request                 | varchar                | nullable    |
| method          | http method เช่น `GET`, `POST`            | varchar                |             |
| host            | hostname ของ request                      | varchar                |             |
| uri             | uri ของ request                           | varchar                | nullable    |
| header          | header ของ request                        | json                   | nullable    |
| body            | payload ของ request                       | text                   | nullable    |
| status          | response status เช่น: `200`, `404`, `500` | unsigned small integer | nullable    |
| time            | เวลาที่ใช้ในการตอบ request (วินาที)       | decimal(8,5)           | nullable    |
| response_header | response header                           | json                   | nullable    |
| response        | response body                             | text                   | nullable    |

## password_reset_tokens

Password reset tokens

| Column | Description   | Type    | Attributes  |
|--------|---------------|---------|-------------|
| email  | Email address | varchar | primary key |
| token  | Unique token  | varchar |             |


## permissions

สำหรับเก็บ permission (สิทธิ์) (จาก package [spatie/laravel-permission](https://spatie.be/docs/laravel-permission/))
มีการเพิ่ม columns ที่ไม่มีใน package เช่น name_th, name_en

| Column     | Description                | Type                 | Attributes  |
|------------|----------------------------|----------------------|-------------|
| id         |                            | unsigned big integer | primary key |
| name       | ชื่อ Permission (internal) | unsigned big integer | foreign key |
| name_th    | ชื่อตำแหน่ง ภาษาไทย        | varchar              | nullable    |
| name_en    | ชื่อตำแหน่ง ภาษาอังกฤษ     | varchar              | nullable    |
| guard_name | auth guard (default: web)  | varchar              | big integer | nullable    |

## provinces

สำหรับจังหวัดในประเทศไทย

| Column    | Description                                                                  | Type                 | Attributes  |
|-----------|------------------------------------------------------------------------------|----------------------|-------------|
| id        |                                                                              | unsigned big integer | primary key |
| region_id | foreign key to [regions](#regions) table                                     | unsigned big integer | foreign key |
| name_th   | ชื่อจังหวัดภาษาไทย                                                           | varchar              |             |
| name_en   | ชื่อจังหวัดภาษาอังกฤษ                                                        | varchar              |             |
| iso3166_2 | code [ISO 3166-2:TH](https://en.wikipedia.org/wiki/ISO_3166-2:TH) ของจังหวัด | varchar              |             |                                                                        | big integer           | nullable    |

## regions

สำหรับภาคในประเทศไทย

| Column  | Description       | Type                 | Attributes  |
|---------|-------------------|----------------------|-------------|
| id      |                   | unsigned big integer | primary key |
| name_th | ชื่อภาคภาษาไทย    | varchar              |             |
| name_en | ชื่อภาคภาษาอังกฤษ | varchar              |             |

## roles

สำหรับเก็บ role (ตำแหน่ง) (จาก package [spatie/laravel-permission](https://spatie.be/docs/laravel-permission/))

มีการเพิ่ม columns ที่ไม่มีใน package เช่น name_th, name_en, business_id, is_salesperson

| Column         | Description                                    | Type                 | Attributes  |
|----------------|------------------------------------------------|----------------------|-------------|
| id             |                                                | unsigned big integer | primary key |
| name           | ชื่อ Role (internal)                           | unsigned big integer | foreign key |
| name_th        | ชื่อตำแหน่ง ภาษาไทย                            | varchar              | nullable    |
| name_en        | ชื่อตำแหน่ง ภาษาอังกฤษ                         | varchar              | nullable    |
| guard_name     | auth guard (default: web)                      | varchar              |             |                                                                        | big integer           | nullable    |

## sessions

สำหรับเก็บ session ของผู้ใช้งาน (สร้างจาก Laravel)

| Column        | Description | Type                 | Attributes      |
|---------------|-------------|----------------------|-----------------|
| id            |             | varchar              | primary key     |
| user_id       | User ID     | unsigned big integer | nullable, index |
| ip_address    | IP Address  | varchar              | nullable        |
| user_agent    | User Agent  | text                 | nullable        |
| payload       |             | long text            |                 |                                                                        | big integer           | nullable    |
| last_activity | unix time   | integer              |                 |                                                                        | big integer           | nullable    |


## subdistricts

สำหรับตำบล/แขวงในประเทศไทย

| Column      | Description                                                        | Type                 | Attributes  |
|-------------|--------------------------------------------------------------------|----------------------|-------------|
| id          |                                                                    | unsigned big integer | primary key |
| district_id | foreign key to [districts](#districts) table                       | unsigned big integer | foreign key |
| name_th     | ชื่อตำบลภาษาไทย                                                    | varchar              |             |
| name_en     | ชื่อตำบลภาษาอังกฤษ                                                 | varchar              |             |
| zip         | zip code                                                           | varchar              |             |   
| sid         | sid จาก json seed ช่วยในการ map ระหว่าง seed ข้อมูล ไม่ได้เอามาใช้ | varchar              | nullable    |    


## taggables

Pivot table สำหรับเก็บ tag ของ model อื่นๆ

| Column        | Description                        | Type                 | Attributes  |
|---------------|------------------------------------|----------------------|-------------|
| tag_id        | foreign key to [tags](#tags) table | unsigned big integer | foreign key |
| taggable_id   | Polymorphic model                  | varchar              |             |
| taggable_type | Polymorphic ID                     | unsigned big integer |             |


## tags

Tag (general purpose)

| Column | Description                                      | Type                 | Attributes  |
|--------|--------------------------------------------------|----------------------|-------------|
| id     |                                                  | unsigned big integer | primary key |
| type   | Enum บอกว่าเป็นหมวดหมู่ของ model ไหน เช่น `blog` | varchar              | nullable    |
| name   | tag                                              | varchar              |             |


## users

บัญชีผู้ใช้งาน

| Column            | Description                                                         | Type                 | Attributes       |
|-------------------|---------------------------------------------------------------------|----------------------|------------------|
| id                |                                                                     | unsigned big integer | primary key      |
| title             | คำนำหน้าชื่อ เช่น Mr., Ms.                                          | varchar              | nullable         |
| first_name        | ชื่อผู้ใช้                                                          | varchar              |                  |
| last_name         | นามสกุลผู้ใช้                                                       | varchar              | nullable         |
| email             | ที่อยู่                                                             | varchar              |                  |
| email_verified_at | วันเวลาที่ยืนยัน email, ยังไม่ยืนยัน email ถ้า null                 | timestamp            | nullable         |
| phone             | เบอร์โทรศัพท์ ใน format E.164 (ตัวอย่าง +66812345678)               | varchar              | nullable         |
| password          | รหัสผ่าน (Hashed) ถ้าเป็น null = invite ไป แต่ยังไม่ได้ตั้งรหัสผ่าน | varchar              | nullable         |
| remember_token    | Token remember me session                                           | varchar              | nullable         |
| email_unique      | computed column สำหรับ check unique email + deleted_at              | varchar              | nullable, unique |
| deleted_at        | Soft deleted if not null                                            | timestamp            | nullable         |

