document.addEventListener('DOMContentLoaded', function() {
    // Add notification handling
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.id = 'success-notification';
        notification.className = `mb-4 ${type === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700'} border-l-4 p-4 rounded shadow-md`;
        notification.style.transition = 'opacity 0.3s ease-in-out';
        notification.style.opacity = '1';
        
        notification.innerHTML = `
            <div class="flex items-center">
                <div class="py-1">
                    <svg class="h-6 w-6 ${type === 'success' ? 'text-green-500' : 'text-red-500'} mr-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold">${type === 'success' ? 'Success!' : 'Error!'}</p>
                    <p class="text-sm">${message}</p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button onclick="closeNotification()" class="inline-flex rounded-md p-1.5 ${type === 'success' ? 'text-green-500 hover:bg-green-200' : 'text-red-500 hover:bg-red-200'} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-${type === 'success' ? 'green' : 'red'}-500">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;

        const container = document.querySelector('.max-w');
        container.insertBefore(notification, container.firstChild);

        // Auto-hide after 5 seconds
        setTimeout(() => {
            closeNotification();
        }, 5000);
    }

    window.closeNotification = function() {
        const notification = document.getElementById('success-notification');
        if (notification) {
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }
    };

    class JournalEntry {
        constructor() {
            this.lineCounter = 1;
            this.setupEventListeners();
            this.setupInitialLines();
            this.updateTotals();
        }

        setupEventListeners() {
            // Add new line button
            const addLineBtn = document.getElementById('add-line');
            if (addLineBtn) {
                addLineBtn.addEventListener('click', () => {
                    this.addNewLine();
                    this.updateTotals();
                });
            }

            // Form submission
            const form = document.getElementById('journal-entry-form');
            if (form) {
                form.addEventListener('submit', (e) => this.handleSubmit(e));
            }
        }

        setupInitialLines() {
            document.querySelectorAll('.journal-line').forEach(line => {
                this.setupLineListeners(line);
            });
        }

        setupLineListeners(line) {
            // Remove button
            const removeBtn = line.querySelector('.remove-line');
            if (removeBtn) {
                removeBtn.addEventListener('click', () => {
                    if (line.parentElement.children.length > 1) {
                        line.remove();
                        this.updateTotals();
                    }
                });
            }

            // Debit/Credit inputs
            const debitInput = line.querySelector('input[name^="items"][name$="[debit]"]');
            const creditInput = line.querySelector('input[name^="items"][name$="[credit]"]');

            if (debitInput && creditInput) {
                // Handle debit input changes
                debitInput.addEventListener('input', () => {
                    const value = parseFloat(debitInput.value) || 0;
                    if (value > 0) {
                        creditInput.value = '';
                    }
                    this.updateTotals();
                });

                // Handle credit input changes
                creditInput.addEventListener('input', () => {
                    const value = parseFloat(creditInput.value) || 0;
                    if (value > 0) {
                        debitInput.value = '';
                    }
                    this.updateTotals();
                });

                // Auto-balance feature
                debitInput.addEventListener('blur', () => this.autoBalance());
                creditInput.addEventListener('blur', () => this.autoBalance());
            }
        }

        autoBalance() {
            const lines = document.querySelectorAll('.journal-line');
            if (lines.length < 2) return;

            let totalDebit = 0;
            let totalCredit = 0;
            let lastLine = null;
            let lastLineDebit = null;
            let lastLineCredit = null;

            lines.forEach(line => {
                const debitInput = line.querySelector('input[name^="items"][name$="[debit]"]');
                const creditInput = line.querySelector('input[name^="items"][name$="[credit]"]');
                
                if (debitInput && creditInput) {
                    const debit = parseFloat(debitInput.value) || 0;
                    const credit = parseFloat(creditInput.value) || 0;

                    if (debit > 0 || credit > 0) {
                        totalDebit += debit;
                        totalCredit += credit;
                        lastLine = line;
                        lastLineDebit = debitInput;
                        lastLineCredit = creditInput;
                    }
                }
            });

            // If we have a difference and a last line, try to balance it
            if (lastLine && Math.abs(totalDebit - totalCredit) > 0.01) {
                const difference = totalDebit - totalCredit;
                
                // If last line has no value, add the difference
                if (!lastLineDebit.value && !lastLineCredit.value) {
                    if (difference > 0) {
                        lastLineCredit.value = Math.abs(difference).toFixed(2);
                    } else {
                        lastLineDebit.value = Math.abs(difference).toFixed(2);
                    }
                    this.updateTotals();
                }
            }
        }

        addNewLine() {
            const template = document.querySelector('.journal-line');
            if (!template) return;

            const newLine = template.cloneNode(true);
            
            // Update the index in the name attributes
            newLine.querySelectorAll('[name^="items"]').forEach(input => {
                const name = input.getAttribute('name');
                input.setAttribute('name', name.replace(/\[\d+\]/, `[${this.lineCounter}]`));
            });

            // Clear values
            newLine.querySelectorAll('input, select').forEach(input => input.value = '');
            
            const journalLines = document.getElementById('journal-lines');
            if (journalLines) {
                journalLines.appendChild(newLine);
                this.setupLineListeners(newLine);
                this.lineCounter++;
            }
        }

        formatNumber(number) {
            return number.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        updateTotals() {
            let debitTotal = 0;
            let creditTotal = 0;
            let hasValidLine = false;

            document.querySelectorAll('.journal-line').forEach(line => {
                const debitInput = line.querySelector('input[name^="items"][name$="[debit]"]');
                const creditInput = line.querySelector('input[name^="items"][name$="[credit]"]');
                
                if (debitInput && creditInput) {
                    const debit = parseFloat(debitInput.value) || 0;
                    const credit = parseFloat(creditInput.value) || 0;

                    if (debit > 0 || credit > 0) {
                        hasValidLine = true;
                    }

                    debitTotal += debit;
                    creditTotal += credit;
                }
            });

            // Update total displays
            const debitTotalElement = document.getElementById('debit-total');
            const creditTotalElement = document.getElementById('credit-total');
            const balanceElement = document.getElementById('balance');
            const balanceStatusElement = document.getElementById('balance-status');

            if (debitTotalElement) {
                debitTotalElement.textContent = this.formatNumber(debitTotal);
            }

            if (creditTotalElement) {
                creditTotalElement.textContent = this.formatNumber(creditTotal);
            }

            if (balanceElement && balanceStatusElement) {
                const balance = debitTotal - creditTotal;
                const absBalance = Math.abs(balance);
                balanceElement.textContent = this.formatNumber(absBalance);

                // Update balance status with more detailed information
                if (!hasValidLine) {
                    balanceElement.classList.remove('text-red-600', 'text-green-600');
                    balanceElement.classList.add('text-gray-600');
                    balanceStatusElement.textContent = 'No Entries';
                    balanceStatusElement.classList.remove('text-red-600', 'text-green-600');
                    balanceStatusElement.classList.add('text-gray-600');
                } else if (Math.abs(balance) < 0.01) {
                    balanceElement.classList.remove('text-red-600', 'text-gray-600');
                    balanceElement.classList.add('text-green-600');
                    balanceStatusElement.textContent = 'Balanced';
                    balanceStatusElement.classList.remove('text-red-600', 'text-gray-600');
                    balanceStatusElement.classList.add('text-green-600');
                } else {
                    balanceElement.classList.remove('text-green-600', 'text-gray-600');
                    balanceElement.classList.add('text-red-600');
                    const difference = this.formatNumber(Math.abs(balance));
                    balanceStatusElement.textContent = `Not Balanced (Difference: ${difference})`;
                    balanceStatusElement.classList.remove('text-green-600', 'text-gray-600');
                    balanceStatusElement.classList.add('text-red-600');
                }
            }

            // Update total colors
            if (!hasValidLine) {
                if (debitTotalElement) debitTotalElement.classList.remove('text-red-600');
                if (creditTotalElement) creditTotalElement.classList.remove('text-red-600');
            } else if (Math.abs(debitTotal - creditTotal) > 0.01) {
                if (debitTotalElement) debitTotalElement.classList.add('text-red-600');
                if (creditTotalElement) creditTotalElement.classList.add('text-red-600');
            } else {
                if (debitTotalElement) debitTotalElement.classList.remove('text-red-600');
                if (creditTotalElement) creditTotalElement.classList.remove('text-red-600');
            }
        }

        async handleSubmit(e) {
            e.preventDefault();
            
            if (!this.validateForm(e)) {
                return;
            }

            const form = e.target;
            const formData = new FormData(form);
            
            // Convert items array to proper format
            const items = [];
            document.querySelectorAll('.journal-line').forEach((line, index) => {
                const accountSelect = line.querySelector('select[name^="items"][name$="[chart_of_account_id]"]');
                const debitInput = line.querySelector('input[name^="items"][name$="[debit]"]');
                const creditInput = line.querySelector('input[name^="items"][name$="[credit]"]');
                const descriptionInput = line.querySelector('input[name^="items"][name$="[description]"]');

                if (accountSelect && (debitInput.value || creditInput.value)) {
                    items.push({
                        chart_of_account_id: accountSelect.value,
                        debit: debitInput.value || 0,
                        credit: creditInput.value || 0,
                        description: descriptionInput ? descriptionInput.value : null
                    });
                }
            });

            // Add items to formData
            formData.delete('items');
            items.forEach((item, index) => {
                formData.append(`items[${index}][chart_of_account_id]`, item.chart_of_account_id);
                formData.append(`items[${index}][debit]`, item.debit);
                formData.append(`items[${index}][credit]`, item.credit);
                if (item.description) {
                    formData.append(`items[${index}][description]`, item.description);
                }
            });

            const submitButton = form.querySelector('button[type="submit"]');
            
            try {
                // Disable submit button
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                `;

                const response = await fetch(form.action, {
                    method: form.method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    showNotification(data.message || 'Journal entry created successfully');
                    // Reset form or redirect if needed
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                } else {
                    // Handle validation errors
                    if (response.status === 422 && data.errors) {
                        const errorMessages = Object.values(data.errors).flat();
                        showNotification(errorMessages.join('<br>'), 'error');
                    } else {
                        showNotification(data.message || 'Error creating journal entry', 'error');
                    }
                }
            } catch (error) {
                console.error('Submission error:', error);
                showNotification('An error occurred while processing your request', 'error');
            } finally {
                // Re-enable submit button
                submitButton.disabled = false;
                submitButton.innerHTML = form.dataset.submitText || 'Submit';
            }
        }

        validateForm(e) {
            let isValid = true;
            let debitTotal = 0;
            let creditTotal = 0;
            let hasValidLine = false;
            let errorMessages = [];

            document.querySelectorAll('.journal-line').forEach((line, index) => {
                const debitInput = line.querySelector('input[name^="items"][name$="[debit]"]');
                const creditInput = line.querySelector('input[name^="items"][name$="[credit]"]');
                const accountSelect = line.querySelector('select[name^="items"][name$="[chart_of_account_id]"]');
                
                if (!debitInput || !creditInput || !accountSelect) return;

                const debit = parseFloat(debitInput.value) || 0;
                const credit = parseFloat(creditInput.value) || 0;

                // Check if this line has any values
                if (debit > 0 || credit > 0) {
                    hasValidLine = true;

                    // Check if account is selected
                    if (!accountSelect.value) {
                        errorMessages.push(`Line ${index + 1}: Please select an account`);
                        isValid = false;
                    }

                    // Check if both debit and credit have values
                    if (debit > 0 && credit > 0) {
                        errorMessages.push(`Line ${index + 1}: Cannot have both debit and credit amounts`);
                        isValid = false;
                    }
                }

                debitTotal += debit;
                creditTotal += credit;
            });

            // Check if we have at least one valid line
            if (!hasValidLine) {
                errorMessages.push('Please add at least one journal entry line');
                isValid = false;
            }

            // Check if totals match
            if (hasValidLine && Math.abs(debitTotal - creditTotal) > 0.01) {
                const difference = this.formatNumber(Math.abs(debitTotal - creditTotal));
                errorMessages.push(`Debit and Credit totals must be equal (Current difference: ${difference})`);
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                alert(errorMessages.join('\n'));
            }

            return isValid;
        }
    }

    // Initialize the journal entry functionality
    new JournalEntry();
}); 