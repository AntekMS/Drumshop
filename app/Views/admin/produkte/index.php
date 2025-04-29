<?php
/**
 * Admin Produkte-Übersicht View
 *
 * @package DrumShop
 */
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Produkte verwalten</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('admin/produkte/neu') ?>" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> Neues Produkt
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Produktübersicht</h5>
        <div class="input-group w-50">
            <input type="text" id="tableSearch" class="form-control" placeholder="Produkte durchsuchen...">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0" id="dataTable">
                <thead>
                <tr>
                    <th width="80">ID</th>
                    <th width="80">Bild</th>
                    <th>Name</th>
                    <th>Kategorie</th>
                    <th class="text-center">Preis</th>
                    <th class="text-center">Bestand</th>
                    <th class="text-center">Status</th>
                    <th width="150">Aktionen</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($produkte as $produkt) :
                    // Kategorie ermitteln
                    $kategorieModel = new \App\Models\KategorieModel();
                    $kategorie = $kategorieModel->find($produkt['kategorie_id']);
                    $kategorieName = $kategorie ? $kategorie['name'] : '-';
                    ?>
                    <tr>
                        <td><?= $produkt['id'] ?></td>
                        <td>
                            <?php if (!empty($produkt['bild_url'])) : ?>
                                <img src="<?= base_url($produkt['bild_url']) ?>" alt="<?= $produkt['name'] ?>" width="50" height="50" class="img-thumbnail">
                            <?php else : ?>
                                <div class="bg-light text-center" style="width: 50px; height: 50px; line-height: 50px;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= $produkt['name'] ?>
                            <small class="text-muted d-block">Art-Nr: <?= $produkt['artikelnummer'] ?? '-' ?></small>
                        </td>
                        <td><?= $kategorieName ?></td>
                        <td class="text-center"><?= number_format($produkt['preis'], 2, ',', '.') ?> €</td>
                        <td class="text-center">
                            <?php if ($produkt['bestand'] <= 0) : ?>
                                <span class="badge bg-danger">Nicht verfügbar</span>
                            <?php elseif ($produkt['bestand'] < 5) : ?>
                                <span class="badge bg-warning"><?= $produkt['bestand'] ?></span>
                            <?php else : ?>
                                <span class="badge bg-success"><?= $produkt['bestand'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($produkt['ist_aktiv']) : ?>
                                <span class="badge bg-success">Aktiv</span>
                            <?php else : ?>
                                <span class="badge bg-danger">Inaktiv</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= base_url('admin/produkte/bearbeiten/' . $produkt['id']) ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= base_url('produkte/detail/' . $produkt['id']) ?>" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= base_url('admin/produkte/loeschen/' . $produkt['id']) ?>" class="btn btn-sm btn-danger delete-confirm">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($produkte)) : ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle"></i> Keine Produkte vorhanden.
                                <a href="<?= base_url('admin/produkte/neu') ?>" class="alert-link">Erstes Produkt erstellen</a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Bestandsstatistik</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <canvas id="bestandsChart" height="250"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="kategorieChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bestandsstatistik Chart
        if (typeof Chart !== 'undefined') {
            // Bestandsübersicht
            const bestandsCtx = document.getElementById('bestandsChart').getContext('2d');

            // Daten aus den Produkten zusammenstellen
            const produkteGesamt = <?= count($produkte) ?>;
            const produkteVerfuegbar = <?= count(array_filter($produkte, function($p) { return $p['bestand'] > 0 && $p['ist_aktiv']; })) ?>;
            const produkteWenigBestand = <?= count(array_filter($produkte, function($p) { return $p['bestand'] > 0 && $p['bestand'] < 5; })) ?>;
            const produkteNichtVerfuegbar = <?= count(array_filter($produkte, function($p) { return $p['bestand'] <= 0; })) ?>;
            const produkteInaktiv = <?= count(array_filter($produkte, function($p) { return !$p['ist_aktiv']; })) ?>;

            new Chart(bestandsCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Verfügbar', 'Wenig Bestand', 'Nicht verfügbar', 'Inaktiv'],
                    datasets: [{
                        data: [
                            produkteVerfuegbar - produkteWenigBestand,
                            produkteWenigBestand,
                            produkteNichtVerfuegbar,
                            produkteInaktiv
                        ],
                        backgroundColor: ['#27ae60', '#f39c12', '#e74c3c', '#95a5a6']
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
                            text: 'Produktbestand Übersicht'
                        }
                    }
                }
            });

            // Kategorienverteilung
            const kategorieCtx = document.getElementById('kategorieChart').getContext('2d');

            <?php
            // Produkte nach Kategorien gruppieren
            $kategorieStatistik = [];
            $kategorieModel = new \App\Models\KategorieModel();
            $alleKategorien = $kategorieModel->findAll();

            foreach ($alleKategorien as $kat) {
                $kategorieStatistik[$kat['name']] = 0;
            }

            foreach ($produkte as $produkt) {
                $katName = '-';
                if (!empty($produkt['kategorie_id'])) {
                    $kat = $kategorieModel->find($produkt['kategorie_id']);
                    if ($kat) {
                        $katName = $kat['name'];
                    }
                }

                if (isset($kategorieStatistik[$katName])) {
                    $kategorieStatistik[$katName]++;
                } else {
                    $kategorieStatistik[$katName] = 1;
                }
            }
            ?>

            const kategorieLabels = <?= json_encode(array_keys($kategorieStatistik)) ?>;
            const kategorieData = <?= json_encode(array_values($kategorieStatistik)) ?>;

            new Chart(kategorieCtx, {
                type: 'bar',
                data: {
                    labels: kategorieLabels,
                    datasets: [{
                        label: 'Anzahl Produkte',
                        data: kategorieData,
                        backgroundColor: '#3498db'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Produkte nach Kategorien'
                        }
                    },
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
    });
</script>