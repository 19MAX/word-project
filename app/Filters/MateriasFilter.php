<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class MateriasFilter implements FilterInterface
{
    /**
     * Verifica que solo usuarios con rol 'user' puedan acceder a las rutas de materias.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Si no está logueado, redirigir al login
        if (!session()->get('logged_in')) {
            return redirect()->to('login');
        }

        // Obtener rol del usuario
        $role = session()->get('role');

        // Verificar que el rol sea 'user', si no, redirigir
        if ($role !== 'user') {
            return redirect()->to('dashboard')->with('flashMessages', [['Solo los usuarios pueden acceder a la sección de materias', 'error', 'top-end']]);
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}