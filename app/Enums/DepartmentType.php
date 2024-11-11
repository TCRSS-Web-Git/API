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

    public function email(): string
    {
        return match ($this) {
            self::CUSTOMER_SERVICE => 'customer_service@tcrss.com',
            self::SALES_AND_MARKETING => 'sales_marketing@tcrss.com',
            self::HR_AND_RECRUIT => 'hr_recruit@tcrss.com',
            self::PROCUREMENT => 'procurement_dept@tcrss.com',
        };
    }
}
