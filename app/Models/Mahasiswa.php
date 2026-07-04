<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    // Override the default primary key name
    protected $primaryKey = 'nim';
    
    // Tell Laravel the primary key is a string (text), not an integer
    protected $keyType = 'string';
    
    // Disable auto-incrementing since NIM is a custom string
    public $incrementing = false;

    protected $fillable = ['nim', 'nama_lengkap'];
}