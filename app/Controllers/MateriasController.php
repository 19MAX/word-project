<?php

namespace App\Controllers;

use App\Models\MateriaModel;
use App\Models\ObjetivoModel;
use App\Models\UnidadModel;
use App\Models\TemaModel;
use App\Models\BibliografiaModel;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Paragraph;
use PhpOffice\PhpWord\Style\Language;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class MateriasController extends BaseController
{
    protected $materiaModel;
    protected $objetivoModel;
    protected $unidadModel;
    protected $temaModel;
    protected $bibliografiaModel;

    public function __construct()
    {
        $this->materiaModel = new MateriaModel();
        $this->objetivoModel = new ObjetivoModel();
        $this->unidadModel = new UnidadModel();
        $this->temaModel = new TemaModel();
        $this->bibliografiaModel = new BibliografiaModel();

        helper('form');

    }



    /**
     * Listado principal de materias (CRUD)
     */
    public function index()
    {
        $usuarioId = session("user_id");
        $data = [
            'title' => 'Mis Materias',
            'materias' => $this->materiaModel->where('usuario_id', $usuarioId)->findAll()
        ];

        return view('client/materias/index', $data);
    }

    /**
     * Datos para DataTables (AJAX)
     */
    public function listar()
    {
        $usuarioId = session("user_id");

        $draw = $this->request->getPost('draw');
        $start = $this->request->getPost('start');
        $length = $this->request->getPost('length');
        $search = $this->request->getPost('search')['value'];

        $builder = $this->materiaModel->builder();
        $builder->where('usuario_id', $usuarioId);

        if (!empty($search)) {
            $builder->like('nombre', $search)
                ->orLike('ciclo', $search);
        }

        $total = $builder->countAllResults(false);
        $materias = $builder->get($length, $start)->getResultArray();

        $data = [
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $materias
        ];

        return $this->response->setJSON($data);
    }

    public function nueva()
    {
        $data = [
            'title' => 'Nueva Materia',
            'validation' => \Config\Services::validation()
        ];

        return view('client/materias/form', $data);
    }

    public function guardar()
    {
        // Verificamos si es una petición AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);
        }

        $nombre = trim($this->request->getPost('nombre'));
        $ciclo = trim($this->request->getPost('ciclo'));
        $descripcion = trim($this->request->getPost('descripcion'));

        $data = [
            'nombre' => $nombre,
            'ciclo' => $ciclo,
            'descripcion' => $descripcion,
            'usuario_id' => session("user_id"),
        ];

        try {
            $validation = \Config\Services::validation();

            // Reglas de validación
            $rules = [
                'nombre' => [
                    'label' => 'Nombre de la Materia',
                    'rules' => 'required|min_length[3]|max_length[100]',
                ],
                'ciclo' => [
                    'label' => 'Ciclo',
                    'rules' => 'permit_empty',
                ],
                'descripcion' => [
                    'label' => 'Descripción',
                    'rules' => 'required',
                ],
            ];

            $validation->setRules($rules);

            if (!$validation->run($data)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validation->getErrors()
                ]);
            }

            // Prepara los datos para insertar
            $insertData = [
                'nombre' => $nombre,
                'ciclo' => $ciclo,
                'descripcion' => $descripcion,
                'usuario_id' => session('user_id'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Inserta la materia
            $inserted = $this->materiaModel->insert($insertData);

            if (!$inserted) {
                throw new \RuntimeException('No se pudo insertar la materia.');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Materia creada exitosamente',
                'data' => [
                    'materia_id' => $inserted
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::guardar] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error al crear la materia: ' . $e->getMessage()
            ]);
        }
    }

    public function editar($id)
    {
        $usuarioId = session("user_id");
        $materia = $this->materiaModel->where('materia_id', $id)
            ->where('usuario_id', $usuarioId)
            ->first();

        if (!$materia) {
            return redirect()->to('/materias')->with('error', 'Materia no encontrada');
        }

        $data = [
            'title' => 'Editar Materia',
            'materia' => $materia,
            'validation' => \Config\Services::validation()
        ];

        return view('client/materias/form', $data);
    }

    public function actualizar($id)
    {
        // Verificamos si es una petición AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);
        }

        $nombre = trim($this->request->getPost('nombre'));
        $ciclo = trim($this->request->getPost('ciclo'));
        $descripcion = trim($this->request->getPost('descripcion'));

        $data = [
            'nombre' => $nombre,
            'ciclo' => $ciclo,
            'descripcion' => $descripcion
        ];

        try {
            $validation = \Config\Services::validation();

            // Reglas de validación (pueden ser las mismas que en guardar)
            $rules = [
                'nombre' => [
                    'label' => 'Nombre de la Materia',
                    'rules' => 'required|min_length[3]|max_length[100]',
                ],
                'ciclo' => [
                    'label' => 'Ciclo',
                    'rules' => 'permit_empty',
                ],
                'descripcion' => [
                    'label' => 'Descripción',
                    'rules' => 'required',
                ],
            ];

            $validation->setRules($rules);

            if (!$validation->run($data)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validation->getErrors()
                ]);
            }

            // Prepara los datos para actualizar
            $updateData = [
                'nombre' => $nombre,
                'ciclo' => $ciclo,
                'descripcion' => $descripcion,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Actualiza la materia
            $updated = $this->materiaModel->update($id, $updateData);

            if (!$updated) {
                throw new \RuntimeException('No se pudo actualizar la materia.');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Materia actualizada exitosamente',
                'data' => [
                    'materia_id' => $id
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::actualizar] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error al actualizar la materia: ' . $e->getMessage()
            ]);
        }
    }

    public function eliminar($id)
    {
        try {
            // Verificar si existe la materia
            $usuarioId = session("user_id"); // Asegúrate de manejar correctamente el ID del usuario
            $materia = $this->materiaModel->where('materia_id', $id)
                ->where('usuario_id', $usuarioId)
                ->first();

            if (!$materia) {
                throw new \RuntimeException('Materia no encontrada o no tienes permiso para eliminarla.');
            }

            // Iniciar transacción para garantizar la integridad de los datos
            $db = \Config\Database::connect();
            $db->transStart();

            try {
                // Eliminar registros relacionados en cascada
                $this->objetivoModel->where('materia_id', $id)->delete();
                $this->unidadModel->where('materia_id', $id)->delete();
                $this->bibliografiaModel->where('materia_id', $id)->delete();

                // Eliminar la materia
                if (!$this->materiaModel->delete($id)) {
                    throw new \RuntimeException('Error al eliminar la materia.');
                }

                // Confirmar transacción
                $db->transComplete();

                return redirectView('materias', null, [['Materia eliminada exitosamente', 'success', 'center']], null);

            } catch (\Exception $e) {
                // Revertir cambios si hay algún error
                $db->transRollback();
                throw $e;
            }

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::eliminar] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return redirectView('materias', null, [['Error al eliminar la materia: ' . $e->getMessage(), 'error', 'top-end']], null);
        }
    }

    public function obtener($id)
    {
        $materia = $this->materiaModel->find($id);

        if (!$materia) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Materia no encontrada'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $materia
        ]);
    }

    /**
     * SECCIÓN OBJETIVOS
     */

    public function objetivos($materia_id)
    {
        $usuarioId = session("user_id");

        // Verificar que la materia pertenece al usuario
        /* if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
            return redirect()->to('/materias')->with('error', 'Acceso no autorizado');
        } */

        $materia = $this->materiaModel->find($materia_id);
        $objetivos = $this->objetivoModel->getObjetivosWithResultados($materia_id);

        $data = [
            'title' => 'Objetivos de ' . $materia['nombre'],
            'materia' => $materia,
            'objetivos' => $objetivos
        ];

        return view('client/materias/objetivos', $data);
    }

    public function nuevoObjetivo($materia_id)
    {
        $usuarioId = session("user_id");

        /* if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
            return redirect()->to('/materias')->with('error', 'Acceso no autorizado');
        } */

        $materia = $this->materiaModel->find($materia_id);
        $ultimoNumero = $this->objetivoModel->where('materia_id', $materia_id)
            ->orderBy('numero_objetivo', 'DESC')
            ->first();

        $data = [
            'title' => 'Nuevo Objetivo',
            'materia' => $materia,
            'ultimo_numero' => $ultimoNumero ? $ultimoNumero['numero_objetivo'] : 0,
            'validation' => \Config\Services::validation()
        ];

        return view('client/materias/form_objetivo', $data);
    }

    public function guardarObjetivo($materia_id)
    {
        // Verificar si es una petición AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);
        }

        $numero_objetivo = trim($this->request->getPost('numero_objetivo'));
        $descripcion = trim($this->request->getPost('descripcion'));
        $resultado = trim($this->request->getPost('resultado'));

        $data = [
            'materia_id' => $materia_id,
            'numero_objetivo' => $numero_objetivo,
            'descripcion' => $descripcion,
            'resultado' => $resultado
        ];

        try {
            $usuarioId = session("user_id");

            // Verificar que la materia pertenece al usuario
            /* if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'No tienes permiso para modificar esta materia.'
                ]);
            } */

            $validation = \Config\Services::validation();

            // Reglas de validación
            $rules = [
                'numero_objetivo' => [
                    'label' => 'Número de Objetivo',
                    'rules' => 'required|numeric',
                ],
                'descripcion' => [
                    'label' => 'Descripción del Objetivo',
                    'rules' => 'required',
                ],
                'resultado' => [
                    'label' => 'Resultado de Aprendizaje',
                    'rules' => 'required',
                ],
            ];

            $validation->setRules($rules);

            if (!$validation->run($data)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validation->getErrors()
                ]);
            }

            // Iniciar transacción
            $db = \Config\Database::connect();
            $db->transStart();

            try {
                // Insertar objetivo
                $dataObjetivo = [
                    'materia_id' => $materia_id,
                    'numero_objetivo' => $numero_objetivo,
                    'descripcion' => $descripcion,
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $objetivo_id = $this->objetivoModel->insert($dataObjetivo);

                if (!$objetivo_id) {
                    throw new \RuntimeException('No se pudo guardar el objetivo.');
                }

                // Insertar resultado si existe
                if (!empty($resultado)) {
                    $dataResultado = [
                        'objetivo_id' => $objetivo_id,
                        'descripcion' => $resultado,
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    $resultadoInserted = $db->table('resultados')->insert($dataResultado);

                    if (!$resultadoInserted) {
                        throw new \RuntimeException('No se pudo guardar el resultado de aprendizaje.');
                    }
                }

                // Confirmar transacción
                $db->transComplete();

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Objetivo guardado exitosamente',
                    'data' => [
                        'objetivo_id' => $objetivo_id
                    ]
                ]);

            } catch (\Exception $e) {
                // Revertir cambios si hay algún error
                $db->transRollback();
                throw $e;
            }

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::guardarObjetivo] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error al guardar el objetivo: ' . $e->getMessage()
            ]);
        }
    }

    public function actualizarObjetivo($materia_id, $objetivo_id)
    {
        // Verificar si es una petición AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);
        }

        $numero_objetivo = trim($this->request->getPost('numero_objetivo'));
        $descripcion = trim($this->request->getPost('descripcion'));
        $resultado = trim($this->request->getPost('resultado'));

        $data = [
            'objetivo_id' => $objetivo_id,
            'materia_id' => $materia_id,
            'numero_objetivo' => $numero_objetivo,
            'descripcion' => $descripcion,
            'resultado' => $resultado
        ];

        try {
            $usuarioId = session("user_id");

            // Verificar que la materia pertenece al usuario
            /* if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'No tienes permiso para modificar esta materia.'
                ]);
            } */

            // Verifica si existe el objetivo
            $objetivo = $this->objetivoModel->find($objetivo_id);
            if (!$objetivo || $objetivo['materia_id'] != $materia_id) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Objetivo no encontrado.'
                ]);
            }

            $validation = \Config\Services::validation();

            // Reglas de validación
            $rules = [
                'numero_objetivo' => [
                    'label' => 'Número de Objetivo',
                    'rules' => 'required|numeric',
                ],
                'descripcion' => [
                    'label' => 'Descripción del Objetivo',
                    'rules' => 'required',
                ],
                'resultado' => [
                    'label' => 'Resultado de Aprendizaje',
                    'rules' => 'required',
                ],
            ];

            $validation->setRules($rules);

            if (!$validation->run($data)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validation->getErrors()
                ]);
            }

            // Iniciar transacción
            $db = \Config\Database::connect();
            $db->transStart();

            try {
                // Actualizar objetivo
                $updateData = [
                    'numero_objetivo' => $numero_objetivo,
                    'descripcion' => $descripcion,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $updated = $this->objetivoModel->update($objetivo_id, $updateData);

                if (!$updated) {
                    throw new \RuntimeException('No se pudo actualizar el objetivo.');
                }

                // Manejar el resultado (si existe)
                $resultadoExistente = $db->table('resultados')
                    ->where('objetivo_id', $objetivo_id)
                    ->get()
                    ->getRow();

                if ($resultadoExistente) {
                    if (!empty($resultado)) {
                        // Actualizar resultado existente
                        $resultadoUpdated = $db->table('resultados')
                            ->where('objetivo_id', $objetivo_id)
                            ->update([
                                'descripcion' => $resultado,
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);

                        if (!$resultadoUpdated) {
                            throw new \RuntimeException('No se pudo actualizar el resultado de aprendizaje.');
                        }
                    } else {
                        // Eliminar resultado si ahora está vacío
                        $resultadoDeleted = $db->table('resultados')
                            ->where('objetivo_id', $objetivo_id)
                            ->delete();

                        if (!$resultadoDeleted) {
                            throw new \RuntimeException('No se pudo eliminar el resultado de aprendizaje.');
                        }
                    }
                } else if (!empty($resultado)) {
                    // Crear nuevo resultado si no existía
                    $dataResultado = [
                        'objetivo_id' => $objetivo_id,
                        'descripcion' => $resultado,
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    $resultadoInserted = $db->table('resultados')->insert($dataResultado);

                    if (!$resultadoInserted) {
                        throw new \RuntimeException('No se pudo guardar el resultado de aprendizaje.');
                    }
                }

                // Confirmar transacción
                $db->transComplete();

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Objetivo actualizado exitosamente',
                    'data' => [
                        'objetivo_id' => $objetivo_id
                    ]
                ]);

            } catch (\Exception $e) {
                // Revertir cambios si hay algún error
                $db->transRollback();
                throw $e;
            }

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::actualizarObjetivo] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error al actualizar el objetivo: ' . $e->getMessage()
            ]);
        }
    }

    public function eliminarObjetivo($materia_id, $objetivo_id)
    {
        try {
            $usuarioId = session("user_id");

            // Verificar que la materia pertenece al usuario
            /* if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
                throw new \RuntimeException('No tienes permiso para modificar esta materia.');
            } */

            // Verificar que el objetivo existe y pertenece a la materia
            $objetivo = $this->objetivoModel->find($objetivo_id);
            if (!$objetivo || $objetivo['materia_id'] != $materia_id) {
                throw new \RuntimeException('Objetivo no encontrado o no pertenece a esta materia.');
            }

            // Iniciar transacción para garantizar la integridad de los datos
            $db = \Config\Database::connect();
            $db->transStart();

            try {
                // Eliminar resultados asociados primero
                $db->table('resultados')->where('objetivo_id', $objetivo_id)->delete();

                // Eliminar el objetivo
                if (!$this->objetivoModel->delete($objetivo_id)) {
                    throw new \RuntimeException('Error al eliminar el objetivo.');
                }

                // Confirmar transacción
                $db->transComplete();

                return redirectView("materias/objetivos/{$materia_id}", null, [['Objetivo eliminado exitosamente', 'success', 'center']], null);

            } catch (\Exception $e) {
                // Revertir cambios si hay algún error
                $db->transRollback();
                throw $e;
            }

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::eliminarObjetivo] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return redirectView("materias/objetivos/{$materia_id}", null, [['Error al eliminar el objetivo: ' . $e->getMessage(), 'error', 'top-end']], null);
        }
    }

    public function obtenerObjetivo($objetivo_id)
    {
        // Verificar si es una petición AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);
        }

        try {
            $usuarioId = session("user_id");
            $objetivo = $this->objetivoModel->getObjetivoWithResultado($objetivo_id);

            if (!$objetivo) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Objetivo no encontrado'
                ]);
            }

            // Verificar que la materia pertenece al usuario
            /* if (!$this->materiaModel->belongsToUser($objetivo['materia_id'], $usuarioId)) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'No tienes permiso para acceder a este objetivo'
                ]);
            } */

            return $this->response->setJSON([
                'success' => true,
                'data' => $objetivo
            ]);

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::obtenerObjetivo] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error al obtener el objetivo'
            ]);
        }
    }

    public function siguienteNumeroObjetivo($materia_id)
    {
        // Verificar si es una petición AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);
        }

        try {
            $usuarioId = session("user_id");

            // Verificar que la materia pertenece al usuario
            /* if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'No tienes permiso para acceder a esta materia.'
                ]);
            } */

            // Obtener el último número de objetivo para esta materia
            $ultimoNumero = $this->objetivoModel
                ->where('materia_id', $materia_id)
                ->selectMax('numero_objetivo')
                ->get()
                ->getRow()
                ->numero_objetivo;

            $siguienteNumero = $ultimoNumero ? $ultimoNumero + 1 : 1;

            return $this->response->setJSON([
                'success' => true,
                'siguiente_numero' => $siguienteNumero
            ]);

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::siguienteNumeroObjetivo] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error al obtener el siguiente número de objetivo'
            ]);
        }
    }

    /**
     * SECCIÓN UNIDADES
     */
    public function unidades($materia_id)
    {
        $usuarioId = session("user_id");

        /* if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
            return redirect()->to('/materias')->with('error', 'Acceso no autorizado');
        } */

        $materia = $this->materiaModel->find($materia_id);
        $unidades = $this->unidadModel->getUnidadesWithTemas($materia_id);

        $data = [
            'title' => 'Unidades de ' . $materia['nombre'],
            'materia' => $materia,
            'unidades' => $unidades
        ];

        return view('client/materias/unidades', $data);
    }

    public function nuevaUnidad($materia_id)
    {
        $usuarioId = session("user_id");

        /*   if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
              return redirect()->to('/materias')->with('error', 'Acceso no autorizado');
          } */

        $materia = $this->materiaModel->find($materia_id);
        $ultimoNumero = $this->unidadModel->where('materia_id', $materia_id)
            ->orderBy('numero_unidad', 'DESC')
            ->first();

        $data = [
            'title' => 'Nueva Unidad',
            'materia' => $materia,
            'ultimo_numero' => $ultimoNumero ? $ultimoNumero['numero_unidad'] : 0,
            'validation' => \Config\Services::validation()
        ];

        return view('client/materias/form_unidad', $data);
    }
    /**
     * Mostrar formulario para editar unidad
     */
    public function editarUnidad($materia_id, $unidad_id)
    {
        $usuarioId = session("user_id");

        // Verificar permisos
        /*  if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
             return redirect()->to('/materias')->with('error', 'Acceso no autorizado');
         } */

        // Verificar que la unidad existe y pertenece a la materia
        $unidad = $this->unidadModel->find($unidad_id);
        if (!$unidad || $unidad['materia_id'] != $materia_id) {
            return redirect()->to("/materias/unidades/{$materia_id}")->with('error', 'Unidad no encontrada');
        }

        $materia = $this->materiaModel->find($materia_id);

        $data = [
            'title' => 'Editar Unidad',
            'materia' => $materia,
            'unidad' => $unidad,
            'validation' => \Config\Services::validation()
        ];

        return view('client/materias/form_unidad', $data);
    }

    /**
     * Guardar una nueva unidad
     */
    public function guardarUnidad($materia_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);
        }

        $numero_unidad = trim($this->request->getPost('numero_unidad'));
        $nombre = trim($this->request->getPost('nombre'));
        $objetivo = trim($this->request->getPost('objetivo'));

        $data = [
            'materia_id' => $materia_id,
            'numero_unidad' => $numero_unidad,
            'nombre' => $nombre,
            'objetivo' => $objetivo
        ];

        try {
            $validation = \Config\Services::validation();

            $rules = [
                'numero_unidad' => [
                    'label' => 'Número de Unidad',
                    'rules' => 'required|numeric|is_unique[unidades.numero_unidad,materia_id,' . $materia_id . ']',
                ],
                'nombre' => [
                    'label' => 'Nombre de la Unidad',
                    'rules' => 'required|min_length[3]|max_length[100]',
                ],
                'objetivo' => [
                    'label' => 'Objetivo de la Unidad',
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'El objetivo de la unidad es obligatorio'
                    ]
                ],
            ];

            $validation->setRules($rules);

            if (!$validation->run($data)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validation->getErrors()
                ]);
            }

            $insertData = [
                'materia_id' => $materia_id,
                'numero_unidad' => $numero_unidad,
                'nombre' => $nombre,
                'objetivo' => $objetivo,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $inserted = $this->unidadModel->insert($insertData);

            if (!$inserted) {
                throw new \RuntimeException('No se pudo guardar la unidad.');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Unidad creada exitosamente',
                'data' => [
                    'unidad_id' => $inserted
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::guardarUnidad] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error al crear la unidad: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Actualizar una unidad existente
     */
    public function actualizarUnidad($materia_id, $unidad_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);
        }

        $numero_unidad = trim($this->request->getPost('numero_unidad'));
        $nombre = trim($this->request->getPost('nombre'));
        $objetivo = trim($this->request->getPost('objetivo'));

        $data = [
            'unidad_id' => $unidad_id,
            'materia_id' => $materia_id,
            'numero_unidad' => $numero_unidad,
            'nombre' => $nombre,
            'objetivo' => $objetivo
        ];

        try {
            $unidad = $this->unidadModel->find($unidad_id);
            if (!$unidad || $unidad['materia_id'] != $materia_id) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Unidad no encontrada'
                ]);
            }

            $validation = \Config\Services::validation();

            $rules = [
                'numero_unidad' => [
                    'label' => 'Número de Unidad',
                    'rules' => "required|numeric|is_unique[unidades.numero_unidad,materia_id,$materia_id,unidad_id,$unidad_id]",
                ],
                'nombre' => [
                    'label' => 'Nombre de la Unidad',
                    'rules' => 'required|min_length[3]|max_length[100]'
                ],
                'objetivo' => [
                    'label' => 'Objetivo de la Unidad',
                    'rules' => 'required'
                ],
            ];

            $validation->setRules($rules);

            if (!$validation->run($data)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validation->getErrors()
                ]);
            }

            $updateData = [
                'numero_unidad' => $numero_unidad,
                'nombre' => $nombre,
                'objetivo' => $objetivo,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $updated = $this->unidadModel->update($unidad_id, $updateData);

            if (!$updated) {
                throw new \RuntimeException('No se pudo actualizar la unidad.');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Unidad actualizada exitosamente',
                'data' => [
                    'unidad_id' => $unidad_id
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::actualizarUnidad] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error al actualizar la unidad: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Eliminar una unidad y sus temas asociados
     */
    public function eliminarUnidad($materia_id, $unidad_id)
    {
        try {
            $usuarioId = session("user_id");

            // Verificar que la materia pertenece al usuario
            /* if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
                throw new \RuntimeException('No tienes permiso para modificar esta materia.');
            } */

            // Verificar que la unidad existe y pertenece a la materia
            $unidad = $this->unidadModel->find($unidad_id);
            if (!$unidad || $unidad['materia_id'] != $materia_id) {
                throw new \RuntimeException('Unidad no encontrada o no pertenece a esta materia.');
            }

            // Iniciar transacción para garantizar la integridad de los datos
            $db = \Config\Database::connect();
            $db->transStart();

            try {
                // Eliminar temas asociados
                $this->temaModel->where('unidad_id', $unidad_id)->delete();

                // Eliminar la unidad
                if (!$this->unidadModel->delete($unidad_id)) {
                    throw new \RuntimeException('Error al eliminar la unidad.');
                }

                // Confirmar transacción
                $db->transComplete();

                return redirectView("materias/unidades/{$materia_id}", null, [['Unidad eliminada exitosamente', 'success', 'center']], null);

            } catch (\Exception $e) {
                // Revertir cambios si hay algún error
                $db->transRollback();
                throw $e;
            }

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::eliminarUnidad] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return redirectView("materias/unidades/{$materia_id}", null, [['Error al eliminar la unidad: ' . $e->getMessage(), 'error', 'top-end']], null);
        }
    }

    public function obtenerUnidad($unidad_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);
        }

        try {
            $unidad = $this->unidadModel->find($unidad_id);

            if (!$unidad) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Unidad no encontrada'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $unidad
            ]);
        } catch (\Exception $e) {
            log_message('error', '[MateriasController::obtenerUnidad] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error al obtener la unidad'
            ]);
        }
    }



    /**
     * SECCIÓN TEMAS
     */

    /**
     * Mostrar formulario para nuevo tema
     */
    public function nuevoTema($materia_id, $unidad_id)
    {
        $usuarioId = session("user_id");

        // Verificar permisos
        /* if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
            return redirect()->to('/materias')->with('error', 'Acceso no autorizado');
        } */

        // Verificar que la unidad pertenece a la materia
        $unidad = $this->unidadModel->find($unidad_id);
        if (!$unidad || $unidad['materia_id'] != $materia_id) {
            return redirect()->to("/materias/unidades/{$materia_id}")->with('error', 'Unidad no encontrada');
        }

        $materia = $this->materiaModel->find($materia_id);
        $ultimoNumero = $this->temaModel->where('unidad_id', $unidad_id)
            ->orderBy('numero_tema', 'DESC')
            ->first();

        $data = [
            'title' => 'Nuevo Tema',
            'materia' => $materia,
            'unidad' => $unidad,
            'ultimo_numero' => $ultimoNumero ? $ultimoNumero['numero_tema'] : 0,
            'validation' => \Config\Services::validation()
        ];

        return view('client/materias/form_tema', $data);
    }

    /**
     * Guardar un nuevo tema
     */
    public function guardarTema($materia_id, $unidad_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);
        }

        $numero_tema = trim($this->request->getPost('numero_tema'));
        $nombre = trim($this->request->getPost('nombre'));

        $data = [
            'unidad_id' => $unidad_id,
            'numero_tema' => $numero_tema,
            'nombre' => $nombre
        ];

        try {
            $unidad = $this->unidadModel->find($unidad_id);
            if (!$unidad || $unidad['materia_id'] != $materia_id) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Unidad no encontrada'
                ]);
            }

            $validation = \Config\Services::validation();

            $rules = [
                'numero_tema' => [
                    'label' => 'Número de Tema',
                    'rules' => "required|numeric",
                ],
                'nombre' => [
                    'label' => 'Nombre del Tema',
                    'rules' => 'required|min_length[3]|max_length[100]',
                ],
            ];

            $validation->setRules($rules);

            if (!$validation->run($data)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validation->getErrors()
                ]);
            }

            $insertData = [
                'unidad_id' => $unidad_id,
                'numero_tema' => $numero_tema,
                'nombre' => $nombre,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $inserted = $this->temaModel->insert($insertData);

            if (!$inserted) {
                throw new \RuntimeException('No se pudo guardar el tema.');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tema creado exitosamente',
                'data' => [
                    'tema_id' => $inserted
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::guardarTema] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error al crear el tema: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mostrar formulario para editar tema
     */
    public function editarTema($materia_id, $unidad_id, $tema_id)
    {
        $usuarioId = session("user_id");

        // Verificar permisos
        /* if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
            return redirect()->to('/materias')->with('error', 'Acceso no autorizado');
        } */

        // Verificar que la unidad pertenece a la materia
        $unidad = $this->unidadModel->find($unidad_id);
        if (!$unidad || $unidad['materia_id'] != $materia_id) {
            return redirect()->to("/materias/unidades/{$materia_id}")->with('error', 'Unidad no encontrada');
        }

        // Obtener el tema
        $tema = $this->temaModel->find($tema_id);
        if (!$tema || $tema['unidad_id'] != $unidad_id) {
            return redirect()->to("/materias/unidades/{$materia_id}")->with('error', 'Tema no encontrado');
        }

        $materia = $this->materiaModel->find($materia_id);

        $data = [
            'title' => 'Editar Tema',
            'materia' => $materia,
            'unidad' => $unidad,
            'tema' => $tema,
            'validation' => \Config\Services::validation()
        ];

        return view('client/materias/form_tema', $data);
    }

    /**
     * Actualizar un tema existente
     */
    public function actualizarTema($materia_id, $unidad_id, $tema_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);
        }

        $numero_tema = trim($this->request->getPost('numero_tema'));
        $nombre = trim($this->request->getPost('nombre'));

        $data = [
            'tema_id' => $tema_id,
            'unidad_id' => $unidad_id,
            'numero_tema' => $numero_tema,
            'nombre' => $nombre
        ];

        try {
            $tema = $this->temaModel->find($tema_id);
            if (!$tema || $tema['unidad_id'] != $unidad_id) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Tema no encontrado'
                ]);
            }

            $unidad = $this->unidadModel->find($unidad_id);
            if (!$unidad || $unidad['materia_id'] != $materia_id) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Unidad no encontrada'
                ]);
            }

            $validation = \Config\Services::validation();

            $rules = [
                'numero_tema' => [
                    'label' => 'Número de Tema',
                    'rules' => "required|numeric",
                ],
                'nombre' => [
                    'label' => 'Nombre del Tema',
                    'rules' => 'required|min_length[3]|max_length[100]'
                ],
            ];

            $validation->setRules($rules);

            if (!$validation->run($data)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validation->getErrors()
                ]);
            }

            $updateData = [
                'numero_tema' => $numero_tema,
                'nombre' => $nombre,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $updated = $this->temaModel->update($tema_id, $updateData);

            if (!$updated) {
                throw new \RuntimeException('No se pudo actualizar el tema.');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tema actualizado exitosamente',
                'data' => [
                    'tema_id' => $tema_id
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::actualizarTema] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error al actualizar el tema: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Eliminar un tema
     */
    public function eliminarTema($materia_id, $unidad_id, $tema_id)
    {
        try {
            $usuarioId = session("user_id");

            // Verificar permisos
            /* if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
                throw new \RuntimeException('No tienes permiso para modificar esta materia.');
            } */

            // Verificar que la unidad pertenece a la materia
            $unidad = $this->unidadModel->find($unidad_id);
            if (!$unidad || $unidad['materia_id'] != $materia_id) {
                throw new \RuntimeException('Unidad no encontrada.');
            }

            // Verificar que el tema existe y pertenece a la unidad
            $tema = $this->temaModel->find($tema_id);
            if (!$tema || $tema['unidad_id'] != $unidad_id) {
                throw new \RuntimeException('Tema no encontrado.');
            }

            // Eliminar el tema
            if (!$this->temaModel->delete($tema_id)) {
                throw new \RuntimeException('Error al eliminar el tema.');
            }

            return redirectView("materias/unidades/{$materia_id}", null, [['Tema eliminado exitosamente', 'success', 'center']], null);

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::eliminarTema] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return redirectView("materias/unidades/{$materia_id}", null, [['Error al eliminar el tema: ' . $e->getMessage(), 'error', 'top-end']], null);
        }
    }

    /**
     * SECCIÓN BIBLIOGRAFÍA
     */
    public function bibliografia($materia_id)
    {
        $usuarioId = session("user_id");

        /* if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
            return redirect()->to('/materias')->with('error', 'Acceso no autorizado');
        } */

        $materia = $this->materiaModel->find($materia_id);
        $bibliografias = $this->bibliografiaModel->where('materia_id', $materia_id)->findAll();

        $data = [
            'title' => 'Bibliografía de ' . $materia['nombre'],
            'materia' => $materia,
            'bibliografias' => $bibliografias
        ];

        return view('client/materias/bibliografia', $data);
    }

    public function nuevaBibliografia($materia_id)
    {
        $usuarioId = session("user_id");

        /* if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
            return redirect()->to('/materias')->with('error', 'Acceso no autorizado');
        } */

        $materia = $this->materiaModel->find($materia_id);

        $data = [
            'title' => 'Nueva Referencia Bibliográfica',
            'materia' => $materia,
            'validation' => \Config\Services::validation()
        ];

        return view('client/materias/form_bibliografia', $data);
    }

    public function guardarBibliografia($materia_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);
        }

        $referencia = trim($this->request->getPost('referencia'));
        $enlace = trim($this->request->getPost('enlace'));

        $data = [
            'materia_id' => $materia_id,
            'referencia' => $referencia,
            'enlace' => $enlace
        ];

        try {
            $validation = \Config\Services::validation();

            $rules = [
                'referencia' => [
                    'label' => 'Referencia',
                    'rules' => 'required',
                ],
                'enlace' => [
                    'label' => 'Enlace',
                    'rules' => 'permit_empty|valid_url',
                ]
            ];

            $validation->setRules($rules);

            if (!$validation->run($data)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validation->getErrors()
                ]);
            }

            $insertData = [
                'materia_id' => $materia_id,
                'referencia' => $referencia,
                'enlace' => $enlace,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $inserted = $this->bibliografiaModel->insert($insertData);

            if (!$inserted) {
                throw new \RuntimeException('No se pudo guardar la referencia bibliográfica.');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Referencia creada exitosamente',
                'data' => [
                    'bibliografia_id' => $inserted
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::guardarBibliografia] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error al guardar la referencia: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mostrar formulario para editar bibliografía
     */
    public function editarBibliografia($materia_id, $bibliografia_id)
    {
        $usuarioId = session("user_id");

        // Verificar que la materia pertenece al usuario
        /* if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
            return redirect()->to('/materias')->with('error', 'Acceso no autorizado');
        } */

        // Verificar que la bibliografía existe y pertenece a la materia
        $bibliografia = $this->bibliografiaModel->find($bibliografia_id);
        if (!$bibliografia || $bibliografia['materia_id'] != $materia_id) {
            return redirect()->to("/materias/bibliografia/{$materia_id}")->with('error', 'Referencia bibliográfica no encontrada');
        }

        $materia = $this->materiaModel->find($materia_id);

        $data = [
            'title' => 'Editar Referencia Bibliográfica',
            'materia' => $materia,
            'bibliografia' => $bibliografia,
            'tipos_bibliografia' => [
                'basica' => 'Básica',
                'complementaria' => 'Complementaria',
                'electronica' => 'Electrónica',
                'otros' => 'Otros'
            ],
            'validation' => \Config\Services::validation()
        ];

        return view('client/materias/form_bibliografia', $data);
    }

    /**
     * Actualizar bibliografía existente
     */
    public function actualizarBibliografia($materia_id, $bibliografia_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);
        }

        $referencia = trim($this->request->getPost('referencia'));
        $enlace = trim($this->request->getPost('enlace'));

        $data = [
            'bibliografia_id' => $bibliografia_id,
            'materia_id' => $materia_id,
            'referencia' => $referencia,
            'enlace' => $enlace
        ];

        try {
            $bibliografia = $this->bibliografiaModel->find($bibliografia_id);
            if (!$bibliografia || $bibliografia['materia_id'] != $materia_id) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Referencia bibliográfica no encontrada'
                ]);
            }

            $validation = \Config\Services::validation();

            $rules = [
                'referencia' => [
                    'label' => 'Referencia',
                    'rules' => 'required',
                ],
                'enlace' => [
                    'label' => 'Enlace',
                    'rules' => 'permit_empty|valid_url',
                    'errors' => [
                        'valid_url' => 'El enlace debe ser una URL válida'
                    ]
                ]
            ];

            $validation->setRules($rules);

            if (!$validation->run($data)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validation->getErrors()
                ]);
            }

            $updateData = [
                'referencia' => $referencia,
                'enlace' => $enlace,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $updated = $this->bibliografiaModel->update($bibliografia_id, $updateData);

            if (!$updated) {
                throw new \RuntimeException('No se pudo actualizar la referencia bibliográfica.');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Referencia actualizada exitosamente',
                'data' => [
                    'bibliografia_id' => $bibliografia_id
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::actualizarBibliografia] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error al actualizar la referencia: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Eliminar bibliografía
     */
    public function eliminarBibliografia($materia_id, $bibliografia_id)
    {
        try {
            $usuarioId = session("user_id");

            // Verificar que la materia pertenece al usuario
            /* if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
                throw new \RuntimeException('No tienes permiso para modificar esta materia.');
            } */

            // Verificar que la bibliografía existe y pertenece a la materia
            $bibliografia = $this->bibliografiaModel->find($bibliografia_id);
            if (!$bibliografia || $bibliografia['materia_id'] != $materia_id) {
                throw new \RuntimeException('Referencia bibliográfica no encontrada o no pertenece a esta materia.');
            }

            // Eliminar la bibliografía
            if (!$this->bibliografiaModel->delete($bibliografia_id)) {
                throw new \RuntimeException('Error al eliminar la referencia bibliográfica.');
            }

            return redirectView("materias/bibliografia/{$materia_id}", null, [['Referencia bibliográfica eliminada exitosamente', 'success', 'center']], null);

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::eliminarBibliografia] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return redirectView("materias/bibliografia/{$materia_id}", null, [['Error al eliminar la referencia: ' . $e->getMessage(), 'error', 'top-end']], null);
        }
    }

    public function obtenerBibliografia($bibliografia_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);
        }

        try {
            $bibliografia = $this->bibliografiaModel->find($bibliografia_id);

            if (!$bibliografia) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Referencia bibliográfica no encontrada'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $bibliografia
            ]);
        } catch (\Exception $e) {
            log_message('error', '[MateriasController::obtenerBibliografia] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error al obtener la referencia'
            ]);
        }
    }



    /**
     * Generar documento Word
     */
    // Método para generar el documento Word
    public function generarWord($materia_id)
    {
        try {
            $usuarioId = session("user_id");
            $materia = $this->materiaModel->getMateriaWithRelations($materia_id, $usuarioId);

            if (!$materia) {
                return redirect()->back()->with('error', 'Materia no encontrada o no tienes permiso');
            }

            // Crear nuevo documento Word
            $phpWord = new PhpWord();

            $phpWord->getSettings()->setThemeFontLang(new Language(Language::ES_ES));
            // Agregar esto al inicio del método, después de crear $phpWord
            $phpWord->addTitleStyle(1, ['size' => 16, 'bold' => true], ['alignment' => 'center']);
            $phpWord->addTitleStyle(2, ['size' => 14, 'bold' => true], ['spaceAfter' => 240]);
            $phpWord->addFontStyle('boldStyle', ['bold' => true]);
            $phpWord->addParagraphStyle('justifyStyle', ['alignment' => 'both', 'spaceAfter' => Converter::pointToTwip(8)]);
            $section = $phpWord->addSection();

            // Estilos
            $fontStyleBold = ['bold' => true];
            $fontStyleTitle = ['size' => 14, 'bold' => true];
            $paragraphStyleCenter = ['alignment' => 'center'];
            $paragraphStyleJustify = ['alignment' => 'both'];

            // 1. Encabezado con el ciclo
            $section->addText(strtoupper($materia['ciclo']), $fontStyleTitle, $paragraphStyleCenter);
            $section->addTextBreak(1);

            // 2. Nombre de la materia
            $section->addText($materia['nombre'], $fontStyleTitle, $paragraphStyleCenter);
            $section->addTextBreak(2);

            // 3. Descripción de la asignatura
            $section->addText('Descripción de la asignatura', $fontStyleBold);
            $section->addText($materia['descripcion'], null, $paragraphStyleJustify);
            $section->addTextBreak(2);

            // 4. Objetivos de la Asignatura
            $section->addText('Objetivos de la Asignatura:', $fontStyleBold);
            foreach ($materia['objetivos'] as $objetivo) {
                $section->addText("Objetivo {$objetivo['numero_objetivo']}: {$objetivo['descripcion']}");
                if (!empty($objetivo['resultado'])) {
                    $section->addText("   - Resultado esperado: {$objetivo['resultado']}", ['italic' => true]);
                }
                $section->addTextBreak(1);
            }
            $section->addTextBreak(1);

            // 5. Unidades Didácticas
            $section->addText('Distribución en Unidades Didácticas:', $fontStyleBold);
            foreach ($materia['unidades'] as $unidad) {
                $section->addText("Unidad {$unidad['numero_unidad']}: {$unidad['nombre']}", $fontStyleBold);
                $section->addText("Objetivo: {$unidad['objetivo']}");

                // Agregar los temas de cada unidad
                if (!empty($unidad['temas'])) {
                    foreach ($unidad['temas'] as $tema) {
                        $section->addText("   - Tema {$tema['numero_tema']}: {$tema['nombre']}");
                    }
                }

                $section->addTextBreak(1);
            }
            $section->addTextBreak(1);

            // 6. Bibliografía
            $section->addText('Bibliografía', $fontStyleBold);
            foreach ($materia['bibliografias'] as $bibliografia) {
                $section->addText("- {$bibliografia['referencia']}");
                if (!empty($bibliografia['enlace'])) {
                    $section->addText("  Enlace: {$bibliografia['enlace']}", ['color' => '0000FF', 'underline' => 'single']);
                }
            }

            // Guardar el documento temporalmente
            $filename = 'Materia_' . url_title($materia['nombre'], '_') . '.docx';
            $temp_file = tempnam(sys_get_temp_dir(), 'phpword');
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($temp_file);

            // Descargar el archivo
            return $this->response->download($filename, file_get_contents($temp_file));
        } catch (\Throwable $e) {
            log_message('error', 'Error generando documento Word: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Ocurrió un error al generar el documento. Revisa los logs.');
        }
    }
}
