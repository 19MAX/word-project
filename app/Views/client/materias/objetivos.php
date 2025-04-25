<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('title') ?>
Objetivos de <?= esc($materia['nombre']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row" style="min-height: calc(100vh - 180px);">
    <!-- Card izquierda (Tabla) -->
    <div class="col-md-7">
        <div class="card h-100 shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Objetivos de <?= esc($materia['nombre']) ?></h6>

                <a href="<?= base_url('materias') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a Materias
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="objetivosTable" class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Descripción</th>
                                <th>Resultado Esperado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($objetivos as $objetivo): ?>
                                <tr>
                                    <td><?= $objetivo['numero_objetivo'] ?></td>
                                    <td>
                                        <span class="truncate-hover"><?= esc($objetivo['descripcion']) ?></span>
                                    </td>
                                    <td><?= !empty($objetivo['resultado']) ? esc($objetivo['resultado']) : 'Sin resultado definido' ?>
                                    </td>
                                    <td class="text-nowrap">
                                        <div class="d-flex align-items-center gap-2">
                                            <button class="btn btn-sm btn-primary edit-btn"
                                                data-id="<?= $objetivo['objetivo_id'] ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="#" class="btn btn-sm btn-danger btn-eliminar"
                                                data-url="<?= base_url("materias/eliminar-objetivo/{$materia['materia_id']}/{$objetivo['objetivo_id']}") ?>">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
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
                <h6 class="m-0 font-weight-bold" id="form-title">Nuevo Objetivo</h6>
            </div>
            <div class="card-body">
                <form id="objetivoForm" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" id="objetivo_id" name="objetivo_id" value="">
                    <input type="hidden" id="materia_id" name="materia_id" value="<?= $materia['materia_id'] ?>">

                    <div class="form-group mb-3">
                        <label for="numero_objetivo">Número de Objetivo</label>
                        <input type="number" class="form-control" id="numero_objetivo" name="numero_objetivo" required
                            min="1">
                    </div>

                    <div class="form-group mb-3">
                        <label for="descripcion">Descripción del Objetivo</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="resultado">Resultado Esperado</label>
                        <textarea class="form-control" id="resultado" name="resultado" rows="3"></textarea>
                    </div>

                    <div class="form-group text-right">
                        <button type="button" id="cancel-btn" class="btn btn-secondary">Cancelar</button>
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
        // Configuración de DataTables
        $('#objetivosTable').DataTable({
            responsive: true,
            language: {
                url: '<?= base_url("assets/js/spanishDatatables.json") ?>'
            }
        });

        // Función para obtener el siguiente número de objetivo
        function cargarSiguienteNumero() {
            $.get(`${base_url}materias/siguiente-numero-objetivo/${$('#materia_id').val()}`, function (response) {
                if (response.success) {
                    $('#numero_objetivo').val(response.siguiente_numero);
                }
            });
        }

        // Llamar la función al cargar la página
        cargarSiguienteNumero();

        // Manejador para el botón de editar
        $(document).on('click', '.edit-btn', function (e) {
            e.preventDefault();
            const objetivoId = $(this).data('id');
            cargarObjetivo(objetivoId);
        });

        // Manejador para el botón de cancelar
        $('#cancel-btn').click(function () {
            resetForm();
        });

        // Manejador para el envío del formulario
        $('#objetivoForm').submit(function (e) {
            e.preventDefault();

            const objetivoId = $('#objetivo_id').val();
            const materiaId = $('#materia_id').val();
            const url = objetivoId
                ? `${base_url}materias/actualizar-objetivo/${materiaId}/${objetivoId}`
                : `${base_url}materias/guardar-objetivo/${materiaId}`;

            $.ajax({
                url: url,
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        showAlert('success', response.message || (objetivoId ? 'Objetivo actualizado correctamente' : 'Objetivo creado correctamente'));
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
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

        // Manejador para el botón de eliminar
        $(document).on('click', '.btn-eliminar', function (e) {
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

        // Función para cargar los datos de un objetivo
        function cargarObjetivo(objetivoId) {
            $.get(`${base_url}materias/obtener-objetivo/${objetivoId}`, function (response) {
                if (response.success && response.data) {
                    const objetivo = response.data;
                    $('#form-title').text('Editar Objetivo');
                    $('#objetivo_id').val(objetivo.objetivo_id);
                    $('#numero_objetivo').val(objetivo.numero_objetivo);
                    $('#descripcion').val(objetivo.descripcion);
                    $('#resultado').val(objetivo.resultado);
                    $('#submit-btn').text('Actualizar');

                    showAlert('success', 'Datos del objetivo cargados correctamente', 'top-end');
                } else {
                    showAlert('error', response.message || 'No se pudieron cargar los datos del objetivo');
                }
            }).fail(function () {
                showAlert('error', 'Error al cargar los datos del objetivo');
            });
        }

        // Función para resetear el formulario
        function resetForm() {
            $('#form-title').text('Nuevo Objetivo');
            $('#objetivo_id').val('');
            $('#objetivoForm')[0].reset();
            $('#submit-btn').text('Guardar');
            showAlert('info', 'Formulario listo para crear un nuevo objetivo', 'top-end');
        }

        // Variables globales
        const csrfToken = '<?= csrf_token() ?>';
        const csrfValue = '<?= csrf_hash() ?>';
    });
</script>
<?= $this->endSection() ?>