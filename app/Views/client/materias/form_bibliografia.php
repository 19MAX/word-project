<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('title') ?>
Formulario de objetivos
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <?= isset($bibliografia) ? 'Editar' : 'Nueva' ?> Referencia Bibliográfica para
                <?= esc($materia['nombre']) ?>
            </h6>
        </div>
        <div class="card-body">
            <form
                action="<?= isset($bibliografia) ? site_url("materias/actualizar-bibliografia/{$bibliografia['materia_id']}/{$bibliografia['bibliografia_id']}") : site_url("materias/guardar-bibliografia/{$materia['materia_id']}") ?>"
                method="post">
                <?= csrf_field() ?>

                <div class="form-group mb-3">
                    <label for="referencia">Referencia Completa</label>
                    <textarea class="form-control" id="referencia" name="referencia" rows="3"
                        required><?= isset($bibliografia) ? esc($bibliografia['referencia']) : '' ?></textarea>
                </div>

                <div class="form-group text-right">
                    <a href="<?= site_url("materias/bibliografia/{$materia['materia_id']}") ?>"
                        class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <?= isset($bibliografia) ? 'Actualizar' : 'Guardar' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->endSection() ?>