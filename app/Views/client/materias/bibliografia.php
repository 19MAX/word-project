<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('title') ?>
Bibliografía de <?= esc($materia['nombre']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row" style="min-height: calc(100vh - 180px);">
    <!-- Card izquierda (Lista de referencias) -->
    <div class="col-md-7">
        <div class="card h-100 shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Bibliografía de <?= esc($materia['nombre']) ?></h6>
                <a href="<?= base_url('materias') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a Materias
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($bibliografias)): ?>
                    <div class="alert alert-info">No hay referencias bibliográficas registradas.</div>
                <?php else: ?>
                    <div class="list-group" id="bibliografia-list">
                        <?php foreach ($bibliografias as $bibliografia): ?>
                            <div class="list-group-item bibliografia-item" data-id="<?= $bibliografia['bibliografia_id'] ?>">
                                <div class="d-flex flex-column">
                                    <div class="mb-2">
                                        <div class="referencia-text" style=" word-wrap: break-word;">
                                            <?= esc($bibliografia['referencia']) ?>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button class="btn btn-sm btn-primary edit-bibliografia-btn mr-2"
                                            data-id="<?= $bibliografia['bibliografia_id'] ?>"
                                            data-referencia="<?= esc($bibliografia['referencia']) ?>"
                                            data-enlace="<?= esc($bibliografia['enlace']) ?>">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <a href="#"
                                            data-url="<?= base_url("materias/eliminar-bibliografia/") . $bibliografia['materia_id'] . "/" . $bibliografia['bibliografia_id'] ?>"
                                            class="btn btn-sm btn-danger btn-eliminar">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="mt-3">
                </div>
            </div>
        </div>
    </div>

    <!-- Card derecha (Formulario) -->
    <div class="col-md-5">
        <div class="card h-100 shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold" id="form-title">Nueva Referencia Bibliográfica</h6>
            </div>
            <div class="card-body">
                <form id="bibliografiaForm" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" id="bibliografia_id" name="bibliografia_id" value="">
                    <input type="hidden" id="materia_id" name="materia_id" value="<?= $materia['materia_id'] ?>">

                    <div class="form-group mb-3">
                        <label for="referencia">Referencia Completa*</label>
                        <textarea class="form-control" id="referencia" name="referencia" rows="8"
                            style="white-space: pre-wrap;" required></textarea>
                        <small class="form-text text-muted">Formato APA recomendado: Autor(es). (Año). Título.
                            Editorial.</small>
                    </div>

                    <!-- <div class="form-group mb-3">
                        <label for="enlace">Enlace (opcional)</label>
                        <input type="url" class="form-control" id="enlace" name="enlace"
                            placeholder="https://ejemplo.com">
                        <small class="form-text text-muted">Enlace a la fuente digital (si aplica)</small>
                    </div>
 -->
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

        // Manejador para el botón de nueva referencia
        $('#nueva-bibliografia-btn').click(function () {
            resetForm();
        });

        // Manejador para el botón de editar
        $(document).on('click', '.edit-bibliografia-btn', function () {
            const id = $(this).data('id');
            $('#form-title').text('Editar Referencia');
            $('#bibliografia_id').val(id);
            $('#referencia').val($(this).data('referencia'));
            $('#enlace').val($(this).data('enlace'));
            $('#submit-btn').text('Actualizar');

            // Ajustar altura del textarea según contenido
            const textarea = $('#referencia');
            textarea.height(textarea[0].scrollHeight);
        });

        // Autoajustar altura del textarea al escribir
        $('#referencia').on('input', function () {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Manejador para el botón de cancelar
        $('#cancel-btn').click(function () {
            resetForm();
        });

        // Manejador para el envío del formulario
        $('#bibliografiaForm').submit(function (e) {
            e.preventDefault();

            const bibliografiaId = $('#bibliografia_id').val();
            const url = bibliografiaId
                ? `${base_url}materias/actualizar-bibliografia/${materiaId}/${bibliografiaId}`
                : `${base_url}materias/guardar-bibliografia/${materiaId}`;

            $.ajax({
                url: url,
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        showAlert('success', response.message || (bibliografiaId ? 'Referencia actualizada correctamente' : 'Referencia creada correctamente'));
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

        // Manejador para eliminar referencia
        $(document).on('click', '.btn-eliminar', function (e) {
            e.preventDefault();
            const url = $(this).data('url');

            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción eliminará la referencia bibliográfica.",
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

        function resetForm() {
            $('#form-title').text('Nueva Referencia Bibliográfica');
            $('#bibliografia_id').val('');
            $('#bibliografiaForm')[0].reset();
            $('#submit-btn').text('Guardar');
            $('#referencia').height('auto'); // Resetear altura del textarea
        }
    });
</script>
<?= $this->endSection() ?>