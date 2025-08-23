// Admin Panel JavaScript

// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.getElementById('mobileMenu');
    const sidebar = document.querySelector('.admin-sidebar') || document.querySelector('.sidebar');
    
    if (mobileMenuBtn && sidebar) {
        mobileMenuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }
    
    // Close sidebar when clicking outside
    document.addEventListener('click', (e) => {
        if (sidebar?.classList.contains('active') && 
            !sidebar.contains(e.target) && 
            e.target !== mobileMenuBtn) {
            sidebar.classList.remove('active');
        }
    });
});

// Form validation
function validateForm(form) {
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('error');
            
            // Show error message
            let errorMsg = field.dataset.error || 'This field is required';
            let errorDiv = field.parentNode.querySelector('.error-message');
            
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                field.parentNode.appendChild(errorDiv);
            }
            
            errorDiv.textContent = errorMsg;
        } else {
            field.classList.remove('error');
            const errorDiv = field.parentNode.querySelector('.error-message');
            if (errorDiv) errorDiv.remove();
        }
    });
    
    return isValid;
}

// File upload preview
function handleImageUpload(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        const preview = document.querySelector('.image-preview');
        
        reader.onload = function(e) {
            if (preview) {
                preview.style.backgroundImage = `url(${e.target.result})`;
                preview.classList.add('has-image');
            }
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// AJAX form submission
async function submitFormAjax(form, successCallback) {
    try {
        const formData = new FormData(form);
        
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData
        });
        
        if (!response.ok) throw new Error('Network response was not ok');
        
        const result = await response.json();
        
        if (result.success) {
            if (typeof successCallback === 'function') {
                successCallback(result);
            }
        } else {
            throw new Error(result.message || 'Unknown error occurred');
        }
        
    } catch (error) {
        console.error('Error:', error);
        showAlert(error.message, 'error');
    }
}

// Alert/notification system
function showAlert(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    // Add to page
    const container = document.querySelector('.container') || document.body;
    container.insertBefore(alertDiv, container.firstChild);
    
    // Remove after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Confirm delete
function confirmDelete(event) {
    if (!confirm('Are you sure you want to delete this item?')) {
        event.preventDefault();
        return false;
    }
    return true;
}

// Sort table columns
function sortTable(table, column) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const isNumeric = column.dataset.type === 'number';
    
    rows.sort((a, b) => {
        let aVal = a.children[column.cellIndex].textContent.trim();
        let bVal = b.children[column.cellIndex].textContent.trim();
        
        if (isNumeric) {
            return parseFloat(aVal) - parseFloat(bVal);
        }
        return aVal.localeCompare(bVal);
    });
    
    if (column.classList.contains('sort-asc')) {
        rows.reverse();
        column.classList.remove('sort-asc');
        column.classList.add('sort-desc');
    } else {
        column.classList.remove('sort-desc');
        column.classList.add('sort-asc');
    }
    
    tbody.append(...rows);
}

// Initialize sortable tables
document.addEventListener('DOMContentLoaded', function() {
    const tables = document.querySelectorAll('table.sortable');
    
    tables.forEach(table => {
        const headers = table.querySelectorAll('th');
        headers.forEach(header => {
            if (!header.classList.contains('no-sort')) {
                header.style.cursor = 'pointer';
                header.addEventListener('click', () => sortTable(table, header));
            }
        });
    });
});

// Export to CSV
function exportTableToCSV(tableId, filename = 'export.csv') {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const rows = table.querySelectorAll('tr');
    let csv = [];
    
    for (let row of rows) {
        const cols = row.querySelectorAll('td,th');
        const rowData = Array.from(cols)
            .map(col => `"${col.textContent.replace(/"/g, '""')}"`)
            .join(',');
        csv.push(rowData);
    }
    
    const csvContent = "data:text/csv;charset=utf-8," + csv.join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
