<?php

// The project runs for a single, fixed agency (AGILE ONE - seeded by
// database/seeders/AgencySeeder.php). Use this id wherever an agency
// is needed instead of looking the agency up by name.
return [
    'id' => (int) env('AGENCY_ID', 1),
];
