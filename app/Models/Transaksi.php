<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = "transaksi";
    protected $fillable = [
        "tanggal",
        "id_produk",
        "produk",
        "harga",
        "terjual",
        "total_harga"
    ];
}
