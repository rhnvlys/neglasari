<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LeaveRequestSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public LeaveRequest $leaveRequest) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pengajuan Izin Baru',
            'message' => "Pengajuan {$this->leaveRequest->type->label()} dari {$this->leaveRequest->employee->full_name} menunggu persetujuan.",
            'leave_request_id' => $this->leaveRequest->id,
            'action_url' => route('admin.leave-requests.show', $this->leaveRequest),
        ];
    }
}
