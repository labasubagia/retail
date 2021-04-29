<?php

namespace App\Models;

use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'enterprise_id',
        'name',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new EnterpriseScope);
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }
}
