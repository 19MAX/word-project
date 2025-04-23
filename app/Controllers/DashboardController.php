<?php

namespace App\Controllers;

use App\Controllers\BaseController;
// use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    protected $userModel;

    // Inicializar los modelos en el constructor
    public function __construct()
    {
        // $this->userModel = new UsersModel();
    }

    public function index()
    {
        // Verificar si el usuario está logueado
        if (!session()->has('user_id')) {
            return redirect()->to('/login'); // Redirigir al login si no hay sesión
        }

        // Obtener el rol del usuario desde la sesión
        $userRole = session()->get('role'); // Ajusta esto según cómo tengas almacenado el rol en tu sesión

        // $data = [
        //     'totalUsuarios' => $this->userModel->countAll(),
        // ];

        // Redireccionar según el rol
        switch ($userRole) {
            case 'admin':
                return view('admin/dashboard'); // Vista para administradores
                break;

            case 'user':

                return view('client/dashboard');

                break;

            default:
                // En caso de un rol no reconocido, puedes redirigir a una vista por defecto o mostrar error
                return redirect()->to('/login')->with('error', 'Rol no reconocido');
                break;
        }
    }
}