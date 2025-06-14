@extends('layouts.dashboard')

@section('title', isset($account) ? 'Edit Account' : 'Create Account')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ isset($account) ? 'Edit Account' : 'Create Account' }}</h3>
                </div>
                <form action="{{ isset($account) ? route('chart-of-accounts.update', $account) : route('chart-of-accounts.store') }}" 
                      method="POST" 
                      class="card-body">
                    @csrf
                    @if(isset($account))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type_code">Account Type</label>
                                <select name="type_code" 
                                        id="type_code" 
                                        class="form-control @error('type_code') is-invalid @enderror" 
                                        required>
                                    <option value="">Select Type</option>
                                    @foreach($accountTypes as $code => $name)
                                        <option value="{{ $code }}" 
                                                {{ (old('type_code', $account->type_code ?? '') == $code) ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type_code')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="group_code">Group</label>
                                <select name="group_code" 
                                        id="group_code" 
                                        class="form-control @error('group_code') is-invalid @enderror" 
                                        required>
                                    <option value="">Select Group</option>
                                    @foreach($accountGroups as $code => $name)
                                        <option value="{{ $code }}" 
                                                {{ (old('group_code', $account->group_code ?? '') == $code) ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('group_code')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="class_code">Class</label>
                                <select name="class_code" 
                                        id="class_code" 
                                        class="form-control @error('class_code') is-invalid @enderror" 
                                        required>
                                    <option value="">Select Class</option>
                                    @foreach($accountClasses as $code => $name)
                                        <option value="{{ $code }}" 
                                                {{ (old('class_code', $account->class_code ?? '') == $code) ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_code')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="account_code">Account Code</label>
                                <input type="text" 
                                       name="account_code" 
                                       id="account_code" 
                                       class="form-control @error('account_code') is-invalid @enderror" 
                                       value="{{ old('account_code', $account->account_code ?? '') }}" 
                                       required>
                                @error('account_code')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Account Name</label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $account->name ?? '') }}" 
                                       required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="parent_id">Parent Account</label>
                                <select name="parent_id" 
                                        id="parent_id" 
                                        class="form-control @error('parent_id') is-invalid @enderror">
                                    <option value="">None</option>
                                    @foreach($parentAccounts as $parent)
                                        <option value="{{ $parent->id }}" 
                                                {{ (old('parent_id', $account->parent_id ?? '') == $parent->id) ? 'selected' : '' }}>
                                            {{ $parent->account_code }} - {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
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
                                  rows="3">{{ old('description', $account->description ?? '') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" 
                                   class="custom-control-input" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', $account->is_active ?? true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($account) ? 'Update Account' : 'Create Account' }}
                        </button>
                        <a href="{{ route('chart-of-accounts.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Form view loaded'); // Debug log

        // Set up CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Function to update group options
        function updateGroups(typeCode) {
            console.log('Updating groups for type:', typeCode); // Debug log
            
            var groupSelect = $('#group_code');
            var classSelect = $('#class_code');
            
            // Clear both selects
            groupSelect.empty().append('<option value="">Select Group</option>');
            classSelect.empty().append('<option value="">Select Class</option>');
            
            if (typeCode) {
                // Make AJAX call to get groups
                $.ajax({
                    url: '{{ route("chart-of-accounts.account-groups") }}',
                    method: 'GET',
                    data: { type_code: typeCode },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Groups response:', response); // Debug log
                        
                        // Populate the group select
                        $.each(response, function(code, name) {
                            groupSelect.append(
                                $('<option></option>').val(code).html(name)
                            );
                        });

                        // If there's only one group, select it automatically
                        if (Object.keys(response).length === 1) {
                            groupSelect.val(Object.keys(response)[0]).trigger('change');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching groups:', error);
                        console.log('Response:', xhr.responseText); // Debug log
                        alert('Error loading account groups. Please try again.');
                    }
                });
            }
        }

        // Function to update class options
        function updateClasses(typeCode, groupCode) {
            console.log('Updating classes for type:', typeCode, 'group:', groupCode); // Debug log
            
            var classSelect = $('#class_code');
            classSelect.empty().append('<option value="">Select Class</option>');
            
            if (groupCode && typeCode) {
                // Make AJAX call to get classes
                $.ajax({
                    url: '{{ route("chart-of-accounts.account-classes") }}',
                    method: 'GET',
                    data: { 
                        type_code: typeCode,
                        group_code: groupCode
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Classes response:', response); // Debug log
                        
                        // Populate the class select
                        $.each(response, function(code, name) {
                            classSelect.append(
                                $('<option></option>').val(code).html(name)
                            );
                        });

                        // If there's only one class, select it automatically
                        if (Object.keys(response).length === 1) {
                            classSelect.val(Object.keys(response)[0]);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching classes:', error);
                        console.log('Response:', xhr.responseText); // Debug log
                        alert('Error loading account classes. Please try again.');
                    }
                });
            }
        }

        // Event handler for type change
        $('#type_code').on('change', function() {
            var typeCode = $(this).val();
            console.log('Type changed to:', typeCode); // Debug log
            updateGroups(typeCode);
        });

        // Event handler for group change
        $('#group_code').on('change', function() {
            var groupCode = $(this).val();
            var typeCode = $('#type_code').val();
            console.log('Group changed to:', groupCode, 'for type:', typeCode); // Debug log
            updateClasses(typeCode, groupCode);
        });

        // Initialize on page load if type is selected
        var initialType = $('#type_code').val();
        console.log('Form view - Initial type:', initialType); // Debug log
        
        if (initialType) {
            updateGroups(initialType);
            
            var initialGroup = $('#group_code').val();
            if (initialGroup) {
                console.log('Form view - Initial group:', initialGroup); // Debug log
                updateClasses(initialType, initialGroup);
            }
        }
    });
</script>
@endpush 