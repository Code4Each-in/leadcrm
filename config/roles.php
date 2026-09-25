<?php

// Role IDs, sourced from .env - must match the ids seeded by
// database/seeders/RoleSeeder.php. Application code should identify a
// role via these ids (see App\Models\User's is*() helpers), never by
// the role's display name, since that name can be renamed at any time
// via the Roles UI.
return [
    'super_admin' => (int) env('ROLE_SUPER_ADMIN_ID', 1),
    'admin' => (int) env('ROLE_ADMIN_ID', 2),
    'mis' => (int) env('ROLE_MIS_ID', 3),
    'ae' => (int) env('ROLE_AE_ID', 4),
    'qa' => (int) env('ROLE_QA_ID', 5),
    'manager' => (int) env('ROLE_MANAGER_ID', 6),
];
