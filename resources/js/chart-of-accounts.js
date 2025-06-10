document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type_code');
    const groupSelect = document.getElementById('group_code');
    const classSelect = document.getElementById('class_code');
    const accountNoInput = document.getElementById('account_no');

    if (!typeSelect || !groupSelect || !classSelect || !accountNoInput) {
        return; // Exit if elements don't exist
    }

    // Filter groups based on selected type
    typeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        Array.from(groupSelect.options).forEach(option => {
            if (option.value === '') return; // Skip the default option
            option.style.display = option.dataset.type === selectedType ? '' : 'none';
        });
        groupSelect.value = ''; // Reset selection
        classSelect.value = ''; // Reset class selection
        accountNoInput.value = ''; // Reset account number
    });

    // Filter classes based on selected group
    groupSelect.addEventListener('change', function() {
        const selectedGroup = this.value;
        Array.from(classSelect.options).forEach(option => {
            if (option.value === '') return; // Skip the default option
            option.style.display = option.dataset.group === selectedGroup ? '' : 'none';
        });
        classSelect.value = ''; // Reset selection
        accountNoInput.value = ''; // Reset account number
    });

    // Auto-generate account number when class is selected
    classSelect.addEventListener('change', function() {
        if (this.value && typeSelect.value && groupSelect.value) {
            // Show loading state
            accountNoInput.value = 'Generating...';
            accountNoInput.disabled = true;

            // Make AJAX call to get next account number
            fetch(`/api/chart-of-accounts/generate-account-number`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    type_code: typeSelect.value,
                    group_code: groupSelect.value,
                    class_code: this.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    accountNoInput.value = data.account_number;
                } else {
                    accountNoInput.value = '';
                    alert('Failed to generate account number. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                accountNoInput.value = '';
                alert('An error occurred while generating the account number.');
            })
            .finally(() => {
                accountNoInput.disabled = false;
            });
        }
    });

    // Initialize form state
    if (typeSelect.value) {
        typeSelect.dispatchEvent(new Event('change'));
    }
    if (groupSelect.value) {
        groupSelect.dispatchEvent(new Event('change'));
    }
}); 