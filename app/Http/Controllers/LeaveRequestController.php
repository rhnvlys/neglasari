<?php

namespace App\Http\Controllers;

use App\Enums\LeaveRequestStatus;
use App\Enums\LeaveRequestType;
use App\Http\Requests\ProcessLeaveRequestRequest;
use App\Http\Requests\StoreLeaveRequestRequest;
use App\Models\LeaveRequest;
use App\Services\LeaveRequestService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeaveRequestController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly LeaveRequestService $service) {}

    // ---------------------------------------------------------
    // Employee Endpoints
    // ---------------------------------------------------------

    public function myRequests(Request $request)
    {
        $employee = Auth::user()->employee ?? abort(403);
        
        $query = LeaveRequest::where('employee_id', $employee->id);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('year')) {
            $query->whereYear('start_date', $request->year);
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('employee.leave_requests.index', compact('leaveRequests'));
    }

    public function create()
    {
        $this->authorize('create', LeaveRequest::class);
        $types = LeaveRequestType::cases();
        return view('employee.leave_requests.create', compact('types'));
    }

    public function store(StoreLeaveRequestRequest $request)
    {
        $employee = Auth::user()->employee;
        $this->service->create($employee, $request->validated(), $request->file('attachment'));
        return redirect()->route('pegawai.leave-requests.index')->with('success', 'Pengajuan berhasil dibuat dan menunggu persetujuan.');
    }

    public function showMine(LeaveRequest $leaveRequest)
    {
        $this->authorize('view', $leaveRequest);
        return view('employee.leave_requests.show', compact('leaveRequest'));
    }

    public function cancel(LeaveRequest $leaveRequest)
    {
        $this->authorize('cancel', $leaveRequest);
        $this->service->cancel($leaveRequest);
        return back()->with('success', 'Pengajuan berhasil dibatalkan.');
    }

    // ---------------------------------------------------------
    // Admin Endpoints
    // ---------------------------------------------------------

    public function index(Request $request)
    {
        $this->authorize('viewAny', LeaveRequest::class);
        
        $query = LeaveRequest::with(['employee.position', 'approver']);

        if ($request->filled('search')) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('employee_number', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $stats = [
            'pending' => LeaveRequest::where('status', LeaveRequestStatus::PENDING->value)->count(),
            'approved' => LeaveRequest::where('status', LeaveRequestStatus::APPROVED->value)->count(),
            'rejected' => LeaveRequest::where('status', LeaveRequestStatus::REJECTED->value)->count(),
            'sick_this_month' => LeaveRequest::where('type', LeaveRequestType::SICK->value)
                ->where('status', LeaveRequestStatus::APPROVED->value)
                ->whereMonth('start_date', now()->month)->count(),
            'leave_this_month' => LeaveRequest::where('type', LeaveRequestType::LEAVE->value)
                ->where('status', LeaveRequestStatus::APPROVED->value)
                ->whereMonth('start_date', now()->month)->count(),
        ];

        return view('admin.leave_requests.index', compact('leaveRequests', 'stats'));
    }

    public function show(LeaveRequest $leaveRequest)
    {
        $this->authorize('view', $leaveRequest);
        $leaveRequest->load(['employee.position', 'approver']);
        return view('admin.leave_requests.show', compact('leaveRequest'));
    }

    public function approve(ProcessLeaveRequestRequest $request, LeaveRequest $leaveRequest)
    {
        $this->service->approve($leaveRequest, $request->validated('approval_note'));
        return redirect()->route('admin.leave-requests.show', $leaveRequest)->with('success', 'Pengajuan berhasil disetujui.');
    }

    public function reject(ProcessLeaveRequestRequest $request, LeaveRequest $leaveRequest)
    {
        $this->service->reject($leaveRequest, $request->validated('approval_note'));
        return redirect()->route('admin.leave-requests.show', $leaveRequest)->with('success', 'Pengajuan telah ditolak.');
    }

    // ---------------------------------------------------------
    // Shared Endpoints
    // ---------------------------------------------------------

    public function attachment(LeaveRequest $leaveRequest): StreamedResponse
    {
        $this->authorize('viewAttachment', $leaveRequest);
        
        if (!$leaveRequest->attachment_path || !Storage::disk('private')->exists($leaveRequest->attachment_path)) {
            abort(404, 'Lampiran tidak ditemukan.');
        }

        return Storage::disk('private')->download($leaveRequest->attachment_path, 'Lampiran_Pengajuan_' . $leaveRequest->id . '.' . pathinfo($leaveRequest->attachment_path, PATHINFO_EXTENSION));
    }
}
