<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Survey data disk
    |--------------------------------------------------------------------------
    |
    | Name of the filesystem disk (see config/filesystems.php) that
    | SurveyRepository reads the survey JSON files from. Tests can override
    | this to point at a fixtures disk instead of storage/app/data.
    |
    */

    'disk' => env('SURVEYS_DISK', 'surveys'),

];
