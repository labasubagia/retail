<?php

namespace App\Policies;

use App\Models\Store;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StorePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isNotEmployee || $user->isOnlyEnterpriseEmployee;
    }

    public function view(User $user, Store $store)
    {
        return $user->isNotEmployee || $this->isAllowed($user, $store);
    }

    public function create(User $user)
    {
        return $user->isOnlyEnterpriseEmployee;
    }

    public function update(User $user, Store $store)
    {
        return $this->isAllowed($user, $store);
    }

    public function delete(User $user, Store $store)
    {
        return $this->isAllowed($user, $store);
    }

    public function restore(User $user, Store $store)
    {
        return $this->isAllowed($user, $store);
    }

    public function forceDelete(User $user, Store $store)
    {
        return $this->isAllowed($user, $store);
    }

    private function isAllowed(User $user, Store $store)
    {
        return $user->isOnlyEnterpriseEmployee
            && $this->isEnterprise($user, $store);
    }

    private function isEnterprise(User $user, Store $store)
    {
        return $user->enterprise_id == $store->enterprise_id;
    }
}
