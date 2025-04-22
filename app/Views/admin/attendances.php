<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('title') ?>Gestión de Asistencias<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <h2 class="mb-4">Gestión de Asistencias</h2>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Registros de Asistencia</h6>
            <button class="btn btn-success btn-sm" id="validateAllBtn">
                <i class="fas fa-check-circle"></i> Validar Seleccionadas
            </button>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="dateRange">Rango de Fechas:</label>
                    <input type="text" class="form-control" id="dateRange" name="dateRange">
                </div>
                <div class="col-md-4">
                    <label for="statusFilter">Estado:</label>
                    <select class="form-control" id="statusFilter">
                        <option value="">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="validada">Validada</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="userFilter">Usuario:</label>
                    <select class="form-control" id="userFilter">
                        <option value="">Todos</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>"><?= esc($user['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table id="attendancesTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th width="20px">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>Usuario</th>
                            <th>Fecha</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Horas</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Validación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="confirmAction">Validar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>

<script>
  $(document).ready(function () {
    // Inicializar date range picker
    $('#dateRange').daterangepicker({
        locale: {
            format: 'YYYY-MM-DD',
            applyLabel: 'Aplicar',
            cancelLabel: 'Cancelar',
            fromLabel: 'Desde',
            toLabel: 'Hasta',
            customRangeLabel: 'Personalizado',
            daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            firstDay: 1
        },
        opens: 'right',
        autoUpdateInput: false,
        ranges: {
            'Hoy': [moment(), moment()],
            'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Últimos 7 Días': [moment().subtract(6, 'days'), moment()],
            'Últimos 30 Días': [moment().subtract(29, 'days'), moment()],
            'Este Mes': [moment().startOf('month'), moment().endOf('month')],
            'Mes Anterior': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    $('#dateRange').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        table.draw();
    });

    $('#dateRange').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
        table.draw();
    });

    // Inicializar DataTable
    var table = $('#attendancesTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimir',
                className: 'btn btn-info',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6]
                }
            }
        ],
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('admin/attendances/getAttendances') ?>',
            type: 'POST',
            data: function (d) {
                d.dateRange = $('#dateRange').val();
                d.status = $('#statusFilter').val();
                d.userId = $('#userFilter').val();
            },
            error: function (xhr, error, thrown) {
                console.error('Error en Ajax:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cargar los datos. Ver la consola para más detalles.'
                });
            }
        },
        columns: [
            {
                data: 'id',
                orderable: false,
                render: function (data, type, row) {
                    return row.status === 'pendiente' ?
                        '<input type="checkbox" class="attendanceCheck" value="' + data + '">' :
                        '';
                }
            },
            { data: 'user_name' },
            { data: 'date' },
            {
                data: 'time_in',
                render: function (data) {
                    return data || '-';
                }
            },
            {
                data: 'time_out',
                render: function (data) {
                    return data || '-';
                }
            },
            {
                data: 'worked_hours',
                render: function (data) {
                    return data || '-';
                }
            },
            {
                data: 'status',
                render: function (data) {
                    var badgeClass = data === 'validada' ? 'bg-success' : 'bg-warning text-dark';
                    return '<span class="badge ' + badgeClass + '">' + data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
                }
            },
            {
                data: 'id',
                orderable: false,
                render: function (data, type, row) {
                    if (row.status === 'pendiente') {
                        return '<button class="btn btn-sm btn-success validate-btn" data-id="' + data + '">Validar</button>';
                    } else {
                        return '<button class="btn btn-sm btn-secondary" disabled>Validado</button>';
                    }
                }
            }
        ],
        columnDefs: [
            { targets: [0], className: 'text-center' },
            { targets: [3, 4, 5], className: 'text-center' }
        ],
        language: {
             url: '<?= base_url('assets/js/spanishDatatables.json') ?>'
        }
    });

    // Aplicar filtros
    $('#statusFilter, #userFilter').change(function () {
        table.draw();
    });

    // Seleccionar todos
    $('#selectAll').click(function () {
        $('.attendanceCheck').prop('checked', this.checked);
    });

    // Función para validar asistencias
    function validateAttendances(data) {
        $.ajax({
            url: '<?= base_url('admin/attendances/validate') ?>',
            type: 'POST',
            data: data,
            success: function (response) {
                if (response.success) {
                    table.draw();
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al procesar la solicitud'
                });
            }
        });
    }

    // Validar asistencia individual
    $('#attendancesTable').on('click', '.validate-btn', function () {
        const attendanceId = $(this).data('id');
        
        Swal.fire({
            title: 'Confirmar Validación',
            text: '¿Estás seguro de validar esta asistencia?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, validar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                validateAttendances({ id: attendanceId });
            }
        });
    });

    // Validar múltiples asistencias
    $('#validateAllBtn').click(function () {
        const selected = $('.attendanceCheck:checked').map(function () {
            return $(this).val();
        }).get();

        if (selected.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Selección requerida',
                text: 'Por favor selecciona al menos una asistencia para validar'
            });
            return;
        }

        Swal.fire({
            title: 'Confirmar Validación Múltiple',
            text: `¿Estás seguro de validar las ${selected.length} asistencias seleccionadas?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, validar todas',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                validateAttendances({ ids: selected });
            }
        });
    });

    // No necesitamos el evento del modal de confirmación ya que ahora usamos SweetAlert
});</script>
<?= $this->endSection() ?>