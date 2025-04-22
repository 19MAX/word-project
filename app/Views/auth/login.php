<!DOCTYPE html>
<html lang="es">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <!-- SweetAlert2 CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.12/sweetalert2.min.css"
        rel="stylesheet">
    <style>
        body {
            background: url('<?= base_url("assets/fondo4.webp") ?>') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            max-width: 420px;
            margin: 100px auto;
        }

        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .card-header {
            background: rgba(13, 110, 253, 0.9);
            color: white;
            text-align: center;
            padding: 30px;
        }

        .card-body {
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.25);
        }

        .form-control {
            padding: 12px;
            border-radius: 6px;
            background-color: rgba(255, 255, 255, 0.7);
            border: 1px solid #ced4da;
            color: #333;
        }

        .btn-primary {
            padding: 12px;
            border-radius: 6px;
        }

        .input-group-text {
            background-color: rgba(255, 255, 255, 0.7);
            border: 1px solid #ced4da;
        }

        .show-password-toggle {
            cursor: pointer;
        }

        .invalid-feedback {
            display: block;
            font-size: 0.9rem;
        }
    </style>

</head>

<body>
    <div class="container login-container">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-circle fa-3x mb-2"></i>
                <h4 class="mb-0">Iniciar Sesión</h4>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('auth/login') ?>" method="post" id="loginForm">
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email"
                                class="form-control <?= session()->get('flashValidation') && isset(session()->get('flashValidation')['email']) ? 'is-invalid' : '' ?>"
                                id="email" name="email"
                                value="<?= session()->get('last_data') && isset(session()->get('last_data')['email']) ? session()->get('last_data')['email'] : '' ?>"
                                required>
                        </div>
                        <?php if (session()->get('flashValidation') && isset(session()->get('flashValidation')['email'])): ?>
                            <div class="invalid-feedback">
                                <?= session()->get('flashValidation')['email'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password"
                                class="form-control <?= session()->get('flashValidation')['password'] ?? '' ? 'is-invalid' : '' ?>"
                                id="password" name="password" required>
                            <span class="input-group-text show-password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                        <?php if (session()->get('flashValidation')['password'] ?? false): ?>
                            <div class="invalid-feedback">
                                <?= session()->get('flashValidation')['password'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Entrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const toggleIcon = document.getElementById("toggleIcon");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleIcon.classList.remove("fa-eye");
                toggleIcon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                toggleIcon.classList.remove("fa-eye-slash");
                toggleIcon.classList.add("fa-eye");
            }
        }
    </script>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.12/sweetalert2.min.js"></script>
    <script src="<?= base_url("assets/js/sweetalert2.js") ?>"></script>

    <script>
        // Verificar si hay mensajes de éxito, advertencia o error
        <?php if (session()->has('flashMessages')): ?>
            <?php foreach (session('flashMessages') as $message): ?>
                <?php
                $type = $message[1]; // Tipo de notificación
                $msg = $message[0];  // Mensaje
                $position = $message[2] ?? 'top-end'; // Posición (por defecto: top-end)
                ?>
                showAlert('<?= $type ?>', '<?= $msg ?>', '<?= $position ?>');
            <?php endforeach; ?>
        <?php endif; ?>

    </script>
</body>

</html>