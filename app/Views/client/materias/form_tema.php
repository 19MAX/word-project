<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('title') ?>
Formulario de temas
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <?= isset($tema) ? 'Editar' : 'Nuevo' ?> Tema para <?= esc($unidad['nombre']) ?> - <?= esc($materia['nombre']) ?>
            </h6>
        </div>
        <div class="card-body">
            <form action="<?= isset($tema) ? base_url("materias/actualizar-tema/" . $materia["materia_id"] . "/" . $unidad['unidad_id'] . "/" . $tema['tema_id']) : base_url("materias/guardar-tema/{$materia['materia_id']}/{$unidad['unidad_id']}") ?>" method="post">
                <?= csrf_field() ?>

                <div class="row">
                    <div class="form-group mb-3 col-md-6">
                        <label for="numero_tema">Número de Tema</label>
                        <input type="number" class="form-control" id="numero_tema" name="numero_tema"
                            value="<?= isset($tema) ? $tema['numero_tema'] : ($ultimo_numero + 1) ?>" required
                            min="1">
                    </div>

                    <div class="form-group mb-3 col-md-6">
                        <label for="nombre">Nombre del Tema</label>
                        <input type="text" class="form-control" id="nombre" name="nombre"
                            value="<?= isset($tema) ? esc($tema['nombre']) : '' ?>" required>
                    </div>

                </div>

                <div class="form-group text-right">
                    <a href="<?= base_url("materias/unidades/{$materia['materia_id']}") ?>"
                        class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <?= isset($tema) ? 'Actualizar' : 'Guardar' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->endSection() ?>