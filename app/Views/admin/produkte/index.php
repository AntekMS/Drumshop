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
                    $kategorieName = '-';
                    $kategorieHierarchie = '';

                    if (!empty($produkt['kategorie_id']) && isset($kategorien_lookup[$produkt['kategorie_id']])) {
                        $aktuelle_kategorie = $kategorien_lookup[$produkt['kategorie_id']];
                        $kategorieName = $aktuelle_kategorie['name'];

                        // Kategorien-Hierarchie berechnen
                        $hierarchie = [$kategorieName];
                        $eltern_id = $aktuelle_kategorie['eltern_id'];

                        while ($eltern_id && isset($kategorien_lookup[$eltern_id])) {
                            $eltern = $kategorien_lookup[$eltern_id];
                            array_unshift($hierarchie, $eltern['name']);
                            $eltern_id = $eltern['eltern_id'];
                        }

                        $kategorieHierarchie = implode(' > ', $hierarchie);
                    }
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
                        <td>
                            <?php if ($kategorieHierarchie): ?>
                                <span data-bs-toggle="tooltip" title="<?= $kategorieHierarchie ?>"><?= $kategorieName ?></span>
                            <?php else: ?>
                                <?= $kategorieName ?>
                            <?php endif; ?>
                        </td>
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
        // Tooltips aktivieren
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

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

            // Kategorienverteilung mit hierarchischer Struktur
            const kategorieCtx = document.getElementById('kategorieChart').getContext('2d');

            <?php
            // Kategorien für die Statistik vorbereiten
            $kategorieStatistik = [];
            $hauptkategorien = [];

            // Hauptkategorien (ohne Eltern) identifizieren
            foreach ($kategorien as $kategorie) {
                if (empty($kategorie['eltern_id'])) {
                    $hauptkategorien[$kategorie['id']] = $kategorie['name'];
                    $kategorieStatistik[$kategorie['id']] = 0;
                }
            }

            // Wenn keine Hauptkategorien gefunden wurden, alle Kategorien einbeziehen
            if (empty($hauptkategorien)) {
                foreach ($kategorien as $kategorie) {
                    $hauptkategorien[$kategorie['id']] = $kategorie['name'];
                    $kategorieStatistik[$kategorie['id']] = 0;
                }
            }

            // Produkte nach Kategorien zählen und zur Elternkategorie zuordnen
            foreach ($produkte as $produkt) {
                if (!empty($produkt['kategorie_id'])) {
                    $kategorie_id = $produkt['kategorie_id'];
                    $aktuelle_kategorie = isset($kategorien_lookup[$kategorie_id]) ? $kategorien_lookup[$kategorie_id] : null;

                    if ($aktuelle_kategorie) {
                        // Die Hauptkategorie in der Hierarchie finden
                        $eltern_id = $aktuelle_kategorie['eltern_id'];
                        $hauptkategorie_id = $kategorie_id;

                        // Nach oben in der Hierarchie navigieren, bis wir eine Hauptkategorie finden
                        while ($eltern_id && isset($kategorien_lookup[$eltern_id])) {
                            $hauptkategorie_id = $eltern_id;
                            $eltern_id = $kategorien_lookup[$eltern_id]['eltern_id'];
                        }

                        // Wenn die Hauptkategorie in unserer Liste ist, zählen wir das Produkt dort
                        if (isset($kategorieStatistik[$hauptkategorie_id])) {
                            $kategorieStatistik[$hauptkategorie_id]++;
                        } else {
                            // Falls es keine bekannte Hauptkategorie ist, zur direkten Kategorie zählen
                            if (!isset($kategorieStatistik[$kategorie_id])) {
                                $kategorieStatistik[$kategorie_id] = 0;
                                $hauptkategorien[$kategorie_id] = $aktuelle_kategorie['name'];
                            }
                            $kategorieStatistik[$kategorie_id]++;
                        }
                    }
                }
            }

            // Nur Kategorien mit Produkten behalten
            $filteredStats = array_filter($kategorieStatistik, function($count) {
                return $count > 0;
            });

            // Labels und Daten für das Chart vorbereiten
            $kategorieLabels = [];
            $kategorieData = [];

            foreach ($filteredStats as $id => $count) {
                if (isset($hauptkategorien[$id])) {
                    $kategorieLabels[] = $hauptkategorien[$id];
                    $kategorieData[] = $count;
                }
            }

            // Wenn keine Daten vorhanden sind, Platzhalter anzeigen
            if (empty($kategorieLabels)) {
                $kategorieLabels = ['Keine Daten'];
                $kategorieData = [0];
            }
            ?>

            const kategorieLabels = <?= json_encode($kategorieLabels) ?>;
            const kategorieData = <?= json_encode($kategorieData) ?>;

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
                            text: 'Produkte nach Hauptkategorien'
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