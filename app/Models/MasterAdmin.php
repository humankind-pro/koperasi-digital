<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class MasterAdmin extends Authenticatable
{
    use Notifiable;

    protected $table = 'master_admin';

    protected $fillable = [
        'username',
        'email',
        'password',
        'nama',
        'role'
    ];

}
