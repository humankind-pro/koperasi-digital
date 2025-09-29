<?php

return [

  'defaults' => [
    'guard' => 'admin',   // default guard kita ubah ke admin
    'passwords' => 'master_admin',
],

'guards' => [
    'admin' => [
        'driver' => 'session',
        'provider' => 'master_admin',
    ],
],

'providers' => [
    'master_admin' => [
        'driver' => 'eloquent',
        'model' => App\Models\MasterAdmin::class,
    ],
],

'passwords' => [
    'master_admin' => [
        'provider' => 'master_admin',
        'table' => 'password_reset_tokens',
        'expire' => 60,
        'throttle' => 60,
    ],
],


];
