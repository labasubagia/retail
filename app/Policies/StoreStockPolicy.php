<?php

namespace App\Policies;

use App\Models\StoreStock;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StoreStockPolicy
{
    use HandlesAuthorization;


    public function create(User $user)
    {
        return $user->isStoreEmployee;
    }

    public function update(User $user, StoreStock $storeStock)
    {
        return $this->isAllowModify($user, $storeStock);
    }

    private function isAllowModify(User $user, StoreStock $storeStock)
    {
        return $this->isStore($user, $storeStock)
            && $user->isStoreEmployee;
    }

    private function isStore(User $user, StoreStock $storeStock)
    {
        $isEnterprise = $user->enterprise_id == $storeStock->enterprise_id;
        $isStore = $user->store_id == $storeStock->store_id;
        return $isEnterprise && $isStore;
    }
}
