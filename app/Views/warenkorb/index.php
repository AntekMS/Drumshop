<?php
/**
 * Warenkorb View
 *
 * @package DrumShop
 */
?>
    <h1 class="my-4">Warenkorb</h1>

<?php if (empty($positionen)) : ?>
    <div class="alert alert-info">
        Ihr Warenkorb ist leer. <a href="<?= base_url('produkte') ?>" class="alert-link">Stöbern Sie in unseren Produkten</a>.
    </div>
<?php else : ?>
    <form action="<?= base_url('warenkorb/aktualisieren') ?>" method="post">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Produkt</th>
                    <th>Preis</th>
                    <th>Menge</th>
                    <th>Summe</th>
                    <th>Aktionen</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($positionen as $position) : ?>
                    <tr>
                        <td>
                            <strong><?= $position['produkt_name'] ?></strong>
                        </td>
                        <td><?= number_format($position['preis'], 2, ',', '.') ?> €</td>
                        <td>
                            <input type="number" name="positionen[<?= $position['id'] ?>]" value="<?= $position['menge'] ?>" min="0" class="form-control w-75">
                        </td>
                        <td><?= number_format($position['preis'] * $position['menge'], 2, ',', '.') ?> €</td>
                        <td>
                            <a href="<?= base_url('warenkorb/entfernen/' . $position['id']) ?>" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i> Entfernen
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="3" class="text-end"><strong>Gesamtsumme:</strong></td>
                    <td><strong><?= number_format($gesamtpreis, 2, ',', '.') ?> €</strong></td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="<?= base_url('produkte') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Weiter einkaufen
            </a>
            <div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sync"></i> Warenkorb aktualisieren
                </button>
                <a href="<?= base_url('checkout') ?>" class="btn btn-success ms-2">
                    <i class="fas fa-check"></i> Zur Kasse
                </a>
            </div>
        </div>
    </form>
<?php endif; ?>