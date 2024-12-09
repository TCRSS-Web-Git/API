<?php

return [
    'mails_for_job_application' => ! empty(env('MAILS_FOR_JOB_APPLICATION')) ? explode(',', env('MAILS_FOR_JOB_APPLICATION')) : 'hr_recruit@tcrss.com',

    'mails_for_contact_us_to_customer_service' => ! empty(env('MAILS_FOR_CONTACT_US_TO_CUSTOMER_SERVICE')) ? explode(',', env('MAILS_FOR_CONTACT_US_TO_CUSTOMER_SERVICE')) : 'customer_service@tcrss.com',
    'mails_for_contact_us_to_sales_and_marketing' => ! empty(env('MAILS_FOR_CONTACT_US_TO_SALES_AND_MARKETING')) ? explode(',', env('MAILS_FOR_CONTACT_US_TO_SALES_AND_MARKETING')) : 'sales_marketing@tcrss.com',
    'mails_for_contact_us_to_hr_and_recruit' => ! empty(env('MAILS_FOR_CONTACT_US_TO_HR_AND_RECRUIT')) ? explode(',', env('MAILS_FOR_CONTACT_US_TO_HR_AND_RECRUIT')) : 'hr_recruit@tcrss.com',
    'mails_for_contact_us_to_procurement' => ! empty(env('MAILS_FOR_CONTACT_US_TO_PROCUREMENT')) ? explode(',', env('MAILS_FOR_CONTACT_US_TO_PROCUREMENT')) : 'procurement_dept@tcrss.com',
];
