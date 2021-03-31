<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item_Penjualan extends Model
{
    use HasFactory;

    protected $table = 'item_penjualan';
	// protected $primaryKey = 'id_nota';
    protected $keyType = 'string';
    protected $fillable = ['nota', 'kode_barang', 'qty', 'total'];

    public $incrementing = false;
    public $timestamps = false;
}
