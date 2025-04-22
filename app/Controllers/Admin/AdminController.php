<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class AdminController extends BaseController
{
    protected $usersModel;

    public function __construct()
    {
        // Cargar el modelo de usuarios
        $this->usersModel = new UsersModel();
    }

    public function index()
    {
        return view('admin/dashboard');
    }
    public function users()
    {
        // Insertar el nuevo usuario
        $users = $this->usersModel->getAllUsersWithoutPassword();
        $data =[
            'users' => $users,
        ];
        return view('admin/users',$data);
    }
}
