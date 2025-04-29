<?php
/**
 * Admin Kategorien Formular
 *
 * @package DrumShop
 */

// Bestimmen, ob es sich um eine Bearbeitung oder ein neues Element handelt
$isEdit = isset($kategorie);
$title = $isEdit ? 'Kategorie bearbeiten' : 'Neue Kategorie';
$submitUrl = $isEdit ? base_url('admin/kategorien/aktualisieren/' . $kategorie['id']) : base_url('admin/kategorien/speichern');
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $title ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('admin/kategorien') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Zurück zur Übersicht
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Kategorie-Informationen</h5>
            </div>
            <div class="card-body">
                <form action="<?= $submitUrl ?>" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Kategoriename *</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= $isEdit ? $kategorie['name'] : old('name') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="beschreibung" class="form-label">Beschreibung</label>
                        <textarea class="form-control" id="beschreibung" name="beschreibung" rows="4"><?= $isEdit ? $kategorie['beschreibung'] : old('beschreibung') ?></textarea>
                        <div class="form-text">Eine kurze Beschreibung der Kategorie.</div>
                    </div>

                    <div class="mb-3">
                        <label for="eltern_id" class="form-label">Elternkategorie</label>
                        <select class="form-select" id="eltern_id" name="eltern_id">
                            <option value="">-- Keine Elternkategorie (Hauptkategorie) --</option>
                            <?php foreach ($kategorien as $kat) : ?>
                                <?php if ($isEdit && $kat['id'] == $kategorie['id']) continue; // Verhindere Selbstreferenz ?>
                                <option value="<?= $kat['id'] ?>" <?= ($isEdit && $kategorie['eltern_id'] == $kat['id']) || old('eltern_id') == $kat['id'] ? 'selected' : '' ?>>
                                    <?= $kat['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Wählen Sie eine übergeordnete Kategorie aus, falls diese Kategorie eine Unterkategorie sein soll.</div>
                    </div>

                    <div class="mb-3">
                        <label for="bild" class="form-label">Kategoriebild</label>
                        <input type="file" class="form-control image-upload" id="bild" name="bild" accept="image/*" data-preview="bildPreview">
                        <div class="form-text">Empfohlene Größe: 800x400 Pixel, max. 2 MB</div>
                    </div>

                    <div class="mb-3">
                        <?php if ($isEdit && !empty($kategorie['bild_url'])) : ?>
                            <label>Aktuelles Bild:</label>
                            <div class="mt-2">
                                <img src="<?= base_url($kategorie['bild_url']) ?>" id="bildPreview" class="img-thumbnail" style="max-width: 200px;" alt="Kategoriebild">
                            </div>
                        <?php else : ?>
                            <img id="bildPreview" class="img-thumbnail d-none" style="max-width: 200px;" alt="Bildvorschau">
                        <?php endif; ?>
                    </div>

                    <hr class="my-4">

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= base_url('admin/kategorien') ?>" class="btn btn-outline-secondary">Abbrechen</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?= $isEdit ? 'Änderungen speichern' : 'Kategorie erstellen' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Hinweise</h5>
            </div>
            <div class="card-body">
                <h6>Kategoriestruktur</h6>
                <p>Kategorien können hierarchisch angeordnet werden. Eine Kategorie kann entweder eine Hauptkategorie sein oder eine Unterkategorie einer anderen Kategorie.</p>

                <h6>Bilder</h6>
                <p>Kategorie-Bilder werden im Shop angezeigt und helfen Kunden, die Kategorien visuell zu identifizieren.</p>

                <h6>Zuweisung zu Produkten</h6>
                <p>Nach dem Erstellen der Kategorie können Sie Produkte zuweisen, indem Sie die Produkte bearbeiten und die entsprechende Kategorie auswählen.</p>
            </div>
        </div>

        <?php if ($isEdit) : ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Statistik</h5>
                </div>
                <div class="card-body">
                    <p><strong>Erstellt am:</strong> <?= date('d.m.Y H:i', strtotime($kategorie['erstellt_am'])) ?></p>
                    <p><strong>Zuletzt aktualisiert:</strong> <?= date('d.m.Y H:i', strtotime($kategorie['aktualisiert_am'])) ?></p>

                    <?php
                    $db = \Config\Database::connect();
                    $produktCount = $db->table('produkte')
                        ->where('kategorie_id', $kategorie['id'])
                        ->countAllResults();
                    ?>
                    <p><strong>Zugewiesene Produkte:</strong> <?= $produktCount ?></p>

                    <?php
                    $unterkategorienCount = $db->table('kategorien')
                        ->where('eltern_id', $kategorie['id'])
                        ->countAllResults();
                    ?>
                    <p><strong>Unterkategorien:</strong> <?= $unterkategorienCount ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>