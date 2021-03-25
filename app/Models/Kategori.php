<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
	use HasFactory;

	protected $table = 'kategori';
	protected $primaryKey = 'kode_kategori';
    protected $keyType = 'string';
    protected $fillable = ['kode_kategori', 'title'];

    public $incrementing = false;
    public $timestamps = false;
}
