<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetHistory extends Model
{
    protected $fillable = ['asset_id', 'user_id', 'action', 'keterangan'];

    public function user()
{
    return $this->belongsTo(User::class);
}

public function asset()
{
    return $this->belongsTo(Asset::class);
}
}
