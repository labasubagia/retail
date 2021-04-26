<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'enterprise_id',
        'name',
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function scopeOfEnterprise($query, User $user)
    {
        return $query->where('enterprise_id', $user->enterprise_id);
    }
}
