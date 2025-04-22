<?php
namespace App\Controllers\Auth;
use App\Controllers\BaseController;
use App\Models\AuthModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class LoginController extends BaseController
{
    public function index()
    {
        // La redirección la maneja ahora el LoggedInFilter
        $data = [
            'lastData' => session()->get('last_data')
        ];

        return view('auth/login', $data);
    }

    public function login()
    {
        helper(['form', 'response']);
        $validation = \Config\Services::validation();

        // Validar entrada
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        // Configurar mensajes personalizados (opcional)
        $messages = [
            'email' => [
                'required' => 'El correo electrónico es obligatorio',
                'valid_email' => 'Ingrese un correo electrónico válido'
            ],
            'password' => [
                'required' => 'La contraseña es obligatoria',
                'min_length' => 'La contraseña debe tener al menos 6 caracteres'
            ]
        ];

        $validation->setRules($rules, $messages);

        if (!$validation->run($this->request->getPost())) {

            return redirectView('login', null, [['Por favor corrige los errores en el formulario', 'error', 'top-end']], $this->request->getPost(), null);
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $userModel = new AuthModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return redirectView('login', null, [['Datos no encontrados', 'error', 'top-end']], $this->request->getPost(), null);
        }

        if (!password_verify($password, $user['password'])) {
            return redirectView('login', null, [['Datos no encontrados', 'error', 'top-end']], $this->request->getPost(), null);
        }

        // Verificar si la cuenta está activa
        if (isset($user['status']) && $user['status'] != 'active') {
            return redirectView('login', null, ['error' => 'Tu cuenta no está activa. Por favor contacta al administrador.'], $this->request->getPost());
        }

        // Login correcto
        session()->set([
            'user_id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'logged_in' => true,
            'last_login' => date('Y-m-d H:i:s')
        ]);

        // Redirigir según el rol
        if ($user['role'] === 'admin') {

            return redirectView('dashboard', null, [['Bienvenido de nuevo, ' . $user['name'], 'success', 'top-end']]);
        } else {
            return redirectView('dashboard', null, [['Bienvenido de nuevo, ' . $user['name'], 'success', 'top-end']]);
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('login')->with('flashMessages', ['info' => 'Has cerrado sesión correctamente']);
    }
}