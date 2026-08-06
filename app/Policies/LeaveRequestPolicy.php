<?php

namespace App\Policies;

use App\Models\LeaveRequest;
use App\Models\User;

class LeaveRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view all leave requests');
    }

    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        if ($user->hasPermissionTo('view all leave requests')) {
            return true;
        }

        return $user->hasPermissionTo('view own leave requests') &&
               $user->employee_id === $leaveRequest->employee_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create leave requests') && $user->employee_id !== null;
    }

    public function cancel(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->hasPermissionTo('cancel own leave requests') &&
               $user->employee_id === $leaveRequest->employee_id &&
               $leaveRequest->status->value === 'pending';
    }

    public function approve(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->hasPermissionTo('approve leave requests') &&
               $user->employee_id !== $leaveRequest->employee_id &&
               $leaveRequest->status->value === 'pending';
    }

    public function reject(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->hasPermissionTo('reject leave requests') &&
               $user->employee_id !== $leaveRequest->employee_id &&
               $leaveRequest->status->value === 'pending';
    }

    public function viewAttachment(User $user, LeaveRequest $leaveRequest): bool
    {
        if ($user->hasPermissionTo('view leave request attachments')) {
            return true;
        }

        return $user->hasPermissionTo('view own leave requests') &&
               $user->employee_id === $leaveRequest->employee_id;
    }
}
