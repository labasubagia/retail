<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;

class VendorPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isNotEmployee
            || $user->isOnlyEnterpriseEmployee
            || $user->isStoreEmployee;
    }

    public function view(User $user, Vendor $vendor)
    {
        return $user->isNotEmployee || $this->isEnterprise($user, $vendor);
    }

    public function create(User $user)
    {
        return $user->isOnlyEnterpriseEmployee;
    }

    public function update(User $user, Vendor $vendor)
    {
        return $this->isAllowed($user, $vendor);
    }

    public function delete(User $user, Vendor $vendor)
    {
        return $this->isAllowed($user, $vendor);
    }

    public function restore(User $user, Vendor $vendor)
    {
        return $this->isAllowed($user, $vendor);
    }

    public function forceDelete(User $user, Vendor $vendor)
    {
        return $this->isAllowed($user, $vendor);
    }

    private function isAllowed(User $user, Vendor $vendor)
    {
        return $user->isOnlyEnterpriseEmployee
            && $this->isEnterprise($user, $vendor);
    }

    private function isEnterprise(User $user, Vendor $vendor)
    {
        return $user->enterprise_id == $vendor->enterprise_id;
    }
}
