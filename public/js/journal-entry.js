document.addEventListener('DOMContentLoaded', function() {
    // Function to handle amount input changes
    function handleAmountInput(input, otherInput) {
        const currentValue = parseFloat(input.value) || 0;
        const otherValue = parseFloat(otherInput.value) || 0;

        // Only clear the other field if both fields have values
        if (currentValue > 0 && otherValue > 0) {
            otherInput.value = '';
        }
    }

    // Function to setup line listeners
    function setupLineListeners(line) {
        const removeBtn = line.querySelector('.remove-line');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                if (line.parentElement.children.length > 1) {
                    line.remove();
                }
            });
        }

        // Handle debit/credit input
        const debitInput = line.querySelector('input[name="debits[]"], input[name="items[][debit]"]');
        const creditInput = line.querySelector('input[name="credits[]"], input[name="items[][credit]"]');

        if (debitInput && creditInput) {
            debitInput.addEventListener('input', function() {
                handleAmountInput(this, creditInput);
            });

            creditInput.addEventListener('input', function() {
                handleAmountInput(this, debitInput);
            });
        }
    }

    // Setup initial lines
    document.querySelectorAll('.journal-line').forEach(setupLineListeners);

    // Add new line button handler
    const addLineBtn = document.getElementById('add-line');
    if (addLineBtn) {
        addLineBtn.addEventListener('click', function() {
            const template = document.querySelector('.journal-line').cloneNode(true);
            template.querySelectorAll('input, select').forEach(input => input.value = '');
            document.getElementById('journal-lines').appendChild(template);
            setupLineListeners(template);
        });
    }

    // Form validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            let debitTotal = 0;
            let creditTotal = 0;

            document.querySelectorAll('.journal-line').forEach(line => {
                const debitInput = line.querySelector('input[name="debits[]"], input[name="items[][debit]"]');
                const creditInput = line.querySelector('input[name="credits[]"], input[name="items[][credit]"]');
                
                const debit = parseFloat(debitInput.value) || 0;
                const credit = parseFloat(creditInput.value) || 0;

                debitTotal += debit;
                creditTotal += credit;

                // Check if both debit and credit have values
                if (debit > 0 && credit > 0) {
                    isValid = false;
                    alert('A transaction cannot have both debit and credit amounts!');
                    e.preventDefault();
                    return;
                }

                // Check if neither debit nor credit has a value
                if (debit === 0 && credit === 0) {
                    isValid = false;
                    alert('Each transaction must have either a debit or credit amount!');
                    e.preventDefault();
                    return;
                }
            });

            // Check if totals match
            if (Math.abs(debitTotal - creditTotal) > 0.01) {
                isValid = false;
                alert('Debit and Credit totals must be equal!');
                e.preventDefault();
                return;
            }
        });
    }
}); 