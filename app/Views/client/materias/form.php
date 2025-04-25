<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('title') ?>
Formulario de Documentos
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?= isset($materia) ? 'Editar' : 'Nueva' ?> Documento</h6>
        </div>
        <div class="card-body">
            <form
                action="<?= isset($materia) ? base_url("materias/actualizar/{$materia['materia_id']}") : base_url('materias/guardar') ?>"
                method="post">
                <?= csrf_field() ?>

                <div class="row">

                    <div class="col form-group mb-3">
                        <label for="nombre">Nombre de la Materia</label>
                        <input type="text" class="form-control" id="nombre" name="nombre"
                            value="<?= isset($materia) ? esc($materia['nombre']) : '' ?>" required>
                    </div>

                    <div class="col form-group mb-3">
                        <label for="ciclo">Ciclo</label>
                        <input type="text" class="form-control" id="ciclo" name="ciclo"
                            value="<?= isset($materia) ? esc($materia['ciclo']) : '' ?>">
                    </div>

                </div>
                <div class="form-group mb-3">
                    <label for="descripcion">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion"
                        rows="5"><?= isset($materia) ? esc($materia['descripcion']) : '' ?></textarea>
                </div>

                <div class="form-group text-right">
                    <a href="<?= base_url('materias') ?>" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <?= isset($materia) ? 'Actualizar' : 'Guardar' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
