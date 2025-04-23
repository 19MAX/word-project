<?= $this->include('partials/vendor-scripts') ?>
<script>
    // Elementos DOM
    const body = document.body;
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const overlay = document.getElementById('overlay');
    const topbar = document.getElementById('topbar');
    const userMenu = document.getElementById('userMenu');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const darkModeToggle = document.getElementById('darkModeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const submenus = document.querySelectorAll('.has-submenu');

    // Verificar si es móvil
    const isMobile = () => window.innerWidth < 768;

    // Manejar submenús
    submenus.forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            this.classList.toggle('active');
            const submenu = this.nextElementSibling;
            submenu.classList.toggle('show');
        });
    });

    // Cargar preferencias guardadas
    function loadPreferences() {
        // Verificar preferencia de tema
        if (localStorage.getItem('darkMode') === 'enabled') {
            enableDarkMode();
            darkModeToggle.checked = true;
        }

        // Verificar estado del sidebar
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            toggleSidebar(false);
        }
    }

    // Alternar el sidebar
    function toggleSidebar(withAnimation = true) {
        if (withAnimation) {
            sidebar.style.transition = 'all 0.3s';
            content.style.transition = 'margin-left 0.3s';
        } else {
            sidebar.style.transition = 'none';
            content.style.transition = 'none';

            // Restaurar las transiciones después
            setTimeout(() => {
                sidebar.style.transition = 'all 0.3s';
                content.style.transition = 'margin-left 0.3s';
            }, 50);
        }

        sidebar.classList.toggle('collapsed');
        content.classList.toggle('expanded');

        const isCollapsed = sidebar.classList.contains('collapsed');
        localStorage.setItem('sidebarCollapsed', isCollapsed);

        // En móvil manejar diferente
        if (isMobile()) {
            sidebar.classList.toggle('mobile-active');
            overlay.classList.toggle('active');
        }
    }

    // Función para activar el modo oscuro
    function enableDarkMode() {
        // Aplicar clase dark-mode a los elementos principales
        body.classList.add('dark-mode');
        sidebar.classList.add('dark-mode');
        topbar.classList.add('dark-mode');
        userMenu.classList.add('dark-mode');

        // Elementos que necesitan modo oscuro
        const darkElements = [
            '.card',
            '.form-control',
            '.input-group-text',
            '.form-select',
            '.form-check-input',
            '.table',
            '.dropdown-menu',
            '.list-group-item',
            '.modal-content',
            '.toast',
            '.nav-tabs',
            '.pagination',
            '.breadcrumb',
            '.alert:not(.alert-success):not(.alert-danger):not(.alert-warning):not(.alert-info)',
            '.bg-light',
            '.bg-white',
            'input:not([type="checkbox"]):not([type="radio"])',
            'textarea',
            'select'
        ];

        // Aplicar a todos los elementos necesarios
        darkElements.forEach(selector => {
            document.querySelectorAll(selector).forEach(item => {
                item.classList.add('dark-mode');
            });
        });

        // Elementos específicos del modal que necesitan estilo oscuro
        const modalElements = [
            '.modal-header',
            '.modal-body',
            '.modal-footer',
            '.modal-title',
            '.modal-backdrop',
            '.btn-close'
        ];

        // Aplicar dark-mode a los elementos del modal
        modalElements.forEach(selector => {
            document.querySelectorAll(selector).forEach(item => {
                item.classList.add('dark-mode');
            });
        });

        // Estilos específicos para modales
        document.querySelectorAll('.modal-content').forEach(modal => {
            modal.style.backgroundColor = '#1e293b';
            modal.style.borderColor = '#334155';
        });

        document.querySelectorAll('.modal-header, .modal-footer').forEach(element => {
            element.style.borderColor = '#334155';
        });

        document.querySelectorAll('.btn-close').forEach(button => {
            button.style.filter = 'invert(1) grayscale(100%) brightness(200%)';
        });

        // Cambiar estilos de tablas
        document.querySelectorAll('.table').forEach(table => {
            table.classList.add('table-dark');
        });

        // Cambiar estilos de inputs específicos
        document.querySelectorAll('input:not([type="checkbox"]):not([type="radio"]), textarea, select').forEach(input => {
            input.style.backgroundColor = '#1e293b';
            input.style.borderColor = '#334155';
            input.style.color = '#f8fafc';
        });

        // Cambiar ícono de modo oscuro/claro
        if (themeIcon) {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        }

        // Guardar preferencia
        localStorage.setItem('darkMode', 'enabled');
    }

    // Función para desactivar el modo oscuro
    function disableDarkMode() {
        // Quitar clase dark-mode de los elementos principales
        body.classList.remove('dark-mode');
        sidebar.classList.remove('dark-mode');
        topbar.classList.remove('dark-mode');
        userMenu.classList.remove('dark-mode');

        // Elementos que necesitan quitar modo oscuro
        const darkElements = [
            '.card',
            '.form-control',
            '.input-group-text',
            '.form-select',
            '.form-check-input',
            '.table',
            '.dropdown-menu',
            '.list-group-item',
            '.modal-content',
            '.toast',
            '.nav-tabs',
            '.pagination',
            '.breadcrumb',
            '.alert',
            '.bg-light',
            '.bg-white',
            'input:not([type="checkbox"]):not([type="radio"])',
            'textarea',
            'select'
        ];

        // Quitar de todos los elementos necesarios
        darkElements.forEach(selector => {
            document.querySelectorAll(selector).forEach(item => {
                item.classList.remove('dark-mode');
            });
        });

        // Elementos específicos del modal que necesitan quitar estilo oscuro
        const modalElements = [
            '.modal-header',
            '.modal-body',
            '.modal-footer',
            '.modal-title',
            '.modal-backdrop',
            '.btn-close'
        ];

        // Quitar dark-mode de los elementos del modal
        modalElements.forEach(selector => {
            document.querySelectorAll(selector).forEach(item => {
                item.classList.remove('dark-mode');
            });
        });

        // Restaurar estilos específicos para modales
        document.querySelectorAll('.modal-content').forEach(modal => {
            modal.style.backgroundColor = '';
            modal.style.borderColor = '';
        });

        document.querySelectorAll('.modal-header, .modal-footer').forEach(element => {
            element.style.borderColor = '';
        });

        document.querySelectorAll('.btn-close').forEach(button => {
            button.style.filter = '';
        });

        // Restaurar estilos de tablas
        document.querySelectorAll('.table').forEach(table => {
            table.classList.remove('table-dark');
        });

        // Restaurar estilos de inputs
        document.querySelectorAll('input:not([type="checkbox"]):not([type="radio"]), textarea, select').forEach(input => {
            input.style.backgroundColor = '';
            input.style.borderColor = '';
            input.style.color = '';
        });

        // Cambiar ícono
        if (themeIcon) {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
        }

        // Guardar preferencia
        localStorage.setItem('darkMode', 'disabled');
    }

    // Cambiar el tema cuando se presiona el interruptor
    if (darkModeToggle) {
        darkModeToggle.addEventListener('change', function () {
            if (this.checked) {
                enableDarkMode();
            } else {
                disableDarkMode();
            }
        });
    }

    // Event listeners
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => toggleSidebar(true));
    }

    if (overlay) {
        overlay.addEventListener('click', () => toggleSidebar(true));
    }

    // Manejar cambios de tamaño de ventana
    window.addEventListener('resize', function () {
        if (isMobile()) {
            if (sidebar && sidebar.classList.contains('mobile-active')) {
                overlay.classList.add('active');
            }
        } else if (overlay) {
            overlay.classList.remove('active');
        }
    });

    // Cargar preferencias al inicio
    document.addEventListener('DOMContentLoaded', function () {
        loadPreferences();
        document.body.classList.add('ready');

        // Observador para nuevos modales que se añadan al DOM
        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                if (document.body.classList.contains('dark-mode')) {
                    mutation.addedNodes.forEach(node => {
                        if (node.nodeType === 1) { // Es un elemento
                            // Verificar si es un modal o contiene un modal
                            const modals = node.classList && node.classList.contains('modal') ?
                                [node] :
                                node.querySelectorAll('.modal');

                            modals.forEach(modal => {
                                // Aplicar estilos oscuros al modal
                                modal.querySelectorAll('.modal-content').forEach(content => {
                                    content.classList.add('dark-mode');
                                    content.style.backgroundColor = '#1e293b';
                                    content.style.borderColor = '#334155';
                                });

                                modal.querySelectorAll('.modal-header, .modal-footer').forEach(element => {
                                    element.classList.add('dark-mode');
                                    element.style.borderColor = '#334155';
                                });

                                modal.querySelectorAll('.modal-body, .modal-title').forEach(element => {
                                    element.classList.add('dark-mode');
                                });

                                modal.querySelectorAll('.btn-close').forEach(button => {
                                    button.classList.add('dark-mode');
                                    button.style.filter = 'invert(1) grayscale(100%) brightness(200%)';
                                });
                            });
                        }
                    });
                }
            });
        });

        // Comenzar a observar cambios en el documento
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });

    // Verificar si hay mensajes de éxito, advertencia o error
    <?php if (session()->has('flashMessages')): ?>
        <?php foreach (session('flashMessages') as $message): ?>
            <?php
            $type = $message[1];
            $msg = $message[0];
            $position = $message[2] ?? 'top-end';
            ?>
            showAlert(<?= json_encode($type) ?>, <?= json_encode($msg) ?>, <?= json_encode($position) ?>);
        <?php endforeach; ?>
    <?php endif; ?>
</script>