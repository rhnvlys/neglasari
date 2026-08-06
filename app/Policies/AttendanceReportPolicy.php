<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttendanceReportPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view attendance reports') || 
               $user->hasPermissionTo('view executive attendance reports');
    }

    public function viewOwn(User $user): bool
    {
        return $user->hasPermissionTo('view own attendance report');
    }

    public function exportExcel(User $user): bool
    {
        return $user->hasPermissionTo('export attendance reports excel') || 
               $user->hasPermissionTo('export own attendance report');
    }

    public function exportPdf(User $user): bool
    {
        return $user->hasPermissionTo('export attendance reports pdf') || 
               $user->hasPermissionTo('export own attendance report');
    }

    public function print(User $user): bool
    {
        return $user->hasPermissionTo('print attendance reports');
    }
}
