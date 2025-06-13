@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title text-gray-800">Asset Details</h3>
                        <div class="card-tools">
                            <a href="{{ route('assets.edit', $asset) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#disposeModal">
                                <i class="fas fa-trash"></i> Dispose
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Basic Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="text-muted">Account Code</label>
                                        <p class="form-control-static">{{ $asset->account_code }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="text-muted">Name</label>
                                        <p class="form-control-static">{{ $asset->name }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="text-muted">Description</label>
                                        <p class="form-control-static">{{ $asset->description }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="text-muted">Status</label>
                                        <p class="form-control-static">
                                            <span class="badge badge-{{ $asset->is_active ? 'success' : 'danger' }}">
                                                {{ $asset->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Asset Details -->
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Asset Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="text-muted">Serial Number</label>
                                        <p class="form-control-static">{{ $asset->details->first()?->serial_number ?? 'N/A' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="text-muted">Purchase Date</label>
                                        <p class="form-control-static">{{ $asset->details->first()?->purchase_date?->format('Y-m-d') ?? 'N/A' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="text-muted">Purchase Price</label>
                                        <p class="form-control-static">{{ $asset->details->first() ? number_format($asset->details->first()->purchase_price, 2) : 'N/A' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="text-muted">Warranty Expiry</label>
                                        <p class="form-control-static">{{ $asset->details->first()?->warranty_expiry?->format('Y-m-d') ?? 'N/A' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="text-muted">Depreciation Method</label>
                                        <p class="form-control-static">{{ $asset->details->first() ? ucfirst(str_replace('_', ' ', $asset->details->first()->depreciation_method)) : 'N/A' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="text-muted">Depreciation Rate</label>
                                        <p class="form-control-static">{{ $asset->details->first() ? $asset->details->first()->depreciation_rate . '%' : 'N/A' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="text-muted">Useful Life</label>
                                        <p class="form-control-static">{{ $asset->details->first() ? $asset->details->first()->useful_life . ' years' : 'N/A' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="text-muted">Location</label>
                                        <p class="form-control-static">{{ $asset->details->first()?->location ?? 'N/A' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="text-muted">Condition</label>
                                        <p class="form-control-static">{{ $asset->details->first() ? ucfirst($asset->details->first()->condition) : 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Information -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Financial Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="text-muted">Current Book Value</label>
                                                <p class="form-control-static h4 text-primary">{{ number_format($asset->getCurrentBookValue(), 2) }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="text-muted">Accumulated Depreciation</label>
                                                <p class="form-control-static h4 text-danger">{{ number_format($asset->getAccumulatedDepreciation(), 2) }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="text-muted">Next Depreciation Date</label>
                                                <p class="form-control-static h4 text-info">{{ $asset->getNextDepreciationDate()->format('Y-m-d') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <ul class="nav nav-tabs card-header-tabs" id="assetTabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="transactions-tab" data-toggle="tab" href="#transactions" role="tab">
                                                <i class="fas fa-exchange-alt"></i> Transactions
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="maintenance-tab" data-toggle="tab" href="#maintenance" role="tab">
                                                <i class="fas fa-tools"></i> Maintenance Records
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="documents-tab" data-toggle="tab" href="#documents" role="tab">
                                                <i class="fas fa-file-alt"></i> Documents
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="assetTabsContent">
                                        <!-- Transactions Tab -->
                                        <div class="tab-pane fade show active" id="transactions" role="tabpanel">
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Type</th>
                                                            <th>Amount</th>
                                                            <th>Description</th>
                                                            <th>Reference</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($asset->transactions as $transaction)
                                                        <tr>
                                                            <td>{{ $transaction->date?->format('Y-m-d') ?? 'N/A' }}</td>
                                                            <td>
                                                                <span class="badge badge-{{ $transaction->type === 'depreciation' ? 'warning' : 'info' }}">
                                                                    {{ $transaction->type ? ucfirst(str_replace('_', ' ', $transaction->type)) : 'N/A' }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $transaction->amount ? number_format($transaction->amount, 2) : '0.00' }}</td>
                                                            <td>{{ $transaction->description ?? 'N/A' }}</td>
                                                            <td>
                                                                @if($transaction->reference_type && $transaction->reference_id)
                                                                    <a href="{{ route($transaction->reference_type . '.show', $transaction->reference_id) }}" class="btn btn-sm btn-link">
                                                                        View Reference
                                                                    </a>
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center py-4 text-muted">
                                                                <i class="fas fa-info-circle"></i> No transactions found for this asset.
                                                            </td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Maintenance Tab -->
                                        <div class="tab-pane fade" id="maintenance" role="tabpanel">
                                            <div class="mb-3">
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#maintenanceModal">
                                                    <i class="fas fa-plus"></i> Add Maintenance Record
                                                </button>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Type</th>
                                                            <th>Description</th>
                                                            <th>Cost</th>
                                                            <th>Performed By</th>
                                                            <th>Next Maintenance</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($asset->maintenanceRecords as $maintenance)
                                                        <tr>
                                                            <td>{{ $maintenance->maintenance_date?->format('Y-m-d') ?? 'N/A' }}</td>
                                                            <td>
                                                                <span class="badge badge-{{ $maintenance->maintenance_type === 'preventive' ? 'success' : 'warning' }}">
                                                                    {{ $maintenance->maintenance_type ? ucfirst(str_replace('_', ' ', $maintenance->maintenance_type)) : 'N/A' }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $maintenance->description ?? 'N/A' }}</td>
                                                            <td>{{ $maintenance->cost ? number_format($maintenance->cost, 2) : '0.00' }}</td>
                                                            <td>{{ $maintenance->performed_by ?? 'N/A' }}</td>
                                                            <td>{{ $maintenance->next_maintenance_date?->format('Y-m-d') ?? 'N/A' }}</td>
                                                            <td>
                                                                <span class="badge badge-{{ $maintenance->isOverdue() ? 'danger' : 'success' }}">
                                                                    {{ $maintenance->isOverdue() ? 'Overdue' : 'Scheduled' }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="7" class="text-center py-4 text-muted">
                                                                <i class="fas fa-info-circle"></i> No maintenance records found for this asset.
                                                            </td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Documents Tab -->
                                        <div class="tab-pane fade" id="documents" role="tabpanel">
                                            <div class="mb-3">
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#documentModal">
                                                    <i class="fas fa-plus"></i> Add Document
                                                </button>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Type</th>
                                                            <th>Upload Date</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($asset->documents as $document)
                                                        <tr>
                                                            <td>{{ $document->name }}</td>
                                                            <td>
                                                                <span class="badge badge-info">
                                                                    {{ $document->type }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $document->created_at->format('Y-m-d') }}</td>
                                                            <td>
                                                                <a href="{{ route('documents.download', $document) }}" class="btn btn-sm btn-info">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteDocument({{ $document->id }})">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center py-4 text-muted">
                                                                <i class="fas fa-info-circle"></i> No documents found for this asset.
                                                            </td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Maintenance Modal -->
<div class="modal fade" id="maintenanceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('assets.maintenance', $asset) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Maintenance Record</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Maintenance Type</label>
                        <select name="maintenance_type" class="form-control" required>
                            <option value="preventive">Preventive</option>
                            <option value="corrective">Corrective</option>
                            <option value="predictive">Predictive</option>
                            <option value="condition_based">Condition Based</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Maintenance Date</label>
                        <input type="date" name="maintenance_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Cost</label>
                        <input type="number" name="cost" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Performed By</label>
                        <input type="text" name="performed_by" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Next Maintenance Date</label>
                        <input type="date" name="next_maintenance_date" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Document Modal -->
<div class="modal fade" id="documentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('assets.documents.store', $asset) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Document</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Document Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Document Type</label>
                        <select name="type" class="form-control" required>
                            <option value="invoice">Invoice</option>
                            <option value="warranty">Warranty</option>
                            <option value="manual">Manual</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>File</label>
                        <input type="file" name="file" class="form-control-file" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload Document</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Dispose Modal -->
<div class="modal fade" id="disposeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('assets.dispose', $asset) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Dispose Asset</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Disposal Date</label>
                        <input type="date" name="disposal_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Disposal Value</label>
                        <input type="number" name="disposal_value" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Disposal Reason</label>
                        <textarea name="disposal_reason" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Confirm Disposal</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteDocument(documentId) {
    if (confirm('Are you sure you want to delete this document?')) {
        // Add your delete document logic here
    }
}
</script>
@endpush
@endsection 