<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('title') ?>
Panel de Control - Sistema de Asistencias
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2 class="mb-4">Panel de control</h2>

<div class="row">
    <!-- Tarjeta para Usuarios -->
    <div class="col-md-6 mb-4">
        <div class="card card-stats" style="border-left-color: #0d6efd;">
            <a href="#" class="text-decoration-none">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-muted mb-1">Usuarios</h6>
                            <h3 class="fw-bold"><?= $totalUsuarios ?? '0' ?></h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x text-primary opacity-75"></i>
                        </div>
                    </div>
                    <p class="small mb-0 mt-2">Total de usuarios registrados</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Tarjeta para Documentos -->
    <div class="col-md-6 mb-4">
        <div class="card card-stats" style="border-left-color: #6f42c1;">
            <a href="#" class="text-decoration-none">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title text-muted mb-1">Documentos</h6>
                            <h3 class="fw-bold"><?= $totalDocumentos ?? '0' ?></h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-alt fa-2x text-purple opacity-75"></i>
                        </div>
                    </div>
                    <p class="small mb-0 mt-2">Documentos creados en el sistema</p>
                </div>
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
