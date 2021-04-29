<?php

namespace App\Scopes;

use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class StoreScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $user = Auth::user();
        $table = $model->getTable();
        $builder
            ->where("$table.enterprise_id", $user->enterprise_id)
            ->where("$table.store_id", $user->store_id);
    }
}
