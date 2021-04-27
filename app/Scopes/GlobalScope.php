<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Auth;

class GlobalScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        $user = Auth::user();
        $builder->when($user->isNotEmployee, fn($q) => $q);
    }
}
