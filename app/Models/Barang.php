<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
	protected $primaryKey = 'kode';
    protected $keyType = 'string';
    protected $fillable = ['kode', 'nama', 'kategori', 'harga', 'qty', 'gambar', 'created_at', 'updated_at'];

    public $incrementing = false;
}
