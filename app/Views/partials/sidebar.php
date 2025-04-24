<div class="sidebar" id="sidebar">
    <ul class="nav flex-column">
        <?php
        // Obtenemos el rol del usuario usando la sintaxis auxiliar
        $role = session("role");
        ?>

        <!-- Inicio - Visible para todos -->
        <li class="nav-item">
            <a class="nav-link <?= current_url() == base_url('dashboard') ? 'active' : '' ?>"
                href="<?= base_url('dashboard') ?>" data-title="Inicio" title="Inicio">
                <i class="fas fa-home"></i>
                <span class="nav-text">Inicio</span>
            </a>
        </li>

        <!-- Sección de Usuarios - Solo visible para administradores -->
        <?php if ($role === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link has-submenu " href="#" data-title="Usuarios" title="Usuarios">
                    <i class="fas fa-users"></i>
                    <span class="nav-text">Usuarios</span>
                </a>
                <ul class="submenu <?= current_url() == base_url('admin/users') ? 'show' : '' ?>">
                    <li class="nav-item">
                        <a class="nav-link <?= current_url() == base_url('admin/users') ? 'active' : '' ?>" href="<?= site_url('admin/users') ?>" data-title="Todos los usuarios"
                            title="Todos los usuarios">
                            <i class="fa-solid fa-users-rays"></i>
                            <span class="nav-text">Todos los usuarios</span>
                        </a>
                    </li>
                </ul>
            </li>


        <?php elseif ($role === 'user'): ?>
            <!-- Registrar Asistencia - Visible para todos -->
            <li class="nav-item">
                <a class="nav-link <?= current_url() == base_url('materias') ? 'active' : '' ?>"  href="<?= site_url('materias') ?>" data-title="Registrar Asistencia"
                    title="Registrar Asistencia">
                    <i class="fa-solid fa-book"></i>
                    <span class="nav-text">Todos los documentos</span>
                </a>
            </li>

        <?php endif; ?>

        <hr>

        <!-- Cerrar Sesión - Visible para todos -->
        <li class="nav-item">
            <a class="nav-link border border-danger" href="<?= site_url('logout') ?>" data-title="Cerrar Sesión"
                title="Cerrar Sesión">
                <i class="fa-solid fa-arrow-right-from-bracket text-danger"></i>
                <span class="nav-text">Cerrar Sesión</span>
            </a>
        </li>
    </ul>
</div>