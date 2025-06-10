@extends('layouts.app')

@section('title', isset($journalEntry) ? 'Edit Journal Entry' : 'Create Journal Entry')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ isset($journalEntry) ? 'Edit Journal Entry' : 'Create Journal Entry' }}</h3>
                </div>
                <form action="{{ isset($journalEntry) ? route('journal-entries.update', $journalEntry) : route('journal-entries.store') }}" 
                      method="POST" 
                      class="card-body">
                    @csrf
                    @if(isset($journalEntry))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entry_date">Entry Date</label>
                                <input type="date" 
                                       name="entry_date" 
                                       id="entry_date" 
                                       class="form-control @error('entry_date') is-invalid @enderror" 
                                       value="{{ old('entry_date', isset($journalEntry) ? $journalEntry->entry_date->format('Y-m-d') : date('Y-m-d')) }}" 
                                       required>
                                @error('entry_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reference_no">Reference No</label>
                                <input type="text" 
                                       name="reference_no" 
                                       id="reference_no" 
                                       class="form-control @error('reference_no') is-invalid @enderror" 
                                       value="{{ old('reference_no', $journalEntry->reference_no ?? '') }}" 
                                       required>
                                @error('reference_no')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" 
                                  id="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="3">{{ old('description', $journalEntry->description ?? '') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Transactions</h4>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" id="add-transaction">
                                    <i class="fas fa-plus"></i> Add Transaction
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="transactions-table">
                                    <thead>
                                        <tr>
                                            <th>Account</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Description</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($journalEntry))
                                            @foreach($journalEntry->items as $item)
                                                <tr>
                                                    <td>
                                                        <select name="items[{{ $loop->index }}][chart_of_account_id]" 
                                                                class="form-control account-select" 
                                                                required>
                                                            <option value="">Select Account</option>
                                                            @foreach($accounts as $account)
                                                                <option value="{{ $account->id }}" 
                                                                        {{ $item->chart_of_account_id == $account->id ? 'selected' : '' }}>
                                                                    {{ $account->account_code }} - {{ $account->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" 
                                                               name="items[{{ $loop->index }}][debit]" 
                                                               class="form-control debit-amount" 
                                                               value="{{ $item->debit }}"
                                                               step="0.01" 
                                                               min="0">
                                                    </td>
                                                    <td>
                                                        <input type="number" 
                                                               name="items[{{ $loop->index }}][credit]" 
                                                               class="form-control credit-amount" 
                                                               value="{{ $item->credit }}"
                                                               step="0.01" 
                                                               min="0">
                                                    </td>
                                                    <td>
                                                        <input type="text" 
                                                               name="items[{{ $loop->index }}][description]" 
                                                               class="form-control" 
                                                               value="{{ $item->description }}">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger remove-row">Remove</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" class="text-right"><strong>Total:</strong></td>
                                            <td colspan="3">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>Debit:</strong> 
                                                        <span id="debit-total">0.00</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Credit:</strong> 
                                                        <span id="credit-total">0.00</span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($journalEntry) ? 'Update Journal Entry' : 'Create Journal Entry' }}
                        </button>
                        <a href="{{ route('journal-entries.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        let transactionCount = {{ isset($journalEntry) ? count($journalEntry->items) : 0 }};

        // Add new transaction row
        $('#add-transaction').click(function() {
            const row = `
                <tr>
                    <td>
                        <select name="items[${transactionCount}][chart_of_account_id]" 
                                class="form-control account-select" 
                                required>
                            <option value="">Select Account</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">
                                    {{ $account->account_code }} - {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" 
                               name="items[${transactionCount}][debit]" 
                               class="form-control debit-amount" 
                               step="0.01" 
                               min="0">
                    </td>
                    <td>
                        <input type="number" 
                               name="items[${transactionCount}][credit]" 
                               class="form-control credit-amount" 
                               step="0.01" 
                               min="0">
                    </td>
                    <td>
                        <input type="text" 
                               name="items[${transactionCount}][description]" 
                               class="form-control">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger remove-row">Remove</button>
                    </td>
                </tr>
            `;
            $('#transactions-table tbody').append(row);
            transactionCount++;
            updateTotals();
        });

        // Remove transaction row
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
            updateTotals();
        });

        // Update totals when amount changes
        $(document).on('change', '.debit-amount, .credit-amount', updateTotals);

        function updateTotals() {
            let debitTotal = 0;
            let creditTotal = 0;

            $('#transactions-table tbody tr').each(function() {
                const debit = parseFloat($(this).find('.debit-amount').val()) || 0;
                const credit = parseFloat($(this).find('.credit-amount').val()) || 0;

                debitTotal += debit;
                creditTotal += credit;
            });

            $('#debit-total').text(debitTotal.toFixed(2));
            $('#credit-total').text(creditTotal.toFixed(2));

            // Highlight if totals don't match
            if (debitTotal !== creditTotal) {
                $('#debit-total, #credit-total').addClass('text-danger');
            } else {
                $('#debit-total, #credit-total').removeClass('text-danger');
            }
        }

        // Initialize Select2 for account selection
        $('.account-select').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    });
</script>
@endpush
@endsection