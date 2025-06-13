@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Asset Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('assets.edit', $asset) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#disposeModal">
                            <i class="fas fa-trash"></i> Dispose
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Basic Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Account Code</th>
                                    <td>{{ $asset->account_code }}</td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $asset->name }}</td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>{{ $asset->description }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge badge-{{ $asset->is_active ? 'success' : 'danger' }}">
                                            {{ $asset->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h4>Asset Details</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Serial Number</th>
                                    <td>{{ $asset->assetDetails->first()?->serial_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Purchase Date</th>
                                    <td>{{ $asset->assetDetails->first()?->purchase_date?->format('Y-m-d') ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Purchase Price</th>
                                    <td>{{ $asset->assetDetails->first() ? number_format($asset->assetDetails->first()->purchase_price, 2) : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Warranty Expiry</th>
                                    <td>{{ $asset->assetDetails->first()?->warranty_expiry?->format('Y-m-d') ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Depreciation Method</th>
                                    <td>{{ $asset->assetDetails->first() ? ucfirst(str_replace('_', ' ', $asset->assetDetails->first()->depreciation_method)) : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Depreciation Rate</th>
                                    <td>{{ $asset->assetDetails->first() ? $asset->assetDetails->first()->depreciation_rate . '%' : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Useful Life</th>
                                    <td>{{ $asset->assetDetails->first() ? $asset->assetDetails->first()->useful_life . ' years' : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Location</th>
                                    <td>{{ $asset->assetDetails->first()?->location ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Condition</th>
                                    <td>{{ $asset->assetDetails->first() ? ucfirst($asset->assetDetails->first()->condition) : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>Financial Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Current Book Value</th>
                                    <td>{{ number_format($asset->getCurrentBookValue(), 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Accumulated Depreciation</th>
                                    <td>{{ number_format($asset->getAccumulatedDepreciation(), 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Next Depreciation Date</th>
                                    <td>{{ $asset->getNextDepreciationDate()->format('Y-m-d') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <ul class="nav nav-tabs" id="assetTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="transactions-tab" data-toggle="tab" href="#transactions" role="tab">
                                        Transactions
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="maintenance-tab" data-toggle="tab" href="#maintenance" role="tab">
                                        Maintenance Records
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="documents-tab" data-toggle="tab" href="#documents" role="tab">
                                        Documents
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content mt-3" id="assetTabsContent">
                                <div class="tab-pane fade show active" id="transactions" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
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
                                                    <td>{{ $transaction->type ? ucfirst(str_replace('_', ' ', $transaction->type)) : 'N/A' }}</td>
                                                    <td>{{ $transaction->amount ? number_format($transaction->amount, 2) : '0.00' }}</td>
                                                    <td>{{ $transaction->description ?? 'N/A' }}</td>
                                                    <td>
                                                        @if($transaction->reference_type && $transaction->reference_id)
                                                            <a href="{{ route($transaction->reference_type . '.show', $transaction->reference_id) }}" class="text-[#01657F] hover:text-[#014d61]">
                                                                View Reference
                                                            </a>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-4 text-gray-500">
                                                        No transactions found for this asset.
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="maintenance" role="tabpanel">
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#maintenanceModal">
                                            <i class="fas fa-plus"></i> Add Maintenance Record
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
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
                                                    <td>{{ $maintenance->maintenance_type ? ucfirst(str_replace('_', ' ', $maintenance->maintenance_type)) : 'N/A' }}</td>
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
                                                    <td colspan="7" class="text-center py-4 text-gray-500">
                                                        No maintenance records found for this asset.
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="documents" role="tabpanel">
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#documentModal">
                                            <i class="fas fa-plus"></i> Add Document
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Type</th>
                                                    <th>Upload Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($asset->documents as $document)
                                                <tr>
                                                    <td>{{ $document->name }}</td>
                                                    <td>{{ $document->type }}</td>
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
                                                @endforeach
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
                        <label>Next Maintenance Date</label>
                        <input type="date" name="next_maintenance_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Cost</label>
                        <input type="number" name="cost" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Performed By</label>
                        <input type="text" name="performed_by" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
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
                        <label>Disposal Amount</label>
                        <input type="number" name="disposal_amount" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Reason</label>
                        <textarea name="reason" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Dispose Asset</button>
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
                            <option value="certificate">Certificate</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>File</label>
                        <input type="file" name="document" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteDocument(documentId) {
    if (confirm('Are you sure you want to delete this document?')) {
        fetch(`/assets/documents/${documentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        });
    }
}
</script>
@endpush
@endsection 