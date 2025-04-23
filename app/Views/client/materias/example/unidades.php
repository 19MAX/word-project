<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('title') ?>
Unidades
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Unidades de <?= esc($materia['nombre']) ?></h6>
            <a href="<?= site_url("materias/nueva-unidad/{$materia['materia_id']}") ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nueva Unidad
            </a>
        </div>
        <div class="card-body">
            <?php if (empty($unidades)): ?>
                <div class="alert alert-info">No hay unidades registradas para esta materia.</div>
            <?php else: ?>
                <div class="accordion" id="unidadesAccordion">
                    <?php foreach ($unidades as $unidad): ?>
                        <div class="card">
                            <div class="card-header" id="heading<?= $unidad['unidad_id'] ?>">
                                <h2 class="mb-0 d-flex justify-content-between align-items-center">
                                    <button class="btn btn-link" type="button" data-toggle="collapse"
                                        data-target="#collapse<?= $unidad['unidad_id'] ?>" aria-expanded="true"
                                        aria-controls="collapse<?= $unidad['unidad_id'] ?>">
                                        Unidad <?= $unidad['numero_unidad'] ?>: <?= esc($unidad['nombre']) ?>
                                    </button>
                                    <div>
                                        <a href="<?= site_url("materias/editar-unidad/{$unidad['unidad_id']}") ?>"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= site_url("materias/eliminar-unidad/{$unidad['unidad_id']}") ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('¿Eliminar esta unidad y todos sus temas?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </h2>
                            </div>

                            <div id="collapse<?= $unidad['unidad_id'] ?>" class="collapse"
                                aria-labelledby="heading<?= $unidad['unidad_id'] ?>" data-parent="#unidadesAccordion">
                                <div class="card-body">
                                    <h5>Objetivo de la Unidad:</h5>
                                    <p><?= esc($unidad['objetivo']) ?></p>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5>Temas:</h5>
                                        <a href="<?= site_url("materias/nuevo-tema/{$unidad['unidad_id']}") ?>"
                                            class="btn btn-sm btn-success">
                                            <i class="fas fa-plus"></i> Nuevo Tema
                                        </a>
                                    </div>

                                    <?php if (empty($unidad['temas'])): ?>
                                        <div class="alert alert-warning">No hay temas en esta unidad.</div>
                                    <?php else: ?>
                                        <ul class="list-group">
                                            <?php foreach ($unidad['temas'] as $tema): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>Tema <?= $tema['numero_tema'] ?>:</strong> <?= esc($tema['nombre']) ?>
                                                    </div>
                                                    <div>
                                                        <a href="<?= site_url("materias/editar-tema/{$tema['tema_id']}") ?>"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="<?= site_url("materias/eliminar-tema/{$tema['tema_id']}") ?>"
                                                            class="btn btn-sm btn-danger"
                                                            onclick="return confirm('¿Eliminar este tema?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="unidadesTable" class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Unidad</th>
                            <th>Objetivo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($unidades as $unidad): ?>
                            <tr>
                                <td><?= $unidad['numero_unidad'] ?></td>
                                <td><?= esc($unidad['nombre']) ?></td>
                                <td><?= esc($unidad['objetivo']) ?></td>
                                <td>

                                    <a href="<?= site_url("materias/editar-unidad/{$unidad['unidad_id']}") ?>"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= site_url("materias/eliminar-unidad/{$unidad['unidad_id']}") ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('¿Eliminar esta unidad y todos sus temas?')">
                                        <i class="fas fa-trash"></i>
                                    </a>


                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <a href="<?= site_url('materias') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a Materias
                </a>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>

<script>
    $(document).ready(function () {
        $('#unidadesTable').DataTable({
            language: {
                url: '<?= base_url("assets/js/spanishDatatables.json") ?>'
            }
        });

        $('.btn-eliminar').on('click', function (e) {
            e.preventDefault();
            const url = $(this).data('url');

            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción eliminará el unidad de la materia.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>