<?php

namespace App\Policies;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BrandPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isNotEmployee
            || $user->isOnlyEnterpriseEmployee
            || $user->isStoreEmployee;
    }

    public function view(User $user, Brand $brand)
    {
        return $user->isNotEmployee || $this->isEnterprise($user, $brand);
    }

    public function create(User $user)
    {
        return $user->isOnlyEnterpriseEmployee;
    }

    public function update(User $user, Brand $brand)
    {
        return $this->isAllowed($user, $brand);
    }

    public function delete(User $user, Brand $brand)
    {
        return $this->isAllowed($user, $brand);
    }

    public function restore(User $user, Brand $brand)
    {
        return $this->isAllowed($user, $brand);
    }

    public function forceDelete(User $user, Brand $brand)
    {
        return $this->isAllowed($user, $brand);
    }

    private function isAllowed(User $user, Brand $brand)
    {
        return $user->isOnlyEnterpriseEmployee
            && $this->isEnterprise($user, $brand);
    }

    private function isEnterprise(User $user, Brand $brand)
    {
        return $user->enterprise_id == $brand->enterprise_id;
    }
}
