<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';
	protected $primaryKey = 'id_pelanggan';
    protected $keyType = 'string';
    protected $fillable = ['id_pelanggan', 'nama', 'email', 'password', 'photo', 'domisili', 'jenis_kelamin'];

    public $incrementing = false;
    public $timestamps = false;
}
