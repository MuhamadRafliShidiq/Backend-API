<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_aset',
        'kode_aset',
        'kategori',
        'lokasi',
        'vendor',
        'purchase_date',
        'keterangan',
    ];

    public function histories()
    {
        return $this->hasMany(AssetHistory::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

}
