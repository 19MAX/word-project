<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('title') ?>
Panel de Control - Mi Aplicación
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row" style="min-height: calc(100vh - 180px);">
    <!-- Card izquierda (Tabla) -->
    <div class="col-md-7">
        <div class="card h-100 shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="materiasTable" class="table table-bordered table-hover table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Materia</th>
                                <th>Descripción</th>
                                <th>Ciclo</th>
                                <th class="text-nowrap">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($materias)): ?>
                                <?php foreach ($materias as $materia): ?>
                                    <tr>
                                        <td><?= esc($materia['materia_id']) ?></td>
                                        <td><?= esc($materia['nombre']) ?></td>
                                        <td>
                                            <span class="truncate-hover"><?= esc($materia['descripcion']) ?></span>
                                        </td>
                                        <td><?= esc($materia['ciclo']) ?></td>
                                        <td class="text-nowrap">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="dropdown dropstart">
                                                    <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        Acciones
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                                        </li>
                                                        <li><a class="dropdown-item"
                                                                href="<?= base_url('materias/objetivos/') . $materia['materia_id'] ?>">
                                                                <i class="fas fa-bullseye"></i> Objetivos</a></li>
                                                        <li><a class="dropdown-item"
                                                                href="<?= base_url('materias/unidades/') . $materia['materia_id'] ?>">
                                                                <i class="fas fa-layer-group"></i> Unidades</a></li>
                                                        <li><a class="dropdown-item"
                                                                href="<?= base_url('materias/bibliografia/') . $materia['materia_id'] ?>">
                                                                <i class="fas fa-book"></i> Bibliografía</a></li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li><a class="dropdown-item text-danger" href="#"
                                                                onclick="confirmarEliminar(<?= $materia['materia_id'] ?>)">
                                                                <i class="fas fa-trash"></i> Eliminar</a></li>
                                                    </ul>
                                                </div>
                                                        <a class="btn btn-sm btn-warning edit-btn" href="#"
                                                                data-id="<?= $materia['materia_id'] ?>">
                                                                <i class="fas fa-edit"></i></a>
                                                <a title="Generar documento"
                                                    href="<?= base_url('materias/generar-word/') . $materia['materia_id'] ?>"
                                                    class="btn btn-sm btn-success">
                                                    <i class="fas fa-file-word"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No hay documentos registrados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Card derecha (Formulario) -->
    <div class="col-md-5">
        <div class="card h-100 shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold" id="form-title">Nuevo Documento</h6>
            </div>
            <div class="card-body">
                <form id="materiaForm" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" id="materia_id" name="materia_id" value="">

                    <div class="row">
                        <div class="col form-group mb-3">
                            <label for="nombre">Nombre de la Materia</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>

                        <div class="col form-group mb-3">
                            <label for="ciclo">Ciclo</label>
                            <input type="text" class="form-control" id="ciclo" name="ciclo">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="descripcion">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="5"></textarea>
                    </div>

                    <div class="form-group text-right">
                        <button type="button" id="cancel-btn" class="btn btn-secondary">Borrar</button>
                        <button type="submit" class="btn btn-primary" id="submit-btn">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>

    $(document).ready(function () {

        // Manejador para el botón de editar
        $(document).on('click', '.edit-btn', function (e) {
            e.preventDefault();
            const materiaId = $(this).data('id');
            cargarMateria(materiaId);
        });

        // Manejador para el botón de cancelar
        $('#cancel-btn').click(function () {
            resetForm();
        });

        // Manejador para el envío del formulario
        $('#materiaForm').submit(function (e) {
            e.preventDefault();

            const materiaId = $('#materia_id').val();
            const url = materiaId ? `${baseUrl}materias/actualizar/${materiaId}` : `${baseUrl}materias/guardar`;
            const method = 'POST';

            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        showAlert('success', response.message || (materiaId ? 'Documento actualizado correctamente' : 'Documento creado correctamente'));
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        // Mostrar errores de validación si existen
                        if (response.errors) {
                            let errorMessages = '';
                            for (const field in response.errors) {
                                errorMessages += `${response.errors[field]}<br>`;
                            }
                            showAlert('error', errorMessages);
                        } else {
                            showAlert('error', response.message || 'Ocurrió un error inesperado');
                        }
                    }
                },
                error: function (xhr) {
                    let errorMessage = 'Error en la solicitud';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showAlert('error', errorMessage);
                }
            });
        });

        // Función para cargar los datos de una materia
        function cargarMateria(materiaId) {
            $.get(`${baseUrl}materias/obtener/${materiaId}`, function (response) {
                if (response.success && response.data) {
                    const materia = response.data;
                    $('#form-title').text('Editar Documento');
                    $('#materia_id').val(materia.materia_id);
                    $('#nombre').val(materia.nombre);
                    $('#ciclo').val(materia.ciclo);
                    $('#descripcion').val(materia.descripcion);
                    $('#submit-btn').text('Actualizar');

                    // Mostrar notificación de éxito
                    showAlert('success', 'Datos cargados correctamente', 'top-end');
                } else {
                    showAlert('error', response.message || 'No se pudieron cargar los datos');
                }
            }).fail(function () {
                showAlert('error', 'Error al cargar los datos');
            });
        }

        // Función para resetear el formulario
        function resetForm() {
            $('#form-title').text('Nuevo Documento');
            $('#materia_id').val('');
            $('#materiaForm')[0].reset();
            $('#submit-btn').text('Guardar');
            showAlert('info', 'Formulario listo para crear un nuevo documento', 'top-end');
        }

        // Variable global para la URL base
        const baseUrl = '<?= base_url() ?>';
    });

    $(document).ready(function () {
        <?php if (!empty($materias)): ?>
            $('#materiasTable').DataTable({
                language: {
                    url: '<?= base_url("assets/js/spanishDatatables.json") ?>'
                }
            });
        <?php else: ?>
            // Si no hay datos, simplemente aplicar estilos básicos o un mensaje
            $('#materiasTable').addClass('table-empty');
        <?php endif; ?>
    });

    function confirmarEliminar(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Todos los contenidos relacionados también se eliminarán.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('materias/eliminar/') ?>" + id;
            }
        });
    }

</script>
<?= $this->endSection() ?>