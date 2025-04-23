<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Bibliografía de <?= esc($materia['nombre']) ?></h6>
            <a href="<?= site_url("materias/nueva-bibliografia/{$materia['materia_id']}") ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nueva Referencia
            </a>
        </div>
        <div class="card-body">
            <?php if (empty($bibliografias)): ?>
                <div class="alert alert-info">No hay referencias bibliográficas registradas.</div>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($bibliografias as $bibliografia): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-1"><?= esc($bibliografia['referencia']) ?></p>
                                <?php if (!empty($bibliografia['enlace'])): ?>
                                    <small><a href="<?= esc($bibliografia['enlace']) ?>" target="_blank"><?= esc($bibliografia['enlace']) ?></a></small>
                                <?php endif; ?>
                            </div>
                            <div>
                                <a href="<?= site_url("materias/editar-bibliografia/{$bibliografia['bibliografia_id']}") ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= site_url("materias/eliminar-bibliografia/{$bibliografia['bibliografia_id']}") ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta referencia?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="mt-3">
                <a href="<?= site_url('materias') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a Materias
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>