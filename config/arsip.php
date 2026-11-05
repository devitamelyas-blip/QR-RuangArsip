<?php

return [
    'loan_due_days' => (int) env('LOAN_DUE_DAYS', 7),
    'allowed_ip_ranges' => array_values(array_filter(
        array_map('trim', explode(',', env('ALLOWED_IP_RANGES', '')))
    )),
];
