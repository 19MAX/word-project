<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <?= isset($unidad) ? 'Editar' : 'Nueva' ?> Unidad para <?= esc($materia['nombre']) ?>
            </h6>
        </div>
        <div class="card-body">
            <form action="<?= isset($unidad) ? site_url("materias/actualizar-unidad/{$unidad['unidad_id']}") : site_url("materias/guardar-unidad/{$materia['materia_id']}") ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="form-group">
                    <label for="numero_unidad">Número de Unidad</label>
                    <input type="number" class="form-control" id="numero_unidad" name="numero_unidad" 
                           value="<?= isset($unidad) ? $unidad['numero_unidad'] : ($ultimo_numero + 1) ?>" required min="1">
                </div>
                
                <div class="form-group">
                    <label for="nombre">Nombre de la Unidad</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" 
                           value="<?= isset($unidad) ? esc($unidad['nombre']) : '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="objetivo">Objetivo de la Unidad</label>
                    <textarea class="form-control" id="objetivo" name="objetivo" rows="3" required><?= isset($unidad) ? esc($unidad['objetivo']) : '' ?></textarea>
                </div>
                
                <div class="form-group text-right">
                    <a href="<?= site_url("materias/unidades/{$materia['materia_id']}") ?>" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <?= isset($unidad) ? 'Actualizar' : 'Guardar' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>