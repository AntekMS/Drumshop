<?php
/**
 * Admin Produkt-Formular View
 *
 * @package DrumShop
 */

// Prüfen, ob es sich um einen neuen Eintrag oder eine Bearbeitung handelt
$isEdit = isset($produkt);
$formTitle = $isEdit ? 'Produkt bearbeiten' : 'Neues Produkt anlegen';
$submitUrl = $isEdit ? base_url('admin/produkte/aktualisieren/' . $produkt['id']) : base_url('admin/produkte/speichern');
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $formTitle ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('admin/produkte') ?>" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Zurück zur Übersicht
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Produktdaten</h5>
            </div>
            <div class="card-body">
                <form action="<?= $submitUrl ?>" method="post" enctype="multipart/form-data">
                    <!-- Allgemeine Produktdaten -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="name" class="form-label">Produktname *</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                   value="<?= $isEdit ? $produkt['name'] : '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="artikelnummer" class="form-label">Artikelnummer</label>
                            <input type="text" class="form-control" id="artikelnummer" name="artikelnummer"
                                   value="<?= $isEdit ? $produkt['artikelnummer'] : '' ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="beschreibung" class="form-label">Beschreibung *</label>
                        <textarea class="form-control" id="beschreibung" name="beschreibung" rows="5" required><?= $isEdit ? $produkt['beschreibung'] : '' ?></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="kategorie_id" class="form-label">Kategorie</label>
                            <select class="form-select" id="kategorie_id" name="kategorie_id">
                                <option value="">-- Keine Kategorie --</option>
                                <?php foreach ($kategorien as $kategorie) :
                                    $selected = $isEdit && $produkt['kategorie_id'] == $kategorie['id'] ? 'selected' : '';
                                    ?>
                                    <option value="<?= $kategorie['id'] ?>" <?= $selected ?>><?= $kategorie['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="preis" class="form-label">Preis (€) *</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="preis" name="preis" min="0" step="0.01" required
                                       value="<?= $isEdit ? $produkt['preis'] : '' ?>">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="bestand" class="form-label">Bestand *</label>
                            <input type="number" class="form-control" id="bestand" name="bestand" min="0" required
                                   value="<?= $isEdit ? $produkt['bestand'] : '0' ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="gewicht" class="form-label">Gewicht (kg)</label>
                            <input type="number" class="form-control" id="gewicht" name="gewicht" min="0" step="0.01"
                                   value="<?= $isEdit ? $produkt['gewicht'] : '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="abmessungen" class="form-label">Abmessungen (B×H×T)</label>
                            <input type="text" class="form-control" id="abmessungen" name="abmessungen" placeholder="z.B. 40×50×30 cm"
                                   value="<?= $isEdit ? $produkt['abmessungen'] : '' ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="hervorgehoben" name="hervorgehoben" value="1"
                                    <?= $isEdit && $produkt['hervorgehoben'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="hervorgehoben">Auf Startseite hervorheben</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="ist_aktiv" name="ist_aktiv" value="1"
                                    <?= $isEdit && $produkt['ist_aktiv'] ? 'checked' : '' ?> <?= !$isEdit ? 'checked' : '' ?>>
                                <label class="form-check-label" for="ist_aktiv">Produkt ist aktiv</label>
                            </div>
                        </div>
                    </div>

                    <!-- Bild-Upload -->
                    <div class="mb-4">
                        <label for="bild" class="form-label">Produktbild</label>
                        <input type="file" class="form-control image-upload" id="bild" name="bild" accept="image/*" data-preview="bildPreview">
                        <div class="form-text">Empfohlene Größe: 800×800 Pixel. Max. 2MB.</div>
                    </div>

                    <?php if ($isEdit && !empty($produkt['bild_url'])) : ?>
                        <div class="mb-3">
                            <label class="form-label">Aktuelles Bild</label>
                            <div>
                                <img src="<?= base_url($produkt['bild_url']) ?>" alt="<?= $produkt['name'] ?>"
                                     class="img-thumbnail mb-2" style="max-height: 150px;" id="bildPreview">
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="mb-3">
                            <img id="bildPreview" src="#" alt="Bildvorschau" class="img-thumbnail mb-2 d-none" style="max-height: 150px;">
                        </div>
                    <?php endif; ?>

                    <hr class="my-4">

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= base_url('admin/produkte') ?>" class="btn btn-outline-secondary">Abbrechen</a>
                        <button type="submit" class="btn btn-primary">
                            <?= $isEdit ? 'Änderungen speichern' : 'Produkt erstellen' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Hilfe</h5>
            </div>
            <div class="card-body">
                <p><strong>Produktname:</strong> Aussagekräftiger Name für das Produkt.</p>
                <p><strong>Artikelnummer:</strong> Optionale eindeutige Kennzeichnung.</p>
                <p><strong>Beschreibung:</strong> Detaillierte Informationen zum Produkt.</p>
                <p><strong>Kategorie:</strong> Zuordnung zu einer Produktkategorie.</p>
                <p><strong>Preis:</strong> Verkaufspreis in Euro (inkl. MwSt).</p>
                <p><strong>Bestand:</strong> Verfügbare Menge im Lager.</p>
                <p><strong>Hervorheben:</strong> Produkt wird auf der Startseite angezeigt.</p>
                <p><strong>Aktiv:</strong> Nur aktive Produkte werden im Shop angezeigt.</p>
            </div>
        </div>

        <?php if ($isEdit) : ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Produktinformationen</h5>
                </div>
                <div class="card-body">
                    <p><strong>Produkt-ID:</strong> <?= $produkt['id'] ?></p>
                    <p><strong>Erstellt am:</strong> <?= date('d.m.Y H:i', strtotime($produkt['erstellt_am'])) ?> Uhr</p>
                    <p><strong>Letzte Aktualisierung:</strong> <?= date('d.m.Y H:i', strtotime($produkt['aktualisiert_am'])) ?> Uhr</p>
                    <div class="mt-3">
                        <a href="<?= base_url('produkte/detail/' . $produkt['id']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-external-link-alt"></i> Im Shop anzeigen
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bildvorschau für Upload
        const bildInput = document.getElementById('bild');
        const bildPreview = document.getElementById('bildPreview');

        if (bildInput && bildPreview) {
            bildInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        bildPreview.src = e.target.result;
                        bildPreview.classList.remove('d-none');
                    }

                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    });
</script>