<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isNotEmployee || $user->isOnlyEnterpriseEmployee || $user->isStoreEmployee;
    }

    public function view(User $user, Order $order)
    {
        return $user->isNotEmployee || $this->isEnterprise($user, $order);
    }

    public function create(User $user)
    {
        return $user->isStoreEmployee;
    }

    private function isEnterprise(User $user, Order $order)
    {
        return $user->enterprise_id == $order->enterprise_id;
    }
}
