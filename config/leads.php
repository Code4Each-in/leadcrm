<?php

return [
    // The Account Manager's "Update Lead Status" control (Hold / Lost /
    // Close) on Lead Details. Hidden for now - the routes, policy
    // (LeadPolicy::updateWorkflowStatus()) and workflow behind it are
    // unchanged, so setting this to true brings the buttons back.
    'am_status_actions' => (bool) env('LEAD_AM_STATUS_ACTIONS', false),
];
