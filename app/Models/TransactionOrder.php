<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'enterprise_id',
        'store_id',
        'user_id',
        'total',
    ];

    protected $casts = [
        'total' => 'integer',
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionOrderItem::class);
    }

    public function scopeOfEnterprise($query, User $user)
    {
        return $query->where('enterprise_id', $user->enterprise_id);
    }

    public function scopeOfStore($query, User $user)
    {
        return $query
            ->where('enterprise_id', $user->enterprise_id)
            ->where('store_id', $user->store_id);
    }
}
