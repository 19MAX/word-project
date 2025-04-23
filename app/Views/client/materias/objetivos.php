<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('title') ?>
Objetivos
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Objetivos de <?= esc($materia['nombre']) ?></h6>
            <a href="<?= base_url("materias/nuevo-objetivo/{$materia['materia_id']}") ?>"
                class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nuevo Objetivo
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="objetivosTable" class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Objetivo</th>
                            <th>Resultado Esperado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($objetivos as $objetivo): ?>
                            <tr>
                                <td><?= $objetivo['numero_objetivo'] ?></td>
                                <td><?= esc($objetivo['descripcion']) ?></td>
                                <td><?= !empty($objetivo['resultado']) ? esc($objetivo['resultado']) : 'Sin resultado definido' ?>
                                </td>
                                <td>
                                    <a href="<?= base_url("materias/editar-objetivo/" . $materia["materia_id"] . "/" . $objetivo['objetivo_id']) ?>"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-danger btn-eliminar"
                                        data-url="<?= base_url("materias/eliminar-objetivo/{$materia['materia_id']}/{$objetivo['objetivo_id']}") ?>">
                                        <i class="fas fa-trash"></i>
                                    </a>


                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <a href="<?= base_url('materias') ?>" class="btn btn-secondary">
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
        $('#objetivosTable').DataTable({
            language: {
                url: '<?= base_url("assets/js/spanishDatatables.json") ?>'
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
    });
</script>
<?= $this->endSection() ?>