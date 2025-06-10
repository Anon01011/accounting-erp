@extends('accounting.accounts.form')

@section('title', 'Edit Account')

@push('scripts')
<script>
    $(document).ready(function() {
        // Additional initialization for edit view
        $('#group_code').trigger('change');
    });
</script>
@endpush 