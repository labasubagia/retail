<?php

namespace App\Policies;

use App\Models\ProductType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isNotEmployee
            || $user->isOnlyEnterpriseEmployee
            || $user->isStoreEmployee;
    }

    public function view(User $user, ProductType $productType)
    {
        return $user->isNotEmployee || $this->isEnterprise($user, $productType);
    }

    public function create(User $user)
    {
        return $user->isOnlyEnterpriseEmployee;
    }

    public function update(User $user, ProductType $productType)
    {
        return $this->isAllowed($user, $productType);
    }

    public function delete(User $user, ProductType $productType)
    {
        return $this->isAllowed($user, $productType);
    }

    public function restore(User $user, ProductType $productType)
    {
        return $this->isAllowed($user, $productType);
    }

    public function forceDelete(User $user, ProductType $productType)
    {
        return $this->isAllowed($user, $productType);
    }

    private function isAllowed(User $user, ProductType $productType)
    {
        return $user->isOnlyEnterpriseEmployee
            && $this->isEnterprise($user, $productType);
    }

    private function isEnterprise(User $user, ProductType $productType)
    {
        return $user->enterprise_id == $productType->enterprise_id;
    }
}
