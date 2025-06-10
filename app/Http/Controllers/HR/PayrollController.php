<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\PayrollItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDF;

class PayrollController extends Controller
{
    public function index()
    {
        $payrolls = Payroll::with(['employee'])
            ->latest()
            ->paginate(10);
        return view('hr.payroll.index', compact('payrolls'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->get();
        return view('hr.payroll.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'payroll_date' => 'required|date',
            'pay_period_start' => 'required|date',
            'pay_period_end' => 'required|date|after:pay_period_start',
            'basic_salary' => 'required|numeric|min:0',
            'items' => 'required|array',
            'items.*.type' => 'required|in:addition,deduction',
            'items.*.description' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:bank_transfer,cash,check',
            'bank_name' => 'required_if:payment_method,bank_transfer',
            'account_number' => 'required_if:payment_method,bank_transfer',
            'check_number' => 'required_if:payment_method,check',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Calculate total additions and deductions
            $totalAdditions = collect($validated['items'])
                ->where('type', 'addition')
                ->sum('amount');
            $totalDeductions = collect($validated['items'])
                ->where('type', 'deduction')
                ->sum('amount');

            // Calculate net salary
            $netSalary = $validated['basic_salary'] + $totalAdditions - $totalDeductions;

            // Create payroll record
            $payroll = Payroll::create([
                'employee_id' => $validated['employee_id'],
                'payroll_date' => $validated['payroll_date'],
                'pay_period_start' => $validated['pay_period_start'],
                'pay_period_end' => $validated['pay_period_end'],
                'basic_salary' => $validated['basic_salary'],
                'total_additions' => $totalAdditions,
                'total_deductions' => $totalDeductions,
                'net_salary' => $netSalary,
                'payment_method' => $validated['payment_method'],
                'bank_name' => $validated['bank_name'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'check_number' => $validated['check_number'] ?? null,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create payroll items
            foreach ($validated['items'] as $item) {
                $payroll->items()->create([
                    'type' => $item['type'],
                    'description' => $item['description'],
                    'amount' => $item['amount'],
                ]);
            }

            DB::commit();

            return redirect()->route('hr.payroll.index')
                ->with('success', 'Payroll created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create payroll. Please try again.')
                ->withInput();
        }
    }

    public function show(Payroll $payroll)
    {
        $payroll->load(['employee', 'items']);
        return view('hr.payroll.show', compact('payroll'));
    }

    public function edit(Payroll $payroll)
    {
        if ($payroll->status !== 'pending') {
            return redirect()->route('hr.payroll.index')
                ->with('error', 'Only pending payrolls can be edited.');
        }

        $employees = Employee::where('is_active', true)->get();
        return view('hr.payroll.edit', compact('payroll', 'employees'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        if ($payroll->status !== 'pending') {
            return redirect()->route('hr.payroll.index')
                ->with('error', 'Only pending payrolls can be updated.');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'payroll_date' => 'required|date',
            'pay_period_start' => 'required|date',
            'pay_period_end' => 'required|date|after:pay_period_start',
            'basic_salary' => 'required|numeric|min:0',
            'items' => 'required|array',
            'items.*.type' => 'required|in:addition,deduction',
            'items.*.description' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:bank_transfer,cash,check',
            'bank_name' => 'required_if:payment_method,bank_transfer',
            'account_number' => 'required_if:payment_method,bank_transfer',
            'check_number' => 'required_if:payment_method,check',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Calculate total additions and deductions
            $totalAdditions = collect($validated['items'])
                ->where('type', 'addition')
                ->sum('amount');
            $totalDeductions = collect($validated['items'])
                ->where('type', 'deduction')
                ->sum('amount');

            // Calculate net salary
            $netSalary = $validated['basic_salary'] + $totalAdditions - $totalDeductions;

            // Update payroll record
            $payroll->update([
                'employee_id' => $validated['employee_id'],
                'payroll_date' => $validated['payroll_date'],
                'pay_period_start' => $validated['pay_period_start'],
                'pay_period_end' => $validated['pay_period_end'],
                'basic_salary' => $validated['basic_salary'],
                'total_additions' => $totalAdditions,
                'total_deductions' => $totalDeductions,
                'net_salary' => $netSalary,
                'payment_method' => $validated['payment_method'],
                'bank_name' => $validated['bank_name'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'check_number' => $validated['check_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Update payroll items
            $payroll->items()->delete();
            foreach ($validated['items'] as $item) {
                $payroll->items()->create([
                    'type' => $item['type'],
                    'description' => $item['description'],
                    'amount' => $item['amount'],
                ]);
            }

            DB::commit();

            return redirect()->route('hr.payroll.index')
                ->with('success', 'Payroll updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update payroll. Please try again.')
                ->withInput();
        }
    }

    public function destroy(Payroll $payroll)
    {
        if ($payroll->status !== 'pending') {
            return redirect()->route('hr.payroll.index')
                ->with('error', 'Only pending payrolls can be deleted.');
        }

        $payroll->items()->delete();
        $payroll->delete();

        return redirect()->route('hr.payroll.index')
            ->with('success', 'Payroll deleted successfully.');
    }

    public function process(Payroll $payroll)
    {
        if ($payroll->status !== 'pending') {
            return redirect()->route('hr.payroll.index')
                ->with('error', 'Only pending payrolls can be processed.');
        }

        $payroll->update(['status' => 'processed']);

        return redirect()->route('hr.payroll.index')
            ->with('success', 'Payroll processed successfully.');
    }

    public function export(Payroll $payroll)
    {
        $payroll->load(['employee', 'items']);

        // Generate PDF
        $pdf = PDF::loadView('hr.payroll.pdf', compact('payroll'));

        return $pdf->download('payroll_' . $payroll->id . '.pdf');
    }
} 