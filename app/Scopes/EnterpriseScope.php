<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Auth;

class EnterpriseScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        $user = Auth::user();
        $table = $model->getTable();
        $builder->where("$table.enterprise_id", $user->enterprise_id);
    }
}
