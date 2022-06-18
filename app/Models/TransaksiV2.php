<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiV2 extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    protected $fillable = [
        "id",
        "tanggal",
        "terjual",
        "total",
    ];

    public $incrementing = false;

    public function produk()
    {
        return $this->hasMany(T_Produk::class, 'id_transaksi', 'id');
    }
}
