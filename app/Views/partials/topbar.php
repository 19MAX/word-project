<nav class="navbar navbar-expand-lg fixed-top" id="topbar">
    <div class="container-fluid">
        <button id="sidebar-toggle" class="btn btn-sm">
            <i class="fas fa-bars"></i>
        </button>
        <a class="navbar-brand ms-2" href="#"><?= $appTitle ?? 'ASISTENCIAS' ?></a>
        <div class="d-flex ms-auto">
            <div class="form-check form-switch me-3 mt-2">
                <input class="form-check-input" type="checkbox" id="darkModeToggle">
                <label class="form-check-label" for="darkModeToggle">
                    <i id="themeIcon" class="fas fa-moon"></i>
                </label>
            </div>
            <div class="dropdown">
                <button class="btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="fas fa-user-circle"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown" id="userMenu">
                    <!-- <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Perfil</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Configuración</a></li> -->
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?=base_url("logout")?>"><i class="text-danger fas fa-sign-out-alt me-2"></i>Cerrar sesión</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>