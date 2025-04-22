<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('title') ?>
Panel de Control - Mi Aplicación
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2 class="mb-4">Panel de control</h2>
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Lista de Usuarios</h5>

        <div class="row mb-2">
            <div id="tableButtons" class="col-12 col-md-6"></div>
            <div id="tableSearchContainer" class="col-12 col-md-6"></div>
        </div>

        <div class="table-responsive">
            <table id="miOtraTabla" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= esc($user['name']) ?></td>
                                <td><?= esc($user['email']) ?></td>
                                <td><?= esc(ucfirst($user['role'])) ?></td>
                                <td>
                                    <!-- Botón Editar -->
                                    <button class="btn btn-warning btn-sm me-1 mb-1 btn-edit-user" data-id="<?= $user['id'] ?>"
                                        data-name="<?= esc($user['name']) ?>" data-email="<?= esc($user['email']) ?>"
                                        title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <!-- Botón Eliminar -->
                                    <button class="btn btn-danger btn-sm me-1 mb-1 btn-delete-user" data-id="<?= $user['id'] ?>"
                                        data-name="<?= esc($user['name']) ?>" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <button class="btn btn-secondary btn-sm me-1 mb-1 btn-reset-password" data-id="<?= $user['id'] ?>"
                                        data-name="<?= esc($user['name']) ?>" title="Restablecer Contraseña">
                                        <i class="fas fa-key"></i>
                                    </button>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No hay usuarios registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
            <div id="tableInfoContainer"></div>
            <div id="tablePaginationContainer" class="pagination-right"></div>
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
                        <input type="password" class="form-control" id="Pass" name="password" required
                            maxlength="255">
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
        initializeUserTable('#miOtraTabla', [
            {
                text: '<i class="fa-solid fa-plus"></i> Nuevo',
                className: 'btn btn-success',
                action: function () {
                    $('#exampleModal').modal('show');
                }
            }
        ], ['excel', 'pdf', 'colvis']);

        // Mostrar modales según la última acción y errores
        <?php if (session()->has('last_action') && session()->has('flashValidation')): ?>
            <?php
            $last_action = session('last_action');
            $errors = session('flashValidation');
            $last_data = session('last_data') ?? [];
            ?>

            switch ('<?= $last_action ?>') {
                case 'create':
                    $('#exampleModal').modal('show');
                    // Rellenar campos
                    $('#name').val('<?= esc($last_data['name'] ?? '') ?>');
                    $('#email').val('<?= esc($last_data['email'] ?? '') ?>');
                    // Mostrar errores
                    <?php if (isset($errors['name'])): ?>
                        $('#name').addClass('is-invalid');
                        $('#name').after('<div class="alert alert-danger"><?= esc($errors['name']) ?></div>');
                    <?php endif; ?>
                    <?php if (isset($errors['email'])): ?>
                        $('#email').addClass('is-invalid');
                        $('#email').after('<div class="alert alert-danger"><?= esc($errors['email']) ?></div>');
                    <?php endif; ?>
                    <?php if (isset($errors['password'])): ?>
                        $('#Pass').addClass('is-invalid');
                        $('#Pass').after('<div class="alert alert-danger"><?= esc($errors['password']) ?></div>');
                    <?php endif; ?>
                    break;

                case 'update':
                    $('#editUserModal').modal('show');
                    // Rellenar campos
                    $('#editUserId').val('<?= esc($last_data['id'] ?? '') ?>');
                    $('#editUserName').val('<?= esc($last_data['name'] ?? '') ?>');
                    $('#editUserEmail').val('<?= esc($last_data['email'] ?? '') ?>');
                    // Mostrar errores
                    <?php if (isset($errors['name'])): ?>
                        $('#editUserName').addClass('is-invalid');
                        $('#editUserName').after('<div class="alert alert-danger"><?= esc($errors['name']) ?></div>');
                    <?php endif; ?>
                    <?php if (isset($errors['email'])): ?>
                        $('#editUserEmail').addClass('is-invalid');
                        $('#editUserEmail').after('<div class="alert alert-danger"><?= esc($errors['email']) ?></div>');
                    <?php endif; ?>
                    break;

                case 'reset_password':
                    $('#resetPasswordModal').modal('show');
                    // Rellenar campos
                    $('#resetPasswordUserId').val('<?= esc($last_data['id'] ?? '') ?>');
                    // Mostrar errores
                    <?php if (isset($errors['password'])): ?>
                        $('#newPassword').addClass('is-invalid');
                        $('#newPassword').after('<div class="alert alert-danger"><?= esc($errors['password']) ?></div>');
                    <?php endif; ?>
                    <?php if (isset($errors['confirm_password'])): ?>
                        $('#confirmPassword').addClass('is-invalid');
                        $('#confirmPassword').after('<div class="alert alert-danger"><?= esc($errors['confirm_password']) ?></div>');
                    <?php endif; ?>
                    break;
            }
        <?php endif; ?>

        // Limpiar errores al cerrar modales
        $('.modal').on('hidden.bs.modal', function () {
            $(this).find('.is-invalid').removeClass('is-invalid');
            $(this).find('.alert alert-danger').remove();
        });

        // Mostrar modal de edición
        $('.btn-edit-user').on('click', function () {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const email = $(this).data('email');

            $('#editUserId').val(id);
            $('#editUserName').val(name);
            $('#editUserEmail').val(email);

            $('#editUserModal').modal('show');
        });

        // Mostrar modal de eliminación
        $('.btn-delete-user').on('click', function () {
            const id = $(this).data('id');
            const name = $(this).data('name');

            $('#deleteUserId').val(id);
            $('#deleteUserName').text(name);

            $('#deleteUserModal').modal('show');
        });

        // Mostrar modal de recuperación de contraseña
        $('.btn-reset-password').on('click', function () {
            const id = $(this).data('id');
            const name = $(this).data('name');

            $('#resetPasswordUserId').val(id);
            $('#resetPasswordUserName').text(name);

            $('#resetPasswordModal').modal('show');
        });

        // Toggle para mostrar/ocultar contraseña
        $('.toggle-password').on('click', function () {
            const input = $($(this).data('target'));
            const icon = $(this).find('i');

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Validación de contraseñas coincidentes
        $('#resetPasswordForm').on('submit', function (e) {
            const password = $('#newPassword').val().trim();
            const confirmPassword = $('#confirmPassword').val().trim();

            if (password !== confirmPassword) {
                e.preventDefault();

                $('#confirmPassword').addClass('is-invalid');
                $('#confirmPasswordError').show();
            } else {
                $('#confirmPassword').removeClass('is-invalid');
                $('#confirmPasswordError').hide();
            }
        });
    });
</script>
<?= $this->endSection() ?>