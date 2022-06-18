<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class T_Produk extends Model
{
    use HasFactory;

    protected $table = "t_produk";
    protected $fillable = [
        "id_transaksi",
        "id_produk",
        "harga",
        "terjual",
        "total"
    ];

    public function produk()
    {
        return $this->hasOne(Produk::class, 'id', 'id_produk');
    }

    public function transaksi()
    {
        return $this->hasOne(TransaksiV2::class, 'id', 'id_transaksi');
    }
}
