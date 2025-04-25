<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('title') ?>
Unidades de <?= esc($materia['nombre']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row" style="min-height: calc(100vh - 180px);">
    <!-- Card izquierda (Lista de unidades) -->
    <div class="col-md-7">
        <div class="card h-100 shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">Unidades de <?= esc($materia['nombre']) ?></h5>
                <div>
                    <a href="<?= base_url('materias') ?>" class="btn btn-secondary btn-sm me-2">
                        <i class="fas fa-arrow-left"></i> Volver
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
                                        </div>

                                        <?php if (empty($unidad['temas'])): ?>
                                            <div class="alert alert-warning mt-2">No hay temas en esta unidad.</div>
                                        <?php else: ?>
                                            <ul class="list-group temas-list" data-unidad-id="<?= $unidad['unidad_id'] ?>">
                                                <?php foreach ($unidad['temas'] as $tema): ?>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center tema-item"
                                                        data-tema-id="<?= $tema['tema_id'] ?>">
                                                        <div>
                                                            <strong><?= $tema['numero_tema'] ?>.</strong> <?= esc($tema['nombre']) ?>
                                                        </div>
                                                        <div>
                                                            <button class="btn btn-sm btn-outline-primary me-1 edit-tema-btn"
                                                                data-tema-id="<?= $tema['tema_id'] ?>"
                                                                data-numero="<?= $tema['numero_tema'] ?>"
                                                                data-nombre="<?= esc($tema['nombre']) ?>">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger btn-eliminar-tema"
                                                                data-url="<?= base_url("materias/eliminar-tema/" . $materia["materia_id"] . "/" . $unidad['unidad_id'] . "/" . $tema['tema_id']) ?>">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>

                                        <!-- Formulario rápido para agregar tema -->
                                        <div class="mt-3 quick-add-tema" data-unidad-id="<?= $unidad['unidad_id'] ?>">
                                            <div class="input-group mb-3">
                                                <input type="number" class="form-control tema-numero" placeholder="Número"
                                                    min="1">
                                                <input type="text" class="form-control tema-nombre"
                                                    placeholder="Nombre del tema">
                                                <button class="btn btn-success agregar-tema-btn" type="button">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <button class="btn btn-sm btn-primary me-2 edit-unidad-btn"
                                                data-unidad-id="<?= $unidad['unidad_id'] ?>"
                                                data-numero="<?= $unidad['numero_unidad'] ?>"
                                                data-nombre="<?= esc($unidad['nombre']) ?>"
                                                data-objetivo="<?= esc($unidad['objetivo']) ?>">
                                                <i class="fas fa-edit"></i> Editar Unidad
                                            </button>

                                            <button class="btn btn-sm btn-danger btn-eliminar-unidad"
                                                data-url="<?= base_url("materias/eliminar-unidad/{$materia['materia_id']}/{$unidad['unidad_id']}") ?>">
                                                <i class="fas fa-trash"></i> Eliminar Unidad
                                            </button>
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

    <!-- Card derecha (Formulario) -->
    <div class="col-md-5">
        <div class="card h-100 shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold" id="form-title">Nueva Unidad</h6>
            </div>
            <div class="card-body">
                <form id="unidadForm" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" id="unidad_id" name="unidad_id" value="">
                    <input type="hidden" id="materia_id" name="materia_id" value="<?= $materia['materia_id'] ?>">

                    <div class="form-group mb-3">
                        <label for="numero_unidad">Número de Unidad</label>
                        <input type="number" class="form-control" id="numero_unidad" name="numero_unidad" required
                            min="1">
                    </div>

                    <div class="form-group mb-3">
                        <label for="nombre">Nombre de la Unidad</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="objetivo">Objetivo</label>
                        <textarea class="form-control" id="objetivo" name="objetivo" rows="3" required></textarea>
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
        const baseUrl = '<?= base_url() ?>';
        const materiaId = '<?= $materia['materia_id'] ?>';
        const csrfToken = '<?= csrf_token() ?>';
        const csrfValue = '<?= csrf_hash() ?>';

        // Inicializar el primer acordeón abierto
        if ($('.accordion-item').length > 0) {
            $('.accordion-item:first-child .accordion-collapse').addClass('show');
            $('.accordion-item:first-child .accordion-button').removeClass('collapsed');
        }

        // Función para calcular el siguiente número de unidad
        function calcularSiguienteNumeroUnidad() {
            const unidades = $('.accordion-item');
            if (unidades.length === 0) return 1;

            const ultimoNumero = parseInt($('.accordion-item:last').find('.accordion-button').text().match(/Unidad (\d+)/)[1]);
            return ultimoNumero + 1;
        }

        // Manejador para el botón de editar unidad
        $(document).on('click', '.edit-unidad-btn', function () {
            const unidadId = $(this).data('unidad-id');
            $('#form-title').text('Editar Unidad');
            $('#unidad_id').val(unidadId);
            $('#numero_unidad').val($(this).data('numero'));
            $('#nombre').val($(this).data('nombre'));
            $('#objetivo').val($(this).data('objetivo'));
            $('#submit-btn').text('Actualizar');
        });

        // Manejador para el envío del formulario de unidad
        $('#unidadForm').submit(function (e) {
            e.preventDefault();

            const unidadId = $('#unidad_id').val();
            const url = unidadId
                ? `${baseUrl}materias/actualizar-unidad/${materiaId}/${unidadId}`
                : `${baseUrl}materias/guardar-unidad/${materiaId}`;

            $.ajax({
                url: url,
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        showAlert('success', response.message || (unidadId ? 'Unidad actualizada correctamente' : 'Unidad creada correctamente'));
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

        // Manejador para eliminar unidad (tradicional con redirección)
        $(document).on('click', '.btn-eliminar-unidad', function (e) {
            e.preventDefault();
            const url = $(this).data('url');

            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción eliminará la unidad y todos sus temas.",
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

        // Manejador para eliminar tema (tradicional con redirección)
        $(document).on('click', '.btn-eliminar-tema', function (e) {
            e.preventDefault();
            const url = $(this).data('url');

            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción eliminará el tema permanentemente.",
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

        // Función para calcular y establecer el siguiente número de tema
        function actualizarSiguienteNumeroTema(unidadId) {
            const temas = $(`.temas-list[data-unidad-id="${unidadId}"] .tema-item`);
            let siguienteNumero = 1;

            if (temas.length > 0) {
                const ultimoNumeroText = $(temas.last()).find('strong').text();
                siguienteNumero = parseInt(ultimoNumeroText.replace('.', '')) + 1;
            }

            $(`.quick-add-tema[data-unidad-id="${unidadId}"] .tema-numero`).val(siguienteNumero);
            return siguienteNumero;
        }

        // Actualizar números de tema al abrir un acordeón
        $(document).on('shown.bs.collapse', '.accordion-collapse', function () {
            const unidadId = $(this).attr('id').replace('collapse', '');
            actualizarSiguienteNumeroTema(unidadId);
        });

        // Manejador para agregar tema rápido
        $(document).on('click', '.agregar-tema-btn', function () {
            const unidadId = $(this).closest('.quick-add-tema').data('unidad-id');
            const $numeroInput = $(this).siblings('.tema-numero');
            const $nombreInput = $(this).siblings('.tema-nombre');

            const numero = $numeroInput.val() || actualizarSiguienteNumeroTema(unidadId);
            const nombre = $nombreInput.val();

            if (!nombre) {
                showAlert('error', 'Debes ingresar un nombre para el tema');
                return;
            }

            $.ajax({
                url: `${baseUrl}materias/guardar-tema/${materiaId}/${unidadId}`,
                type: 'POST',
                data: {
                    [csrfToken]: csrfValue,
                    numero_tema: numero,
                    nombre: nombre
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        showAlert('success', 'Tema agregado correctamente', 'top-end');
                        $nombreInput.val(''); // Limpiar el campo de nombre
                        actualizarSiguienteNumeroTema(unidadId); // Actualizar el número para el próximo tema

                        // Opcional: recargar después de un breve retraso
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showAlert('error', response.message || 'Error al agregar el tema');
                    }
                },
                error: function (xhr) {
                    showAlert('error', xhr.responseJSON?.message || 'Error en la solicitud');
                }
            });
        });

        // Manejador para editar tema
        $(document).on('click', '.edit-tema-btn', function () {
            const temaId = $(this).data('tema-id');
            const unidadId = $(this).closest('.temas-list').data('unidad-id');
            const numero = $(this).data('numero');
            const nombre = $(this).data('nombre');

            // Crear formulario de edición rápida
            const temaItem = $(this).closest('.tema-item');
            temaItem.html(`
            <div class="input-group">
                <input type="number" class="form-control edit-tema-numero" value="${numero}" min="1">
                <input type="text" class="form-control edit-tema-nombre" value="${nombre}">
                <button class="btn btn-success guardar-tema-btn" type="button" data-tema-id="${temaId}">
                    <i class="fas fa-check"></i>
                </button>
                <button class="btn btn-secondary cancelar-edicion-tema" type="button">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `);
        });

        // Manejador para guardar tema editado
        $(document).on('click', '.guardar-tema-btn', function () {
            const temaId = $(this).data('tema-id');
            const unidadId = $(this).closest('.temas-list').data('unidad-id');
            const numero = $(this).siblings('.edit-tema-numero').val();
            const nombre = $(this).siblings('.edit-tema-nombre').val();

            if (!nombre) {
                showAlert('error', 'El nombre del tema es obligatorio');
                return;
            }

            $.ajax({
                url: `${baseUrl}materias/actualizar-tema/${materiaId}/${unidadId}/${temaId}`,
                type: 'POST',
                data: {
                    [csrfToken]: csrfValue,
                    numero_tema: numero,
                    nombre: nombre
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        showAlert('success', 'Tema actualizado correctamente', 'top-end');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showAlert('error', response.message || 'Error al actualizar el tema');
                    }
                },
                error: function (xhr) {
                    showAlert('error', xhr.responseJSON?.message || 'Error en la solicitud');
                }
            });
        });

        // Manejador para presionar Enter en los campos de tema
        $(document).on('keypress', '.tema-nombre, .edit-tema-nombre', function (e) {
            if (e.which === 13) { // Tecla Enter
                e.preventDefault();
                $(this).closest('.input-group').find('.agregar-tema-btn, .guardar-tema-btn').click();
            }
        });

        // Manejador para cancelar edición de tema
        $(document).on('click', '.cancelar-edicion-tema', function () {
            window.location.reload();
        });

        // Función para resetear el formulario de unidad
        function resetForm() {
            $('#form-title').text('Nueva Unidad');
            $('#unidad_id').val('');
            $('#unidadForm')[0].reset();
            $('#submit-btn').text('Guardar');
        }

        // Inicializar números de tema para las unidades visibles
        $('.accordion-collapse.show').each(function () {
            const unidadId = $(this).attr('id').replace('collapse', '');
            actualizarSiguienteNumeroTema(unidadId);
        });

        // Mostrar el siguiente número de unidad al cargar la página
        $('#numero_unidad').val(calcularSiguienteNumeroUnidad());
    });
</script>
<?= $this->endSection() ?>