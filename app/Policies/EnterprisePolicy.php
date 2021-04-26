<?php

namespace App\Policies;

use App\Models\Enterprise;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EnterprisePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isNotEmployee;
    }

    public function view(User $user, Enterprise $enterprise)
    {
        return $user->isNotEmployee;
    }

    public function create(User $user)
    {
        return $user->isNotEmployee;
    }

    public function update(User $user, Enterprise $enterprise)
    {
        return $this->isAllowModify($user, $enterprise);
    }

    public function delete(User $user, Enterprise $enterprise)
    {
        return $this->isAllowModify($user, $enterprise);
    }

    public function restore(User $user, Enterprise $enterprise)
    {
        return $this->isAllowModify($user, $enterprise);
    }

    public function forceDelete(User $user, Enterprise $enterprise)
    {
        return $this->isAllowModify($user, $enterprise);
    }

    private function isAllowModify(User $user, Enterprise $enterprise)
    {
        return $user->isNotEmployee;
    }
}
