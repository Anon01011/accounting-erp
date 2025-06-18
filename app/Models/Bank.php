<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'branch',
        'address',
        'contact_person',
        'phone',
        'email',
        'status',
        'notes'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];
}
