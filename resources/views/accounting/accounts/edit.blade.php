@extends('accounting.accounts.form')

@section('title', 'Edit Account')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Edit view loaded'); // Debug log
        
        // Wait for a short moment to ensure the form is fully loaded
        setTimeout(function() {
            var typeCode = $('#type_code').val();
            var groupCode = $('#group_code').val();
            
            console.log('Edit view - Initial type:', typeCode);
            console.log('Edit view - Initial group:', groupCode);
            
            if (typeCode) {
                // Trigger type change to load groups
                $('#type_code').trigger('change');
                
                // After groups are loaded, trigger group change
                setTimeout(function() {
                    if (groupCode) {
                        $('#group_code').trigger('change');
                    }
                }, 500);
            }
        }, 100);
    });
</script>
@endpush 