<?php

namespace App\Policies;

use App\Models\TransactionOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionOrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isNotEmployee
            || $user->isOnlyEnterpriseEmployee
            || $user->isStoreEmployee;
    }

    public function view(User $user, TransactionOrder $transactionOrder)
    {
        return $user->isNotEmployee || $this->isEnterprise($user, $transactionOrder);
    }

    public function create(User $user)
    {
        return $user->isStoreEmployee;
    }

    private function isEnterprise(User $user, TransactionOrder $transactionOrder)
    {
        return $user->enterprise_id == $transactionOrder->enterprise_id;
    }
}
