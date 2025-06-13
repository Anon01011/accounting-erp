@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Asset</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('assets.update', $asset) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Basic Information</h4>
                                <div class="form-group">
                                    <label>Asset Group</label>
                                    <select name="group_code" class="form-control" required>
                                        <option value="">Select Group</option>
                                        @foreach($assetGroups as $code => $name)
                                            <option value="{{ $code }}" {{ $asset->group_code == $code ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Asset Category</label>
                                    <select name="class_code" class="form-control" required>
                                        <option value="">Select Category</option>
                                        @foreach($assetCategories as $code => $name)
                                            <option value="{{ $code }}" {{ $asset->class_code == $code ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Account Code</label>
                                    <input type="text" name="account_code" class="form-control" value="{{ $asset->account_code }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $asset->name }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control">{{ $asset->description }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Parent Asset</label>
                                    <select name="parent_id" class="form-control">
                                        <option value="">None</option>
                                        @foreach($parentAssets as $parent)
                                            <option value="{{ $parent->id }}" {{ $asset->parent_id == $parent->id ? 'selected' : '' }}>
                                                {{ $parent->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ $asset->is_active ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">Active</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4>Asset Details</h4>
                                <div class="form-group">
                                    <label>Serial Number</label>
                                    <input type="text" name="serial_number" class="form-control" value="{{ $asset->assetDetails->serial_number }}">
                                </div>
                                <div class="form-group">
                                    <label>Purchase Date</label>
                                    <input type="date" name="purchase_date" class="form-control" value="{{ $asset->assetDetails->purchase_date->format('Y-m-d') }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Purchase Price</label>
                                    <input type="number" name="purchase_price" class="form-control" step="0.01" value="{{ $asset->assetDetails->purchase_price }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Warranty Expiry</label>
                                    <input type="date" name="warranty_expiry" class="form-control" value="{{ $asset->assetDetails->warranty_expiry ? $asset->assetDetails->warranty_expiry->format('Y-m-d') : '' }}">
                                </div>
                                <div class="form-group">
                                    <label>Depreciation Method</label>
                                    <select name="depreciation_method" class="form-control" required>
                                        <option value="straight_line" {{ $asset->assetDetails->depreciation_method == 'straight_line' ? 'selected' : '' }}>
                                            Straight Line
                                        </option>
                                        <option value="declining_balance" {{ $asset->assetDetails->depreciation_method == 'declining_balance' ? 'selected' : '' }}>
                                            Declining Balance
                                        </option>
                                        <option value="sum_of_years" {{ $asset->assetDetails->depreciation_method == 'sum_of_years' ? 'selected' : '' }}>
                                            Sum of Years
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Depreciation Rate (%)</label>
                                    <input type="number" name="depreciation_rate" class="form-control" step="0.01" value="{{ $asset->assetDetails->depreciation_rate }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Useful Life (Years)</label>
                                    <input type="number" name="useful_life" class="form-control" value="{{ $asset->assetDetails->useful_life }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Location</label>
                                    <input type="text" name="location" class="form-control" value="{{ $asset->assetDetails->location }}">
                                </div>
                                <div class="form-group">
                                    <label>Condition</label>
                                    <select name="condition" class="form-control" required>
                                        <option value="new" {{ $asset->assetDetails->condition == 'new' ? 'selected' : '' }}>New</option>
                                        <option value="good" {{ $asset->assetDetails->condition == 'good' ? 'selected' : '' }}>Good</option>
                                        <option value="fair" {{ $asset->assetDetails->condition == 'fair' ? 'selected' : '' }}>Fair</option>
                                        <option value="poor" {{ $asset->assetDetails->condition == 'poor' ? 'selected' : '' }}>Poor</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea name="notes" class="form-control">{{ $asset->assetDetails->notes }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="bg-[#01657F] hover:bg-[#014d61] text-white font-bold py-2 px-4 rounded">
                                    <i class="fas fa-save mr-2"></i>Update Asset
                                </button>
                                <a href="{{ route('assets.show', $asset) }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-generate account code based on group and class
    $('select[name="group_code"], select[name="class_code"]').change(function() {
        var groupCode = $('select[name="group_code"]').val();
        var classCode = $('select[name="class_code"]').val();
        if (groupCode && classCode) {
            // You can implement your own logic for generating account codes
            var accountCode = groupCode + classCode + '0000';
            $('input[name="account_code"]').val(accountCode);
        }
    });
});
</script>
@endpush
@endsection 