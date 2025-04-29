<?php
/**
 * Admin Kategorien Übersicht
 *
 * @package DrumShop
 */
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Kategorien verwalten</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('admin/kategorien/neu') ?>" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> Neue Kategorie
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Kategorien</h5>
        <div class="input-group w-50">
            <input type="text" id="tableSearch" class="form-control" placeholder="Kategorien durchsuchen...">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0" id="dataTable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Bild</th>
                    <th>Name</th>
                    <th>Elternkategorie</th>
                    <th>Produkte</th>
                    <th>Erstellt am</th>
                    <th>Aktionen</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($kategorien as $kategorie) : ?>
                    <tr>
                        <td><?= $kategorie['id'] ?></td>
                        <td>
                            <?php if (!empty($kategorie['bild_url'])) : ?>
                                <img src="<?= base_url($kategorie['bild_url']) ?>" width="50" alt="<?= $kategorie['name'] ?>">
                            <?php else : ?>
                                <span class="badge bg-secondary">Kein Bild</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $kategorie['name'] ?></td>
                        <td>
                            <?php
                            if (!empty($kategorie['eltern_id'])) {
                                $eltern = null;
                                foreach ($kategorien as $k) {
                                    if ($k['id'] == $kategorie['eltern_id']) {
                                        $eltern = $k;
                                        break;
                                    }
                                }
                                echo $eltern ? $eltern['name'] : 'Unbekannt';
                            } else {
                                echo '<span class="badge bg-info">Hauptkategorie</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            $count = 0;
                            if (isset($produkt_counts) && isset($produkt_counts[$kategorie['id']])) {
                                $count = $produkt_counts[$kategorie['id']];
                            }
                            echo '<span class="badge bg-' . ($count > 0 ? 'success' : 'secondary') . '">' . $count . '</span>';
                            ?>
                        </td>
                        <td><?= date('d.m.Y H:i', strtotime($kategorie['erstellt_am'])) ?></td>
                        <td>
                            <a href="<?= base_url('admin/kategorien/bearbeiten/' . $kategorie['id']) ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= base_url('admin/kategorien/loeschen/' . $kategorie['id']) ?>" class="btn btn-sm btn-danger delete-confirm">
                                <i class="fas fa-trash"></i>
                            </a>
                            <a href="<?= base_url('produkte/kategorie/' . $kategorie['id']) ?>" class="btn btn-sm btn-info" target="_blank">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Kategorie-Hierarchie</h5>
            </div>
            <div class="card-body">
                <div class="category-tree">
                    <?php
                    // Hilfsfunktion zum rekursiven Aufbau des Kategoriebaums
                    function buildCategoryTree($kategorien, $parent_id = null) {
                        $html = '<ul' . ($parent_id === null ? ' class="list-unstyled"' : '') . '>';

                        foreach ($kategorien as $kategorie) {
                            if ($kategorie['eltern_id'] == $parent_id) {
                                $html .= '<li>';
                                $html .= '<div class="d-flex align-items-center mb-2">';
                                $html .= '<i class="fas fa-folder me-2 text-warning"></i>';
                                $html .= '<strong>' . $kategorie['name'] . '</strong>';
                                $html .= '</div>';

                                // Nach Unterkategorien suchen
                                $hasChildren = false;
                                foreach ($kategorien as $child) {
                                    if ($child['eltern_id'] == $kategorie['id']) {
                                        $hasChildren = true;
                                        break;
                                    }
                                }

                                if ($hasChildren) {
                                    $html .= buildCategoryTree($kategorien, $kategorie['id']);
                                }

                                $html .= '</li>';
                            }
                        }

                        $html .= '</ul>';
                        return $html;
                    }

                    // Kategoriebaum erzeugen und ausgeben
                    echo buildCategoryTree($kategorien);
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Statistik</h5>
            </div>
            <div class="card-body">
                <canvas id="kategorieChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<style>
    .category-tree ul {
        padding-left: 20px;
        margin-bottom: 10px;
    }
    .category-tree li {
        margin-bottom: 5px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Chart !== 'undefined') {
            // Kategorien-Chart vorbereiten
            const ctx = document.getElementById('kategorieChart').getContext('2d');

            // Daten sammeln
            const kategorien = <?= json_encode(array_column($kategorien, 'name')) ?>;

            // Produkt-Counts (falls verfügbar)
            let produktCounts = [];
            <?php if (isset($produkt_counts)): ?>
            produktCounts = <?= json_encode(array_values($produkt_counts)) ?>;
            <?php else: ?>
            // Dummy-Daten, falls keine echten verfügbar
            produktCounts = kategorien.map(() => Math.floor(Math.random() * 10));
            <?php endif; ?>

            // Chart erstellen
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: kategorien,
                    datasets: [{
                        label: 'Anzahl Produkte',
                        data: produktCounts,
                        backgroundColor: '#3498db',
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    });
</script>