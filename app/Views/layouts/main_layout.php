<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url("assets/css/dashboard.css") ?>">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.12/sweetalert2.min.css"
        rel="stylesheet">
    <!-- Datatables CSS -->
    <link href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/3.2.2/css/buttons.bootstrap5.css" rel="stylesheet">

<!-- Summernote CSS (opcional) -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <?= $this->renderSection('styles') ?>
</head>

<body>
    <!-- Navbar (Topbar) -->
    <?= $this->include('partials/topbar') ?>

    <!-- Overlay para dispositivos móviles -->
    <div class="overlay" id="overlay"></div>

    <!-- Sidebar -->
    <?= $this->include('partials/sidebar') ?>

    <!-- Main Content -->
    <div class="main-content" id="content">
        <div class="container-fluid">
            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <?= $this->include('partials/scripts') ?>
    <?= $this->renderSection('scripts') ?>
</body>

</html>