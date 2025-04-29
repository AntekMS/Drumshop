/**
 * DrumShop - Admin-Bereich Skript
 * Speicherort: public/assets/js/admin.js
 */

document.addEventListener('DOMContentLoaded', function() {

    // Initialisiere Bootstrap-Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Mobile Sidebar-Toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }

    // Bestätigung für Löschaktionen
    const deleteButtons = document.querySelectorAll('.delete-confirm');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Sind Sie sicher, dass Sie diesen Eintrag löschen möchten? Diese Aktion kann nicht rückgängig gemacht werden!')) {
                e.preventDefault();
            }
        });
    });

    // Bild-Vorschau für Datei-Uploads
    const imageInputs = document.querySelectorAll('.image-upload');
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            const previewContainer = document.getElementById(this.dataset.preview);
            if (!previewContainer) return;

            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.src = e.target.result;
                    previewContainer.classList.remove('d-none');
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // Toggle für aktiv/inaktiv Status
    const toggleStatusSwitches = document.querySelectorAll('.toggle-status');
    toggleStatusSwitches.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const itemId = this.dataset.id;
            const itemType = this.dataset.type;
            const status = this.checked ? 1 : 0;

            // AJAX-Request zum Aktualisieren des Status
            fetch(`/admin/${itemType}/toggle-status/${itemId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ status: status })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Status wurde erfolgreich aktualisiert
                        showNotification('Status erfolgreich aktualisiert', 'success');
                    } else {
                        // Fehler beim Aktualisieren des Status
                        showNotification('Fehler beim Aktualisieren des Status', 'error');
                        // Switch zurücksetzen
                        this.checked = !this.checked;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Fehler bei der Kommunikation mit dem Server', 'error');
                    // Switch zurücksetzen
                    this.checked = !this.checked;
                });
        });
    });

    // Benachrichtigungen anzeigen
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} notification`;
        notification.innerHTML = message;

        document.body.appendChild(notification);

        // Animation zum Einblenden
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateY(0)';
        }, 10);

        // Ausblenden nach 5 Sekunden
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-20px)';

            // Entfernen nach Animation
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
    }

    // Bestellstatus-Änderung
    const bestellstatusSelect = document.getElementById('bestellstatus');
    const zahlungsstatusSelect = document.getElementById('zahlungsstatus');

    if (bestellstatusSelect) {
        bestellstatusSelect.addEventListener('change', function() {
            const statusColor = this.options[this.selectedIndex].dataset.color;
            this.classList.remove('bg-primary', 'bg-warning', 'bg-success', 'bg-danger', 'bg-info');
            if (statusColor) {
                this.classList.add(statusColor);
            }
        });
    }

    if (zahlungsstatusSelect) {
        zahlungsstatusSelect.addEventListener('change', function() {
            const statusColor = this.options[this.selectedIndex].dataset.color;
            this.classList.remove('bg-primary', 'bg-warning', 'bg-success', 'bg-danger');
            if (statusColor) {
                this.classList.add(statusColor);
            }
        });
    }

    // Tabellenfilter und Suche
    const tableSearchInput = document.getElementById('tableSearch');
    const dataTable = document.getElementById('dataTable');

    if (tableSearchInput && dataTable) {
        tableSearchInput.addEventListener('keyup', function() {
            const searchText = this.value.toLowerCase();
            const rows = dataTable.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.indexOf(searchText) > -1) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Dashboard Charts (wenn Chart.js verfügbar ist)
    if (typeof Chart !== 'undefined') {
        // Umsatzstatistik Chart
        const salesChartCanvas = document.getElementById('salesChart');
        if (salesChartCanvas) {
            const salesChart = new Chart(salesChartCanvas, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'],
                    datasets: [{
                        label: 'Umsatz',
                        data: salesChartCanvas.dataset.values ? JSON.parse(salesChartCanvas.dataset.values) : [],
                        backgroundColor: 'rgba(52, 152, 219, 0.2)',
                        borderColor: 'rgba(52, 152, 219, 1)',
                        borderWidth: 2,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + ' €';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Bestellungsstatistik Chart
        const ordersChartCanvas = document.getElementById('ordersChart');
        if (ordersChartCanvas) {
            const ordersChart = new Chart(ordersChartCanvas, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'],
                    datasets: [{
                        label: 'Bestellungen',
                        data: ordersChartCanvas.dataset.values ? JSON.parse(ordersChartCanvas.dataset.values) : [],
                        backgroundColor: 'rgba(46, 204, 113, 0.7)',
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        // Produktkategorien Chart
        const categoriesChartCanvas = document.getElementById('categoriesChart');
        if (categoriesChartCanvas) {
            const labels = categoriesChartCanvas.dataset.labels ? JSON.parse(categoriesChartCanvas.dataset.labels) : [];
            const values = categoriesChartCanvas.dataset.values ? JSON.parse(categoriesChartCanvas.dataset.values) : [];

            const categoriesChart = new Chart(categoriesChartCanvas, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: [
                            'rgba(52, 152, 219, 0.7)',
                            'rgba(46, 204, 113, 0.7)',
                            'rgba(155, 89, 182, 0.7)',
                            'rgba(230, 126, 34, 0.7)',
                            'rgba(231, 76, 60, 0.7)',
                            'rgba(149, 165, 166, 0.7)'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
        }
    }

    // Dynamisches Hinzufügen von Formularfeldern (z.B. für Produktvarianten)
    const addFieldButton = document.getElementById('addFieldButton');
    const fieldContainer = document.getElementById('fieldContainer');

    if (addFieldButton && fieldContainer) {
        let fieldIndex = document.querySelectorAll('.dynamic-field').length;

        addFieldButton.addEventListener('click', function() {
            const fieldTemplate = document.getElementById('fieldTemplate');
            if (!fieldTemplate) return;

            const newField = fieldTemplate.content.cloneNode(true);
            const inputs = newField.querySelectorAll('input, select, textarea');

            // Indizes und IDs aktualisieren
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace('INDEX', fieldIndex));
                }

                const id = input.getAttribute('id');
                if (id) {
                    input.setAttribute('id', id.replace('INDEX', fieldIndex));
                }
            });

            // Event-Handler für Remove-Button
            const removeButton = newField.querySelector('.remove-field');
            if (removeButton) {
                removeButton.addEventListener('click', function() {
                    this.closest('.dynamic-field').remove();
                });
            }

            fieldContainer.appendChild(newField);
            fieldIndex++;
        });
    }
});