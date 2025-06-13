@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Asset Report</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="exportToPDF()">
                            <i class="fas fa-file-pdf"></i> Export to PDF
            </button>
        </div>
    </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ number_format($report->sum(function($group) {
                                        return $group->sum(function($class) {
                                            return $class->sum(function($asset) {
                                                return $asset->assetDetails->purchase_price;
                                            });
                                        });
                                    }), 2) }}</h3>
                                    <p>Total Asset Value</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ number_format($report->sum(function($group) {
                                        return $group->sum(function($class) {
                                            return $class->sum(function($asset) {
                                                return $asset->getCurrentBookValue();
                                            });
                                        });
                                    }), 2) }}</h3>
                                    <p>Current Book Value</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-calculator"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ number_format($report->sum(function($group) {
                                        return $group->sum(function($class) {
                                            return $class->sum(function($asset) {
                                                return $asset->getAccumulatedDepreciation();
                                            });
                                        });
                                    }), 2) }}</h3>
                                    <p>Accumulated Depreciation</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $report->sum(function($group) {
                                        return $group->sum(function($class) {
                                            return $class->count();
                                        });
                                    }) }}</h3>
                                    <p>Total Assets</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-boxes"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs">
                                    <li class="active">
                                        <a href="#summary" data-toggle="tab">Summary</a>
                                    </li>
                                    <li>
                                        <a href="#depreciation" data-toggle="tab">Depreciation</a>
                                    </li>
                                    <li>
                                        <a href="#maintenance" data-toggle="tab">Maintenance</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="summary">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Group</th>
                                                        <th>Category</th>
                                                        <th>Count</th>
                                                        <th>Total Value</th>
                                                        <th>Book Value</th>
                                                        <th>Depreciation</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($report as $groupCode => $group)
                                                        @foreach($group as $classCode => $class)
                                                            <tr>
                                                                <td>{{ $assetGroups[$groupCode] ?? 'N/A' }}</td>
                                                                <td>{{ $assetCategories[$classCode] ?? 'N/A' }}</td>
                                                                <td>{{ $class->count() }}</td>
                                                                <td>{{ number_format($class->sum(function($asset) {
                                                                    return $asset->assetDetails->purchase_price;
                                                                }), 2) }}</td>
                                                                <td>{{ number_format($class->sum(function($asset) {
                                                                    return $asset->getCurrentBookValue();
                                                                }), 2) }}</td>
                                                                <td>{{ number_format($class->sum(function($asset) {
                                                                    return $asset->getAccumulatedDepreciation();
                                                                }), 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="depreciation">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Asset</th>
                                                        <th>Purchase Date</th>
                                                        <th>Purchase Price</th>
                                                        <th>Method</th>
                                                        <th>Rate</th>
                                                        <th>Current Value</th>
                                                        <th>Next Depreciation</th>
                                    </tr>
                                </thead>
                                                <tbody>
                                                    @foreach($report as $group)
                                                        @foreach($group as $class)
                                                            @foreach($class as $asset)
                                                                <tr>
                                                                    <td>{{ $asset->name }}</td>
                                                                    <td>{{ $asset->assetDetails->purchase_date->format('Y-m-d') }}</td>
                                                                    <td>{{ number_format($asset->assetDetails->purchase_price, 2) }}</td>
                                                                    <td>{{ ucfirst(str_replace('_', ' ', $asset->assetDetails->depreciation_method)) }}</td>
                                                                    <td>{{ $asset->assetDetails->depreciation_rate }}%</td>
                                                                    <td>{{ number_format($asset->getCurrentBookValue(), 2) }}</td>
                                                                    <td>{{ $asset->getNextDepreciationDate()->format('Y-m-d') }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="maintenance">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Asset</th>
                                                        <th>Last Maintenance</th>
                                                        <th>Next Maintenance</th>
                                                        <th>Status</th>
                                                        <th>Cost</th>
                                        </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($report as $group)
                                                        @foreach($group as $class)
                                                            @foreach($class as $asset)
                                                                @foreach($asset->maintenanceRecords as $maintenance)
                                                                    <tr>
                                                                        <td>{{ $asset->name }}</td>
                                                                        <td>{{ $maintenance->maintenance_date->format('Y-m-d') }}</td>
                                                                        <td>{{ $maintenance->next_maintenance_date->format('Y-m-d') }}</td>
                                                                        <td>
                                                                            <span class="badge badge-{{ $maintenance->isOverdue() ? 'danger' : 'success' }}">
                                                                                {{ $maintenance->isOverdue() ? 'Overdue' : 'Scheduled' }}
                                                                            </span>
                                                    </td>
                                                                        <td>{{ number_format($maintenance->cost, 2) }}</td>
                                                </tr>
                                            @endforeach
                                                            @endforeach
                                                        @endforeach
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
</div>

@push('scripts')
<script>
function exportToExcel() {
    // Implement Excel export functionality
    alert('Excel export functionality to be implemented');
}

function exportToPDF() {
    // Implement PDF export functionality
    alert('PDF export functionality to be implemented');
}
</script>
@endpush
@endsection 