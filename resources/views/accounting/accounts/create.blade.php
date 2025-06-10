@extends('accounting.accounts.form')

@section('title', 'Create Account')

@push('scripts')
<script>
    $(document).ready(function() {
        // Additional initialization for create view
        $('#type_code').trigger('change');
    });
</script>
@endpush 