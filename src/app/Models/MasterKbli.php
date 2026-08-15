<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterKbli extends Model
{
    protected $table = 'master_kbli';

    protected $fillable = [
        'kode_kbli',
        'judul',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
