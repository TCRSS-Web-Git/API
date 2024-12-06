<?php

return [
    'mails_for_job_application' => !empty(env('MAILS_FOR_JOB_APPLICATION')) ? explode(',', env('MAILS_FOR_JOB_APPLICATION')) : 'hr_recruit@tcrss.com',
];
