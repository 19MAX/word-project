<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('title') ?>
Panel de Control - Sistema de Asistencias
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2 class="mb-4">Panel de control</h2>

<div class="row">
    <!-- Tarjeta para Gestionar Usuarios -->
    <div class="col-md-6 mb-4">
        <div class="card card-stats" style="border-left-color: #0d6efd;">
            <a href="<?= base_url('admin/users') ?>" class="text-decoration-none">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-muted mb-1">Gestión de Usuarios</h6>
                            <h3 class="fw-bold"><?= $totalUsuarios ?? '0' ?></h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x text-primary opacity-75"></i>
                        </div>
                    </div>
                    <p class="small mb-0 mt-2">Administra todos los usuarios del sistema</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Tarjeta para Gestionar Asistencias -->
    <div class="col-md-6 mb-4">
        <div class="card card-stats" style="border-left-color: #198754;">
            <a href="<?= base_url('admin/attendances') ?>" class="text-decoration-none">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-muted mb-1">Gestión de Asistencias</h6>
                            <h3 class="fw-bold"><?= $totalAsistencias ?? '0' ?></h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clipboard-check fa-2x text-success opacity-75"></i>
                        </div>
                    </div>
                    <p class="small mb-0 mt-2">Registro y control de asistencias</p>
                </div>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Sección de Asistencias Recientes -->
    <div class="col-lg-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="card-title">Asistencias Recientes</h5>
                    <!-- <a href="<?= base_url('asistencias') ?>" class="text-decoration-none small">Ver todas</a> -->
                </div>

                <?php if (!empty($ultimasAsistencias)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Fecha</th>
                                    <th>Hora Entrada</th>
                                    <th>Hora Salida</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ultimasAsistencias as $asistencia): ?>
                                    <tr>
                                        <td><?= $asistencia['user_name'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($asistencia['date'])) ?></td>
                                        <td><?= $asistencia['time_in'] ?></td>
                                        <td><?= $asistencia['time_out'] ?? '--' ?></td>
                                        <td>
                                            <span
                                                class="badge bg-<?= $asistencia['status'] == 'validada' ? 'success' : 'warning' ?>">
                                                <?= ucfirst($asistencia['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-3">
                        <p class="text-muted">No hay asistencias registradas recientemente</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>