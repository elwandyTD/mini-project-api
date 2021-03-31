<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';
	protected $primaryKey = 'id_nota';
    protected $keyType = 'string';
    protected $fillable = ['id_nota', 'kode_pelanggan', 'subtotal'];

    public $incrementing = false;
    public $timestamps = false;
}
