<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TotalPerbulan extends Model
{
    use HasFactory;

    protected $table = 'total_perbulan';
    protected $fillable = [
        'periode',
        'terjual',
        'pendapatan'
    ];
}
