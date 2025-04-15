<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model // Ubah dari Maintenances menjadi Maintenance
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'user_id',
        'lokasi',
        'jadwal',
        'keterangan',
        'status'
    ];

    // Relasi ke model Asset
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
    
    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}