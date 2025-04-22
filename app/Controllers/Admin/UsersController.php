<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;
use Config\App;

class UsersController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        // Cargar el modelo de usuarios
        $this->userModel = new UsersModel();
    }

    public function create()
    {
        // Obtener datos del formulario
        $name = trim($this->request->getPost('name'));
        $email = trim($this->request->getPost('email'));
        $password = $this->request->getPost('password');

        // Crear el array para la validación
        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $password
        ];

        try {
            $validation = \Config\Services::validation();

            // Definir reglas de validación
            $rules = [
                'name' => [
                    'label' => 'Nombre',
                    'rules' => 'required|min_length[3]|max_length[100]',
                ],
                'email' => [
                    'label' => 'Correo Electrónico',
                    'rules' => 'required|valid_email|max_length[100]|is_unique[users.email]',
                ],
                'password' => [
                    'label' => 'Contraseña',
                    'rules' => 'required|min_length[8]|max_length[255]',
                ]
            ];

            $validation->setRules($rules);

            // Ejecutar validación
            if (!$validation->run($data)) {
                return redirectView('admin/users', $validation, [['Errores de validación', 'error', 'top-end']], $data, 'create');
            }

            // Hashear la contraseña antes de guardarla
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Datos para guardar en la base de datos
            $userData = [
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => 'user', // Rol por defecto
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Insertar el nuevo usuario
            $userId = $this->userModel->insert($userData);

            if (!$userId) {
                throw new \RuntimeException('No se pudo crear el usuario en la base de datos.');
            }

            // Éxito - redirigir con mensaje de éxito
            return redirectView('admin/users', null, [['Usuario creado exitosamente', 'success', 'center']], null);

        } catch (\Exception $e) {
            log_message('error', '[UserController::create] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());

            return redirectView('admin/users', null, [['Error al crear el usuario: ' . $e->getMessage(), 'error', 'top-end']], $data, 'create');
        }
    }

    /**
     * Actualiza la información de un usuario existente
     */
    public function update()
    {
        // Obtener datos del formulario
        $id = $this->request->getPost('id');
        $name = trim($this->request->getPost('name'));
        $email = trim($this->request->getPost('email'));

        // Crear el array para la validación
        $data = [
            'id' => $id,
            'name' => $name,
            'email' => $email
        ];

        try {
            $validation = \Config\Services::validation();

            // Obtener el usuario actual para verificar si cambia el email
            $currentUser = $this->userModel->find($id);

            if (!$currentUser) {
                throw new \RuntimeException('Usuario no encontrado.');
            }

            // Definir reglas de validación
            $rules = [
                'id' => 'required|is_natural_no_zero',
                'name' => [
                    'label' => 'Nombre',
                    'rules' => 'required|min_length[3]|max_length[100]',
                ],
                'email' => [
                    'label' => 'Correo Electrónico',
                    'rules' => 'required|valid_email|max_length[100]',
                ]
            ];

            // Si el email es diferente, validar que sea único
            if ($email !== $currentUser['email']) {
                $rules['email']['rules'] .= '|is_unique[users.email]';
            }

            $validation->setRules($rules);

            // Ejecutar validación
            if (!$validation->run($data)) {
                return redirectView('admin/users', $validation, [['Errores de validación', 'error', 'top-end']], $data, 'update');
            }

            // Datos para actualizar en la base de datos
            $userData = [
                'name' => $name,
                'email' => $email,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Actualizar el usuario
            $result = $this->userModel->update($id, $userData);

            if (!$result) {
                throw new \RuntimeException('No se pudo actualizar el usuario en la base de datos.');
            }

            // Éxito - redirigir con mensaje de éxito
            return redirectView('admin/users', null, [['Usuario actualizado exitosamente', 'success', 'center']], null);

        } catch (\Exception $e) {
            log_message('error', '[UserController::update] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return redirectView('admin/users', null, [['Error al actualizar el usuario: ' . $e->getMessage(), 'error', 'top-end']], $data, 'update');
        }
    }

    /**
     * Elimina un usuario existente
     */
    public function delete()
    {
        // Obtener el ID del usuario
        $id = $this->request->getPost('id');

        try {
            $validation = \Config\Services::validation();

            // Definir reglas de validación
            $rules = [
                'id' => 'required|is_natural_no_zero'
            ];

            $validation->setRules($rules);

            // Ejecutar validación
            if (!$validation->run(['id' => $id])) {
                return redirectView('admin/users', $validation, [['ID de usuario inválido', 'error', 'top-end']], null);
            }

            // Verificar si el usuario existe
            $user = $this->userModel->find($id);

            if (!$user) {
                throw new \RuntimeException('Usuario no encontrado.');
            }

            // Eliminar el usuario
            $result = $this->userModel->delete($id);

            if (!$result) {
                throw new \RuntimeException('No se pudo eliminar el usuario de la base de datos.');
            }

            // Éxito - redirigir con mensaje de éxito
            return redirectView('admin/users', null, [['Usuario eliminado exitosamente', 'success', 'center']], null);

        } catch (\Exception $e) {
            log_message('error', '[UserController::delete] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return redirectView('admin/users', null, [['Error al eliminar el usuario: ' . $e->getMessage(), 'error', 'top-end']], null);
        }
    }

    /**
     * Restablece la contraseña de un usuario
     */
    public function resetPassword()
    {
        // Obtener datos del formulario
        $id = $this->request->getPost('id');
        $password = $this->request->getPost('password');
        $confirm_password = $this->request->getPost('confirm_password');

        // Crear el array para la validación
        $data = [
            'id' => $id,
            'password' => $password,
            'confirm_password' => $confirm_password
        ];

        try {
            $validation = \Config\Services::validation();

            // Definir reglas de validación
            $rules = [
                'id' => 'required|is_natural_no_zero',
                'password' => [
                    'label' => 'Contraseña',
                    'rules' => 'required|min_length[8]|max_length[255]',
                ],
                'confirm_password' => [
                    'label' => 'Confirmar Contraseña',
                    'rules' => 'required|matches[password]',
                ]
            ];

            $validation->setRules($rules);

            // Ejecutar validación
            if (!$validation->run($data)) {
                return redirectView('admin/users', $validation, [['Errores de validación', 'error', 'top-end']], null, 'reset_password');
            }

            // Verificar si el usuario existe
            $user = $this->userModel->find($id);

            if (!$user) {
                throw new \RuntimeException('Usuario no encontrado.');
            }

            // Hashear la nueva contraseña
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Datos para actualizar en la base de datos
            $userData = [
                'password' => $hashedPassword,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Actualizar la contraseña del usuario
            $result = $this->userModel->update($id, $userData);

            if (!$result) {
                throw new \RuntimeException('No se pudo actualizar la contraseña en la base de datos.');
            }

            // Éxito - redirigir con mensaje de éxito
            return redirectView('admin/users', null, [['Contraseña restablecida exitosamente', 'success', 'center']], null);

        } catch (\Exception $e) {
            log_message('error', '[UserController::resetPassword] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return redirectView('admin/users', null, [['Error al restablecer la contraseña: ' . $e->getMessage(), 'error', 'top-end']], null, 'reset_password');
        }
    }
}
