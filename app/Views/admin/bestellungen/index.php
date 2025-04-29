<?php
/**
 * Admin Bestellungsübersicht View
 *
 * @package DrumShop
 */
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Bestellungen</h1>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Neue Bestellungen</h6>
                        <h2 class="mb-0"><?= count(array_filter($bestellungen, function($b) { return $b['status'] == 'neu'; })) ?></h2>
                    </div>
                    <i class="fas fa-shopping-cart fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Offene Zahlungen</h6>
                        <h2 class="mb-0"><?= count(array_filter($bestellungen, function($b) { return $b['zahlungsstatus'] == 'ausstehend'; })) ?></h2>
                    </div>
                    <i class="fas fa-euro-sign fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Umsatz</h6>
                        <h2 class="mb-0"><?= number_format(array_sum(array_column(array_filter($bestellungen, function($b) { return $b['zahlungsstatus'] == 'bezahlt'; }), 'gesamtpreis')), 2, ',', '.') ?> €</h2>
                    </div>
                    <i class="fas fa-chart-line fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Bestellungen</h5>
        <div class="input-group w-50">
            <input type="text" id="tableSearch" class="form-control" placeholder="Bestellungen durchsuchen...">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0" id="dataTable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Datum</th>
                    <th>Kunde</th>
                    <th>Betrag</th>
                    <th>Status</th>
                    <th>Zahlung</th>
                    <th>Aktionen</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($bestellungen as $bestellung) : ?>
                    <tr>
                        <td>#<?= $bestellung['id'] ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($bestellung['erstellt_am'])) ?></td>
                        <td>
                            <?= $bestellung['kunde_name'] ?>
                            <small class="text-muted d-block"><?= $bestellung['kunde_email'] ?></small>
                        </td>
                        <td><?= number_format($bestellung['gesamtpreis'], 2, ',', '.') ?> €</td>
                        <td>
                                <span class="badge <?php
                                switch ($bestellung['status']) {
                                    case 'neu': echo 'bg-primary'; break;
                                    case 'bearbeitet': echo 'bg-warning'; break;
                                    case 'versandt': echo 'bg-info'; break;
                                    case 'geliefert': echo 'bg-success'; break;
                                    case 'storniert': echo 'bg-danger'; break;
                                    default: echo 'bg-secondary';
                                }
                                ?>">
                                    <?= $bestellung['status'] ?>
                                </span>
                        </td>
                        <td>
                                <span class="badge <?php
                                switch ($bestellung['zahlungsstatus']) {
                                    case 'ausstehend': echo 'bg-warning'; break;
                                    case 'bezahlt': echo 'bg-success'; break;
                                    case 'zurückerstattet': echo 'bg-danger'; break;
                                    default: echo 'bg-secondary';
                                }
                                ?>">
                                    <?= $bestellung['zahlungsstatus'] ?>
                                </span>
                        </td>
                        <td>
                            <a href="<?= base_url('admin/bestellungen/detail/' . $bestellung['id']) ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= base_url('admin/bestellungen/email/' . $bestellung['id']) ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-envelope"></i>
                            </a>
                            <a href="<?= base_url('admin/bestellungen/rechnung/' . $bestellung['id']) ?>" class="btn btn-sm btn-secondary">
                                <i class="fas fa-file-invoice"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Zahlungsstatistik</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <canvas id="statusChart" height="250"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="zahlungsChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Chart !== 'undefined') {
            // Status-Statistik
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            const statusLabels = ['Neu', 'In Bearbeitung', 'Versandt', 'Geliefert', 'Storniert'];

            const statusData = [
                <?= count(array_filter($bestellungen, function($b) { return $b['status'] == 'neu'; })) ?>,
                <?= count(array_filter($bestellungen, function($b) { return $b['status'] == 'bearbeitet'; })) ?>,
                <?= count(array_filter($bestellungen, function($b) { return $b['status'] == 'versandt'; })) ?>,
                <?= count(array_filter($bestellungen, function($b) { return $b['status'] == 'geliefert'; })) ?>,
                <?= count(array_filter($bestellungen, function($b) { return $b['status'] == 'storniert'; })) ?>
            ];

            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusData,
                        backgroundColor: ['#0d6efd', '#ffc107', '#0dcaf0', '#198754', '#dc3545']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: 'Bestellstatus'
                        }
                    }
                }
            });

            // Zahlungs-Statistik
            const zahlungCtx = document.getElementById('zahlungsChart').getContext('2d');
            const zahlungLabels = ['Ausstehend', 'Bezahlt', 'Zurückerstattet'];

            const zahlungData = [
                <?= count(array_filter($bestellungen, function($b) { return $b['zahlungsstatus'] == 'ausstehend'; })) ?>,
                <?= count(array_filter($bestellungen, function($b) { return $b['zahlungsstatus'] == 'bezahlt'; })) ?>,
                <?= count(array_filter($bestellungen, function($b) { return $b['zahlungsstatus'] == 'zurückerstattet'; })) ?>
            ];

            new Chart(zahlungCtx, {
                type: 'pie',
                data: {
                    labels: zahlungLabels,
                    datasets: [{
                        data: zahlungData,
                        backgroundColor: ['#ffc107', '#198754', '#dc3545']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: 'Zahlungsstatus'
                        }
                    }
                }
            });
        }
    });
</script>