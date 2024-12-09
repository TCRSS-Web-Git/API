<?php

namespace App\Enums;

enum DepartmentType: string
{
    case CUSTOMER_SERVICE = 'Customer Service';
    case SALES_AND_MARKETING = 'Sales and Marketing';
    case HR_AND_RECRUIT = 'HR and Recruit';
    case PROCUREMENT = 'Procurement';

    public function labelEn(): string
    {
        return match ($this) {
            self::CUSTOMER_SERVICE => 'Customer Service',
            self::SALES_AND_MARKETING => 'Sales and Marketing',
            self::HR_AND_RECRUIT => 'HR and Recruit',
            self::PROCUREMENT => 'Procurement',
        };
    }

    public function labelTh(): string
    {
        return match ($this) {
            self::CUSTOMER_SERVICE => 'แผนกฝ่ายดูแลลูกค้า',
            self::SALES_AND_MARKETING => 'แผนกผ่ายขาย / การตลาด',
            self::HR_AND_RECRUIT => 'แผนกฝ่ายบุคคล',
            self::PROCUREMENT => 'แผนกการจัดซื้อจัดจ้าง',
        };
    }

    public function email(): array
    {
        return match ($this) {
            self::CUSTOMER_SERVICE => config('tcrss.mails_for_contact_us_to_customer_service'),
            self::SALES_AND_MARKETING => config('tcrss.mails_for_contact_us_to_sales_and_marketing'),
            self::HR_AND_RECRUIT => config('tcrss.mails_for_contact_us_to_hr_and_recruit'),
            self::PROCUREMENT => config('tcrss.mails_for_contact_us_to_procurement'),
        };
    }
}
