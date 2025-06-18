<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use PDF;

class QuotationController extends Controller
{
    public function index()
    {
        // Debug: Log the count of quotations fetched
        $quotations = Quotation::with('customer')
            ->latest()
            ->paginate(10);

        Log::info('Quotations fetched count: ' . $quotations->count());

        return view('sales.quotations.index', compact('quotations'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        
        return view('sales.quotations.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        Log::info('Quotation store method called with data: ', $request->all());

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'reference_number' => 'required|string|unique:quotations,reference_number',
            'quotation_date' => 'required|date',
            'valid_until' => 'required|date|after:quotation_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0',
            'items.*.description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $quotation = Quotation::create([
                'customer_id' => $validated['customer_id'],
                'reference_number' => $validated['reference_number'],
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $validated['valid_until'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'draft',
                'total_amount' => 0,
            ]);

            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $discountAmount = ($subtotal * ($item['discount'] ?? 0)) / 100;
                $taxAmount = (($subtotal - $discountAmount) * $item['tax_rate']) / 100;
                $total = $subtotal - $discountAmount + $taxAmount;

                $quotation->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0,
                    'tax_rate' => $item['tax_rate'],
                    'total' => $total,
                    'description' => $item['description'] ?? null,
                ]);

                $totalAmount += $total;
            }

            $quotation->update(['total_amount' => $totalAmount]);

            DB::commit();

            Log::info('Quotation created with ID: ' . $quotation->id);

            return redirect()->route('sales.quotations.index')
                ->with('success', 'Quotation created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating quotation: ' . $e->getMessage());
            return redirect()->back()->withErrors('Failed to create quotation. Please try again.');
        }
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['customer', 'items.product']);
        return view('sales.quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        
        return view('sales.quotations.edit', compact('quotation', 'customers', 'products'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'reference_number' => 'required|string|unique:quotations,reference_number,' . $quotation->id,
            'quotation_date' => 'required|date',
            'valid_until' => 'required|date|after:quotation_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0',
            'items.*.description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $quotation->update([
                'customer_id' => $validated['customer_id'],
                'reference_number' => $validated['reference_number'],
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $validated['valid_until'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Delete existing items
            $quotation->items()->delete();

            // Create new items
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $discountAmount = ($subtotal * ($item['discount'] ?? 0)) / 100;
                $taxAmount = (($subtotal - $discountAmount) * $item['tax_rate']) / 100;
                $total = $subtotal - $discountAmount + $taxAmount;

                $quotation->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0,
                    'tax_rate' => $item['tax_rate'],
                    'total' => $total,
                    'description' => $item['description'] ?? null,
                ]);

                $totalAmount += $total;
            }

            $quotation->update(['total_amount' => $totalAmount]);

            DB::commit();

            Log::info('Quotation updated with ID: ' . $quotation->id);

            return redirect()->route('sales.quotations.index')
                ->with('success', 'Quotation updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating quotation: ' . $e->getMessage());
            return redirect()->back()->withErrors('Failed to update quotation. Please try again.');
        }
    }

    public function destroy(Quotation $quotation)
    {
        try {
            DB::beginTransaction();

            $quotation->delete();

            DB::commit();

            Log::info('Quotation deleted with ID: ' . $quotation->id);

            return redirect()->route('sales.quotations.index')
                ->with('success', 'Quotation deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting quotation: ' . $e->getMessage());
            return redirect()->back()->withErrors('Failed to delete quotation. Please try again.');
        }
    }

    public function convert($id)
    {
        // TODO: Implement quotation to order conversion logic
    }

    public function pdf(Quotation $quotation)
    {
        $pdf = PDF::loadView('sales.quotations.pdf', compact('quotation'));
        return $pdf->stream('quotation-' . $quotation->reference_number . '.pdf');
    }

    public function sendEmail(Request $request)
    {
        $request->validate([
            'quotation_id' => 'required|exists:quotations,id',
            'email' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        $quotation = Quotation::with('customer')->findOrFail($request->quotation_id);
        
        // Generate PDF
        $pdf = PDF::loadView('sales.quotations.pdf', compact('quotation'));
        
        // Send email
        Mail::send('emails.quotation', ['quotation' => $quotation, 'message' => $request->message], function($message) use ($request, $quotation, $pdf) {
            $message->to($request->email)
                   ->subject($request->subject)
                   ->attachData($pdf->output(), "quotation-{$quotation->reference_number}.pdf");
        });

        return response()->json(['success' => true]);
    }

    public function template()
    {
        // Create a sample quotation for template preview
        $quotation = new \stdClass();
        $quotation->reference_number = 'QT-TEMPLATE';
        $quotation->customer = (object)[
            'name' => 'Sample Customer',
            'address' => '123 Business Street, City, State, ZIP',
            'phone' => '(123) 456-7890',
            'email' => 'sample@customer.com'
        ];
        $quotation->quotation_date = now();
        $quotation->valid_until = now()->addDays(30);
        $quotation->items = collect([
            (object)[
                'product' => (object)['name' => 'Sample Product 1'],
                'description' => 'Sample description for product 1',
                'quantity' => 2,
                'unit_price' => 100.00,
                'tax_amount' => 20.00,
                'total_amount' => 220.00
            ],
            (object)[
                'product' => (object)['name' => 'Sample Product 2'],
                'description' => 'Sample description for product 2',
                'quantity' => 1,
                'unit_price' => 150.00,
                'tax_amount' => 30.00,
                'total_amount' => 180.00
            ]
        ]);
        $quotation->subtotal = 350.00;
        $quotation->tax_amount = 50.00;
        $quotation->total_amount = 400.00;
        $quotation->notes = 'This is a sample quotation template. Replace this text with your actual notes.';

        $pdf = PDF::loadView('sales.quotations.pdf', compact('quotation'));
        return $pdf->stream('quotation-template.pdf');
    }
}
