// public/js/datatables-init.js

function initializeUserTable(selector = '#userTable', extraButtons = [], defaultButtonTypes = ['pageLength', 'copy', 'excel', 'pdf', 'colvis']) {
    // Inicialización de DataTables con configuración personalizada
    const allDefaultButtons = {
        pageLength: {
            extend: 'pageLength',
            text: function (dt) {
                const len = dt.page.len();
                return `Mostrar ${len} Registros`;
            },

            className: 'btn btn-secondary'
        },
        copy: {
            extend: 'copy',
            text: '<i class="fa-solid fa-copy"></i>',
            className: 'btn-primary'
        },
        excel: {
            extend: 'excel',
            text: '<i class="fa-solid fa-file-excel"></i>',
            className: 'btn btn-success'
        },
        pdf: {
            extend: 'pdf',
            text: '<i class="fa-solid fa-file-pdf"></i>',
            className: 'btn btn-danger'
        },
        colvis: {
            extend: 'colvis',
            text: '<i class="fa-solid fa-eye"></i>',
            className: 'btn btn-info'
        }
    };

    // Asegurarnos de que el botón `pageLength` siempre esté presente
    let defaultButtons = defaultButtonTypes.map(type => allDefaultButtons[type]).filter(Boolean);
    if (!defaultButtons.find(btn => btn.extend === 'pageLength')) {
        defaultButtons.unshift(allDefaultButtons.pageLength); // Colocamos `pageLength` como primer botón
    }

    const userTable = $(selector).DataTable({
        paging: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        // Usar solo "t" para que solo muestre la tabla sin controles
        dom: 't',
        buttons: [...defaultButtons, ...extraButtons],
        language: {
            "sEmptyTable": "No hay datos disponibles",
            "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 a 0 de 0 registros",
            "sInfoFiltered": "(filtrado de _MAX_ registros en total)",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sLoadingRecords": "Cargando...",
            "sProcessing": "Procesando...",
            "sSearch": "Buscar:",
            "sZeroRecords": "No se encontraron resultados",
            "oPaginate": {
                "sFirst": "Primero",
                "sPrevious": "Anterior",
                "sNext": "Siguiente",
                "sLast": "Último"
            }
        }
    });

    // Actualizamos el texto del botón `pageLength` cuando cambie la cantidad de registros por página
    userTable.on('length.dt', function (e, settings, len) {
        const buttonsContainer = $(selector).closest('.dataTables_wrapper').find('.dt-buttons');
        const pageLengthBtn = buttonsContainer.find('button.buttons-page-length');
        if (pageLengthBtn.length) {
            pageLengthBtn.html(`Mostrar ${len} Registros`);
        }
    });


    // Crear y colocar el buscador en su contenedor
    $('#tableSearchContainer').append(
        $('<div class="dataTables_filter"><label class="d-flex align-items-center"><span class="me-2">Buscar:</span><input type="text" class="form-control form-control-sm" placeholder="Buscar..."></label></div>')
    );

    // Conectar el input de búsqueda con DataTables
    $('#tableSearchContainer input').on('keyup', function () {
        userTable.search(this.value).draw();
    });

    // Crear y colocar los botones en el contenedor específico
    new $.fn.dataTable.Buttons(userTable, {
        buttons: userTable.init().buttons
    }).container().appendTo('#tableButtons');

    // Crear y colocar el info en su contenedor
    $('#tableInfoContainer').append(
        $('<div class="dataTables_info" role="status" aria-live="polite"></div>')
    );

    // Crear y colocar la paginación en su contenedor
    $('#tablePaginationContainer').append(
        $('<div class="dataTables_paginate paging_simple_numbers"></div>')
    );

    // Función para actualizar la información y paginación
    function updatePaginationInfo() {
        const info = userTable.page.info();

        // Actualizar la información de registros mostrados
        const infoText = `Mostrando ${info.start + 1} a ${info.end} de ${info.recordsTotal} registros`;
        $('#tableInfoContainer .dataTables_info').html(infoText);

        // Crear los botones de paginación
        let paginationHtml = '<ul class="pagination">';

        // Botón "Anterior"
        const prevDisabled = info.page === 0 ? ' disabled' : '';
        paginationHtml += `<li class="paginate_button page-item previous${prevDisabled}">
                    <a href="#" class="page-link" data-dt-idx="previous">Anterior</a>
                  </li>`;

        // Páginas numeradas
        const maxPages = 5; // Número máximo de páginas para mostrar
        let startPage = Math.max(0, info.page - Math.floor(maxPages / 2));
        let endPage = Math.min(info.pages - 1, startPage + maxPages - 1);

        // Ajustar si estamos cerca del final
        if (endPage - startPage + 1 < maxPages && startPage > 0) {
            startPage = Math.max(0, endPage - maxPages + 1);
        }

        for (let i = startPage; i <= endPage; i++) {
            const active = i === info.page ? ' active' : '';
            paginationHtml += `<li class="paginate_button page-item${active}">
                        <a href="#" class="page-link" data-dt-idx="${i}">${i + 1}</a>
                      </li>`;
        }

        // Botón "Siguiente"
        const nextDisabled = info.page >= info.pages - 1 ? ' disabled' : '';
        paginationHtml += `<li class="paginate_button page-item next${nextDisabled}">
                    <a href="#" class="page-link" data-dt-idx="next">Siguiente</a>
                  </li>`;

        paginationHtml += '</ul>';
        $('#tablePaginationContainer .dataTables_paginate').html(paginationHtml);

        // Agregar eventos a los botones de paginación
        $('#tablePaginationContainer .paginate_button a').on('click', function (e) {
            e.preventDefault();

            if ($(this).parent().hasClass('disabled')) {
                return;
            }

            const idx = $(this).data('dt-idx');
            if (idx === 'previous') {
                userTable.page('previous').draw('page');
            } else if (idx === 'next') {
                userTable.page('next').draw('page');
            } else {
                userTable.page(parseInt(idx)).draw('page');
            }
        });
    }

    // Actualizar la paginación e información inicialmente y cuando cambie la tabla
    updatePaginationInfo();
    userTable.on('draw', updatePaginationInfo);
}
