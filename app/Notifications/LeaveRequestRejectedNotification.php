<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LeaveRequestRejectedNotification extends Notification implements ShouldQueue
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
            'title' => 'Pengajuan Izin Ditolak',
            'message' => "Pengajuan {$this->leaveRequest->type->label()} Anda ditolak: {$this->leaveRequest->approval_note}",
            'leave_request_id' => $this->leaveRequest->id,
            'action_url' => route('pegawai.leave-requests.show', $this->leaveRequest),
        ];
    }
}
