<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    protected $table = 'asset_categories';
    protected $fillable = [
        'code', 'name', 'description',
    ];
} 