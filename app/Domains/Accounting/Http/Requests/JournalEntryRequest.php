<?php

namespace App\Domains\Accounting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Domains\Accounting\Services\JournalEntryValidationService;

class JournalEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reference_no' => ['required', 'string', 'max:50', 'unique:journal_entries,reference_no,' . $this->journal_entry?->id],
            'entry_date' => ['required', 'date', 'before_or_equal:today'],
            'description' => ['required', 'string', 'max:1000'],
            'status' => ['required', 'in:draft,posted'],
            'items' => ['required', 'array', 'min:2'],
            'items.*.chart_of_account_id' => ['required', 'exists:chart_of_accounts,id'],
            'items.*.debit' => ['required_without:items.*.credit', 'numeric', 'min:0', 'max:999999999.99'],
            'items.*.credit' => ['required_without:items.*.debit', 'numeric', 'min:0', 'max:999999999.99'],
            'items.*.description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $journalEntry = $this->journal_entry ?? new \App\Domains\Accounting\Models\JournalEntry();
            $journalEntry->fill($this->validated());
            
            // Validate items
            if ($this->has('items')) {
                $journalEntry->items = collect($this->items)->map(function ($item) {
                    return new \App\Domains\Accounting\Models\JournalEntryItem($item);
                });
            }

            // Run custom validation
            $validationService = app(JournalEntryValidationService::class);
            $errors = $validationService->validate($journalEntry);

            foreach ($errors as $error) {
                $validator->errors()->add('items', $error);
            }

            // Validate debit equals credit
            $totalDebit = collect($this->items)->sum('debit');
            $totalCredit = collect($this->items)->sum('credit');

            if (abs($totalDebit - $totalCredit) > 0.01) {
                $validator->errors()->add('items', 'Total debits must equal total credits.');
            }
        });
    }
} 