<div>
    <h1>Job Application</h1>

    <div class="section">
        <div class="field">
            <div class="field-label">ตำแหน่งาน (Position Applied)</div>
            <div class="field-value">{{ $careerTH }} ({{ $careerEN }})</div>
        </div>
        <div class="field">
            <div class="field-label">เงินเดือนที่คาดหวัง (Salary Requirement)</div>
            <div class="field-value">{{ $salary }} ฿</div>
        </div>
    </div>

    <div class="section">
        <h2>ประวัติส่วนตัว (Personal Data)</h2>
        <div class="field">
            <div class="field-label">คำนำหน้า (Name Title)</div>
            <div class="field-value">{{ $titleTH }} ({{ $titleEN }})</div>
        </div>
        <div class="field">
            <div class="field-label">ชื่อภาษาไทย (Thai Name)</div>
            <div class="field-value">{{ $firstNameTH }}</div>
        </div>
        <div class="field">
            <div class="field-label">นามสกุลภาษาไทย (Thai Surname)</div>
            <div class="field-value">{{ $lastNameTH }}</div>
        </div>
        <div class="field">
            <div class="field-label">ขื่อเล่น (Nickname)</div>
            <div class="field-value">{{ $nickname }}</div>
        </div>
        <div class="field">
            <div class="field-label">ชื่อภาษาอังกฤษ (English Name) (ภาษาอังกฤษตัวพิมพ์ใหญ่)</div>
            <div class="field-value">{{ $firstNameEN }}</div>
        </div>
        <div class="field">
            <div class="field-label">นามสกุลภาษาอังกฤษ (English Surname) (ภาษาอังกฤษตัวพิมพ์ใหญ่)</div>
            <div class="field-value">{{ $lastNameEN }}</div>
        </div>
        <div class="field">
            <div class="field-label">วันเกิด (Date of Birth)</div>
            <div class="field-value">{{ $dateOrBirth }}</div>
        </div>
        <div class="field">
            <div class="field-label">ที่อยู่ปัจจุบัน (Present Address)</div>
            <div class="field-value">{{ $address }}</div>
        </div>
        <div class="field">
            <div class="field-label">จังหวัด (Province)</div>
            <div class="field-value">-- TODO ex: Bangkok --</div>
        </div>
        <div class="field">
            <div class="field-label">อำเภอ/เขต (District)</div>
            <div class="field-value">-- TODO ex: สวนหลวง --</div>
        </div>
        <div class="field">
            <div class="field-label">ตำบล/แขวง (Sub-district)</div>
            <div class="field-value">-- TODO ex: สวนหลวง --</div>
        </div>
        <div class="field">
            <div class="field-label">รหัสไปรษณีย์ (Postal Code)</div>
            <div class="field-value">-- TODO ex: 10250 --</div>
        </div>
        <div class="field">
            <div class="field-label">จังหวัดตามทะเบียนบ้าน (Registered Province)</div>
            <div class="field-value">-- TODO --</div>
        </div>
        <div class="field">
            <div class="field-label">เบอร์โทรศัพท์ (Phone)</div>
            <div class="field-value">{{ $phone }}</div>
        </div>
        <div class="field">
            <div class="field-label">Email</div>
            <div class="field-value">{{ $email }}</div>
        </div>
        <div class="field">
            <div class="field-label">สถานภาพทางครอบครัว (Family Status)</div>
            <div class="field-value">{{ $familyStatusTH }} ({{$familyStatusEN}})</div>
        </div>
        <div class="field">
            <div class="field-label">สถานภาพทางทหาร (Military Service)</div>
            <div class="field-value">{{ $militaryServiceTH }} ({{$militaryServiceEN}})</div>
        </div>
    </div>

    <div class="section">
        <h2>ประวัติการศึกษา (Educational)</h2>
        <div class="field">
            <div class="field-label">ระดับการศึกษา (Education Level)</div>
            <div class="field-value">{{ $educationTH }} ({{ $educationEN }})</div>
        </div>
        <div class="field">
            <div class="field-label">สาขาวิชา (Major Subject)</div>
            <div class="field-value">{{ $major }}</div>
        </div>
        <div class="field">
            <div class="field-label">สถานศึกษา (Institution)</div>
            <div class="field-value">{{ $institution }}</div>
        </div>
        <div class="field">
            <div class="field-label">เกรดเฉลี่ย (GPA)</div>
            <div class="field-value">{{ $gpa }}</div>
        </div>
    </div>
</div>
