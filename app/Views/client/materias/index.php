<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('title') ?>
Panel de Control - Mi Aplicación
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2 class="mb-4"><i class="fa-solid fa-book"></i> Mis Documentos</h2>
<div class="card">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold">Documentos </h6>
        <a href="<?= base_url('materias/nueva') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Nuevo Documento
        </a>
    </div>
    <div class="card-body">

        <div class="table-responsive">
            <table id="materiasTable" class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
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
                                <td><?= esc($materia['descripcion']) ?></td>
                                <td><?= esc($materia['ciclo']) ?></td>
                                <td class="text-nowrap">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                Acciones
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                                <!-- <li><a class="dropdown-item"
                                                        href="<?= base_url('materias/') . $materia['materia_id'] ?>">
                                                        <i class="fas fa-eye"></i> Ver</a></li> -->
                                                <li><a class="dropdown-item"
                                                        href="<?= base_url('materias/editar/') . $materia['materia_id'] ?>">
                                                        <i class="fas fa-edit"></i> Editar</a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
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



<!-- Modal  crear-->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url("admin/users/create") ?>" method="POST" id="Create">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="name" name="name" required maxlength="100">

                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" required maxlength="100">
                    </div>
                    <div class="mb-3 position-relative">
                        <label for="Pass" class="form-label">Nueva Contraseña</label>
                        <input type="password" class="form-control" id="Pass" name="password" required maxlength="255">
                        <button type="button" class="btn btn-sm btn-outline-secondary toggle-password"
                            data-target="#Pass" style="position: absolute; top: 35px; right: 10px;">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary" form="Create">Crear Usuario</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editUserForm" method="POST" action="<?= base_url('admin/users/update') ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editUserId">
                    <div class="mb-3">
                        <label for="editUserName" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="editUserName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editUserEmail" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="editUserEmail" name="email" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Eliminar Usuario -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="deleteUserForm" method="POST" action="<?= base_url('admin/users/delete') ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar al usuario <strong id="deleteUserName"></strong>?</p>
                    <input type="hidden" name="id" id="deleteUserId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Restablecer Contraseña -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="resetPasswordForm" method="POST" action="<?= base_url('admin/users/reset-password') ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Restablecer Contraseña</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="resetPasswordUserId">
                    <p>Restablecer contraseña para: <strong id="resetPasswordUserName"></strong></p>
                    <!-- Nueva Contraseña -->
                    <div class="mb-3 position-relative">
                        <label for="newPassword" class="form-label">Nueva Contraseña</label>
                        <input type="password" class="form-control" id="newPassword" name="password" required
                            maxlength="255">
                        <button type="button" class="btn btn-sm btn-outline-secondary toggle-password"
                            data-target="#newPassword" style="position: absolute; top: 35px; right: 10px;">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div class="mb-3 position-relative">
                        <label for="confirmPassword" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirm_password"
                            required maxlength="255">
                        <div class="alert alert-danger" id="confirmPasswordError">Las contraseñas no coinciden.</div>
                        <button type="button" class="btn btn-sm btn-outline-secondary toggle-password"
                            data-target="#confirmPassword" style="position: absolute; top: 35px; right: 10px;">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
    $(document).ready(function () {
        <?php if (!empty($materias)): ?>
            $('#materiasTable').DataTable({
                language: {
                    url: '<?=base_url("assets/js/spanishDatatables.json")?>'
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