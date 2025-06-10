@extends('layouts.app')

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
<script>
    $(document).ready(function() {
        // Update class options based on selected group
        $('#group_code').change(function() {
            var groupCode = $(this).val();
            var classSelect = $('#class_code');
            classSelect.empty().append('<option value="">Select Class</option>');
            if (groupCode) {
                $.get('/api/account-classes/' + groupCode, function(classes) {
                    $.each(classes, function(code, name) {
                        classSelect.append(
                            $('<option></option>').val(code).html(name)
                        );
                    });
                });
            }
        });
    });
</script>
@endpush 