<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('title') ?>
Formulario de objetivos
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <?= isset($objetivo) ? 'Editar' : 'Nuevo' ?> Objetivo para <?= esc($materia['nombre']) ?>
            </h6>
        </div>
        <div class="card-body">
            <form
                action="<?= isset($objetivo) ? site_url("materias/actualizar-objetivo/" . $materia["materia_id"] . "/" .$objetivo['objetivo_id'])  : site_url("materias/guardar-objetivo/{$materia['materia_id']}") ?>"
                method="post">
                <?= csrf_field() ?>

                <div class="form-group mb-3">
                    <label for="numero_objetivo">Número de Objetivo</label>
                    <input type="number" class="form-control" id="numero_objetivo" name="numero_objetivo"
                        value="<?= isset($objetivo) ? $objetivo['numero_objetivo'] : ($ultimo_numero + 1) ?>" required
                        min="1">
                </div>

                <div class="form-group mb-3">
                    <label for="descripcion">Descripción del Objetivo</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                        required><?= isset($objetivo) ? esc($objetivo['descripcion']) : '' ?></textarea>
                </div>

                <div class="form-group mb-3">
                    <label for="resultado">Resultado Esperado</label>
                    <textarea class="form-control" id="resultado" name="resultado"
                        rows="3"><?= isset($objetivo) && !empty($objetivo['resultado']) ? esc($objetivo['resultado']) : '' ?></textarea>
                </div>

                <div class="form-group text-right">
                    <a href="<?= site_url("materias/objetivos/{$materia['materia_id']}") ?>"
                        class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <?= isset($objetivo) ? 'Actualizar' : 'Guardar' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->endSection() ?>