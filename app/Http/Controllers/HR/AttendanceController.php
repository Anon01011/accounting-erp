<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['employee']);

        // Filter by date range
        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('date', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay(),
            ]);
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $attendances = $query->latest()->paginate(10);
        $employees = Employee::where('is_active', true)->get();

        return view('hr.attendance.index', compact('attendances', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
        return view('hr.attendance.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'required|date_format:H:i',
            'check_out' => 'required|date_format:H:i|after:check_in',
            'status' => 'required|in:present,absent,late,early_leave,half_day',
            'notes' => 'nullable|string',
        ]);

        // Check if attendance record already exists for this employee and date
        $existingAttendance = Attendance::where('employee_id', $validated['employee_id'])
            ->whereDate('date', $validated['date'])
            ->first();

        if ($existingAttendance) {
            return redirect()->back()
                ->with('error', 'Attendance record already exists for this employee on the selected date.')
                ->withInput();
        }

        $attendance = Attendance::create($validated);

        return redirect()->route('hr.attendance.index')
            ->with('success', 'Attendance record created successfully.');
    }

    public function show(Attendance $attendance)
    {
        $attendance->load('employee');
        return view('hr.attendance.show', compact('attendance'));
    }

    public function edit(Attendance $attendance)
    {
        $employees = Employee::where('is_active', true)->get();
        return view('hr.attendance.edit', compact('attendance', 'employees'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'required|date_format:H:i',
            'check_out' => 'required|date_format:H:i|after:check_in',
            'status' => 'required|in:present,absent,late,early_leave,half_day',
            'notes' => 'nullable|string',
        ]);

        // Check if attendance record already exists for this employee and date
        $existingAttendance = Attendance::where('employee_id', $validated['employee_id'])
            ->whereDate('date', $validated['date'])
            ->where('id', '!=', $attendance->id)
            ->first();

        if ($existingAttendance) {
            return redirect()->back()
                ->with('error', 'Attendance record already exists for this employee on the selected date.')
                ->withInput();
        }

        $attendance->update($validated);

        return redirect()->route('hr.attendance.index')
            ->with('success', 'Attendance record updated successfully.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return redirect()->route('hr.attendance.index')
            ->with('success', 'Attendance record deleted successfully.');
    }

    public function bulkCreate()
    {
        $employees = Employee::where('is_active', true)->get();
        return view('hr.attendance.bulk-create', compact('employees'));
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.employee_id' => 'required|exists:employees,id',
            'attendances.*.check_in' => 'required|date_format:H:i',
            'attendances.*.check_out' => 'required|date_format:H:i|after:attendances.*.check_in',
            'attendances.*.status' => 'required|in:present,absent,late,early_leave,half_day',
            'attendances.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            foreach ($validated['attendances'] as $attendanceData) {
                // Check if attendance record already exists
                $existingAttendance = Attendance::where('employee_id', $attendanceData['employee_id'])
                    ->whereDate('date', $validated['date'])
                    ->first();

                if (!$existingAttendance) {
                    Attendance::create([
                        'employee_id' => $attendanceData['employee_id'],
                        'date' => $validated['date'],
                        'check_in' => $attendanceData['check_in'],
                        'check_out' => $attendanceData['check_out'],
                        'status' => $attendanceData['status'],
                        'notes' => $attendanceData['notes'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('hr.attendance.index')
                ->with('success', 'Bulk attendance records created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create bulk attendance records. Please try again.')
                ->withInput();
        }
    }

    public function report(Request $request)
    {
        $query = Attendance::with(['employee']);

        // Filter by date range
        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('date', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay(),
            ]);
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->get();
        $employees = Employee::where('is_active', true)->get();

        // Calculate statistics
        $stats = [
            'total_days' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'early_leave' => $attendances->where('status', 'early_leave')->count(),
            'half_day' => $attendances->where('status', 'half_day')->count(),
        ];

        return view('hr.attendance.report', compact('attendances', 'employees', 'stats'));
    }

    public function export(Request $request)
    {
        $query = Attendance::with(['employee']);

        // Apply filters
        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('date', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay(),
            ]);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->get();

        // Generate CSV
        $filename = 'attendance_report_' . Carbon::now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($attendances) {
            $file = fopen('php://output', 'w');

            // Write header
            fputcsv($file, ['Employee', 'Date', 'Check In', 'Check Out', 'Status', 'Notes']);

            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    $attendance->employee->name,
                    $attendance->date,
                    $attendance->check_in,
                    $attendance->check_out,
                    $attendance->status,
                    $attendance->notes,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 