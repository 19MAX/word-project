<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('title') ?>
Unidades de <?= esc($materia['nombre']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">Unidades de <?= esc($materia['nombre']) ?></h5>
            <div>
                <a href="<?= base_url('materias') ?>" class="btn btn-secondary btn-sm me-2">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="<?= base_url("materias/nueva-unidad/{$materia['materia_id']}") ?>"
                    class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nueva Unidad
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($unidades)): ?>
                <div class="alert alert-info">No hay unidades registradas para esta materia.</div>
            <?php else: ?>
                <div class="accordion" id="accordionUnidades">
                    <?php foreach ($unidades as $index => $unidad): ?>
                        <div class="accordion-item mb-2">
                            <h2 class="accordion-header" id="heading<?= $unidad['unidad_id'] ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse<?= $unidad['unidad_id'] ?>" aria-expanded="false"
                                    aria-controls="collapse<?= $unidad['unidad_id'] ?>">
                                    Unidad <?= $unidad['numero_unidad'] ?>: <?= esc($unidad['nombre']) ?>
                                </button>
                            </h2>
                            <div id="collapse<?= $unidad['unidad_id'] ?>" class="accordion-collapse collapse"
                                aria-labelledby="heading<?= $unidad['unidad_id'] ?>" data-bs-parent="#accordionUnidades">
                                <div class="accordion-body">
                                    <p><strong>Objetivo:</strong> <?= esc($unidad['objetivo']) ?></p>

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">Temas</h6>
                                        <a href="<?= base_url("materias/nuevo-tema/" . $materia["materia_id"] . "/" . $unidad['unidad_id']) ?>"
                                            class="btn btn-sm btn-success">
                                            <i class="fas fa-plus"></i> Tema
                                        </a>
                                    </div>

                                    <?php if (empty($unidad['temas'])): ?>
                                        <div class="alert alert-warning mt-2">No hay temas en esta unidad.</div>
                                    <?php else: ?>
                                        <ul class="list-group">
                                            <?php foreach ($unidad['temas'] as $tema): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong><?= $tema['numero_tema'] ?>.</strong> <?= esc($tema['nombre']) ?>
                                                    </div>
                                                    <div>
                                                        <a href="<?= base_url("materias/editar-tema/" . $materia["materia_id"] . "/" . $unidad['unidad_id']. "/" .$tema['tema_id'])  ?>"
                                                            class="btn btn-sm btn-outline-primary me-1">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="#"
                                                            class="btn btn-sm btn-outline-danger btn-eliminar-tema"
                                                            data-url="<?= base_url("materias/eliminar-tema/" . $materia["materia_id"] . "/" . $unidad['unidad_id']. "/" .$tema['tema_id']) ?>"
                                                            >
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>

                                    <div class="mt-3">
                                        <a href="<?= base_url("materias/editar-unidad/" . $materia["materia_id"] . "/" . $unidad['unidad_id']) ?>"
                                            class="btn btn-sm btn-primary me-2">
                                            <i class="fas fa-edit"></i> Editar Unidad
                                        </a>

                                        <a href="#" class="btn btn-sm btn-danger btn-eliminar"
                                            data-url="<?= base_url("materias/eliminar-unidad/{$materia['materia_id']}/{$unidad['unidad_id']}") ?>">
                                            <i class="fas fa-trash"></i> Eliminar Unidad
                                        </a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // No necesitas jQuery, pero si lo estás usando aún, esto puede quedarse
    document.addEventListener('DOMContentLoaded', function () {
        const firstCollapse = document.querySelector('.accordion .accordion-collapse');
        if (firstCollapse) {
            const bsCollapse = new bootstrap.Collapse(firstCollapse, {
                toggle: true
            });
        }
    });

    $('.btn-eliminar').on('click', function (e) {
        e.preventDefault();
        const url = $(this).data('url');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción eliminará el objetivo de la materia.",
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
    $('.btn-eliminar-tema').on('click', function (e) {
        e.preventDefault();
        const url = $(this).data('url');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción eliminará el objetivo de la materia.",
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
</script>
<?= $this->endSection() ?>