<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isNotEmployee
            || $user->isOnlyEnterpriseEmployee
            || $user->isStoreEmployee;
    }

    public function view(User $user, Product $product)
    {
        return $user->isNotEmployee || $this->isEnterprise($user, $product);
    }

    public function create(User $user)
    {
        return $user->isOnlyEnterpriseEmployee;
    }

    public function update(User $user, Product $product)
    {
        return $this->isAllowed($user, $product);
    }

    public function delete(User $user, Product $product)
    {
        return $this->isAllowed($user, $product);
    }

    public function restore(User $user, Product $product)
    {
        return $this->isAllowed($user, $product);
    }

    public function forceDelete(User $user, Product $product)
    {
        return $this->isAllowed($user, $product);
    }

    private function isAllowed(User $user, Product $product)
    {
        return $user->isOnlyEnterpriseEmployee
            && $this->isEnterprise($user, $product);
    }

    private function isEnterprise(User $user, Product $product)
    {
        return $user->enterprise_id == $product->enterprise_id;
    }
}
