<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],


        'profiles' => [
            'driver' => 'local',
            'root' => public_path('profiles'),
            'url' => env('APP_URL').'/profiles',
            'visibility' => 'public',
            'throw' => false,
        ],

        'profile_case_note' => [
            'driver' => 'local',
            'root' => public_path('profiles_case_note'),
            'url' => env('APP_URL').'/profiles_case_note',
            'visibility' => 'public',
            'throw' => false,
        ],

        'worked_case_notes' => [
            'driver' => 'local',
            'root' => public_path('worked_case_notes'),
            'url' => env('APP_URL').'/worked_case_notes',
            'visibility' => 'public',
            'throw' => false,
        ],

        'trashed' => [
            'driver' => 'local',
            'root' => public_path('trashed'),
            'url' => env('APP_URL').'/trashed',
            'visibility' => 'public',
            'throw' => false,
        ],

        'certificates' => [
            'driver' => 'local',
            'root' => public_path('certificates'),
            'url' => env('APP_URL').'/certificates',
            'visibility' => 'public',
            'throw' => false,
        ],

        'work_experience' => [
            'driver' => 'local',
            'root' => public_path('work_experience'),
            'url' => env('APP_URL').'/work_experience',
            'visibility' => 'public',
            'throw' => false,
        ],

        'cases' => [
            'driver' => 'local',
            'root' => public_path('cases'),
            'url' => env('APP_URL').'/cases',
            'visibility' => 'public',
            'throw' => false,
        ],

        'docs' => [
            'driver' => 'local',
            'root' => public_path('docs'),
            'url' => env('APP_URL').'/docs',
            'visibility' => 'public',
            'throw' => false,
        ],

        'verifications' => [
            'driver' => 'local',
            'root' => public_path('verifications'),
            'url' => env('APP_URL').'/verifications',
            'visibility' => 'public',
            'throw' => false,
        ],


        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
