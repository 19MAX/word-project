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
        $usuarioId = 2;
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
        $usuarioId = 2;

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
        $nombre = trim($this->request->getPost('nombre'));
        $ciclo = trim($this->request->getPost('ciclo'));
        $descripcion = trim($this->request->getPost('descripcion'));

        $data = [
            'nombre' => $nombre,
            'ciclo' => $ciclo,
            'descripcion' => $descripcion,
            'usuario_id' => 2 // Asegúrate de manejar correctamente el ID del usuario
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
                    'rules' => 'permit_empty|max_length[20]',
                ],
                'descripcion' => [
                    'label' => 'Descripción',
                    'rules' => 'permit_empty|max_length[500]',
                ],
            ];

            $validation->setRules($rules);

            if (!$validation->run($data)) {
                return redirectView('materias', $validation, [['Corrige los errores del formulario', 'error', 'top-end']], $data, 'create');
            }

            // Prepara los datos para insertar
            $insertData = [
                'nombre' => $nombre,
                'ciclo' => $ciclo,
                'descripcion' => $descripcion,
                'usuario_id' => 2, // Ajusta según tu lógica para obtener el ID del usuario
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Inserta la materia
            $inserted = $this->materiaModel->insert($insertData);

            if (!$inserted) {
                throw new \RuntimeException('No se pudo insertar la materia.');
            }

            return redirectView('materias', null, [['Materia creada exitosamente', 'success', 'center']], null);

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::insertar] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return redirectView('materias', null, [['Error al crear la materia: ' . $e->getMessage(), 'error', 'top-end']], $data, 'create');
        }
    }

    public function editar($id)
    {
        $usuarioId = 2;
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
        $nombre = trim($this->request->getPost('nombre'));
        $ciclo = trim($this->request->getPost('ciclo'));
        $descripcion = trim($this->request->getPost('descripcion'));

        $data = [
            'materia_id' => $id,
            'nombre' => $nombre,
            'ciclo' => $ciclo,
            'descripcion' => $descripcion
        ];

        try {
            $validation = \Config\Services::validation();

            // Verifica si existe la materia
            $materia = $this->materiaModel->find($id);
            if (!$materia) {
                throw new \RuntimeException('Materia no encontrada.');
            }

            // Reglas de validación
            $rules = [
                'nombre' => [
                    'label' => 'Nombre de la Materia',
                    'rules' => 'required|min_length[3]|max_length[100]',
                ],
                'ciclo' => [
                    'label' => 'Ciclo',
                    'rules' => 'permit_empty|max_length[20]',
                ],
                'descripcion' => [
                    'label' => 'Descripción',
                    'rules' => 'permit_empty|max_length[500]',
                ],
            ];

            $validation->setRules($rules);

            if (!$validation->run($data)) {
                return redirectView('materias', $validation, [['Corrige los errores del formulario', 'error', 'top-end']], $data, 'update');
            }

            // Actualiza la materia
            $updateData = [
                'nombre' => $nombre,
                'ciclo' => $ciclo,
                'descripcion' => $descripcion,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $updated = $this->materiaModel->update($id, $updateData);

            if (!$updated) {
                throw new \RuntimeException('No se pudo actualizar la materia.');
            }

            return redirectView('materias', null, [['Materia actualizada exitosamente', 'success', 'center']], null);

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::actualizar] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return redirectView('materias', null, [['Error al actualizar la materia: ' . $e->getMessage(), 'error', 'top-end']], $data, 'update');
        }
    }

    public function eliminar($id)
    {
        try {
            // Verificar si existe la materia
            $usuarioId = 2; // Asegúrate de manejar correctamente el ID del usuario
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

    public function ver($id)
    {
        $usuarioId = 2;
        $materia = $this->materiaModel->getMateriaWithRelations($id, $usuarioId);

        if (!$materia) {
            return redirect()->to('/materias')->with('error', 'Materia no encontrada');
        }

        $data = [
            'title' => $materia['nombre'],
            'materia' => $materia
        ];

        return view('client/materias/view', $data);
    }

    /**
     * SECCIÓN OBJETIVOS
     */

    public function objetivos($materia_id)
    {
        $usuarioId = 2;

        // Verificar que la materia pertenece al usuario
        if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
            return redirect()->to('/materias')->with('error', 'Acceso no autorizado');
        }

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
        $usuarioId = 2;

        if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
            return redirect()->to('/materias')->with('error', 'Acceso no autorizado');
        }

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
        $numero_objetivo = trim($this->request->getPost('numero_objetivo'));
        $descripcion = trim($this->request->getPost('descripcion'));
        $resultado = trim($this->request->getPost('resultado'));

        $data = [
            'materia_id' => $materia_id,
            'numero_objetivo' => $numero_objetivo,
            'descripcion' => $descripcion,
            'resultado' => $resultado // Este campo es para el formulario, no para la BD directamente
        ];

        try {
            $usuarioId = 2;

            // Verificar que la materia pertenece al usuario
            if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
                throw new \RuntimeException('No tienes permiso para modificar esta materia.');
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
                return redirectView("materias/nuevo-objetivo/{$materia_id}", $validation, [['Corrige los errores del formulario', 'error', 'top-end']], $data, 'create');
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

                return redirectView("materias/nuevo-objetivo/{$materia_id}", null, [['Objetivo guardado exitosamente', 'success', 'center']], null);

            } catch (\Exception $e) {
                // Revertir cambios si hay algún error
                $db->transRollback();
                throw $e;
            }

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::guardarObjetivo] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return redirectView("materias/nuevo-objetivo/{$materia_id}", null, [['Error al guardar el objetivo: ' . $e->getMessage(), 'error', 'top-end']], $data, 'create');
        }
    }

    /**
     * Editar un objetivo existente
     */
    public function editarObjetivo($materia_id, $objetivo_id)
    {
        $usuarioId = 2;

        // Verificar que la materia pertenece al usuario
        if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
            return redirect()->to('/materias')->with('error', 'Acceso no autorizado');
        }

        $materia = $this->materiaModel->find($materia_id);
        $objetivo = $this->objetivoModel->getObjetivoWithResultado($objetivo_id);

        if (!$objetivo) {
            return redirect()->to("/materias/objetivos/{$materia_id}")->with('error', 'Objetivo no encontrado');
        }

        $data = [
            'title' => 'Editar Objetivo',
            'materia' => $materia,
            'objetivo' => $objetivo,
            'validation' => \Config\Services::validation()
        ];

        return view('client/materias/form_objetivo', $data);
    }

    /**
     * Actualizar un objetivo existente
     */
    public function actualizarObjetivo($materia_id, $objetivo_id)
    {
        $numero_objetivo = trim($this->request->getPost('numero_objetivo'));
        $descripcion = trim($this->request->getPost('descripcion'));
        $resultado = trim($this->request->getPost('resultado'));

        $data = [
            'objetivo_id' => $objetivo_id,
            'materia_id' => $materia_id,
            'numero_objetivo' => $numero_objetivo,
            'descripcion' => $descripcion,
            'resultado' => $resultado // Este campo es para el formulario, no para la BD directamente
        ];

        try {
            $usuarioId = 2;

            // Verificar que la materia pertenece al usuario
            if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
                throw new \RuntimeException('No tienes permiso para modificar esta materia.');
            }

            // Verifica si existe el objetivo
            $objetivo = $this->objetivoModel->find($objetivo_id);
            if (!$objetivo || $objetivo['materia_id'] != $materia_id) {
                throw new \RuntimeException('Objetivo no encontrado.');
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
                return redirectView("materias/objetivos/{$materia_id}", $validation, [['Corrige los errores del formulario', 'error', 'top-end']], $data, 'update');
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

                return redirectView("materias/objetivos/{$materia_id}", null, [['Objetivo actualizado exitosamente', 'success', 'center']], null);

            } catch (\Exception $e) {
                // Revertir cambios si hay algún error
                $db->transRollback();
                throw $e;
            }

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::actualizarObjetivo] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return redirectView("materias/objetivos/{$materia_id}", null, [['Error al actualizar el objetivo: ' . $e->getMessage(), 'error', 'top-end']], $data, 'update');
        }
    }

    /**
     * Eliminar un objetivo
     */
    public function eliminarObjetivo($materia_id, $objetivo_id)
    {
        try {
            $usuarioId = 2;

            // Verificar que la materia pertenece al usuario
            if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
                throw new \RuntimeException('No tienes permiso para modificar esta materia.');
            }

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


    /**
     * SECCIÓN UNIDADES
     */
    public function unidades($materia_id)
    {
        $usuarioId = 2;

        if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
            return redirect()->to('/materias')->with('error', 'Acceso no autorizado');
        }

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
        $usuarioId = 2;

        if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
            return redirect()->to('/materias')->with('error', 'Acceso no autorizado');
        }

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
        $usuarioId = 2;

        // Verificar permisos
        if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
            return redirect()->to('/materias')->with('error', 'Acceso no autorizado');
        }

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
            $usuarioId = 2;

            // Verificar que la materia pertenece al usuario
            if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
                throw new \RuntimeException('No tienes permiso para modificar esta materia.');
            }

            $validation = \Config\Services::validation();

            // Reglas de validación
            $rules = [
                'numero_unidad' => [
                    'label' => 'Número de Unidad',
                    'rules' => 'required|numeric',
                ],
                'nombre' => [
                    'label' => 'Nombre de la Unidad',
                    'rules' => 'required',
                ],
                'objetivo' => [
                    'label' => 'Objetivo de la Unidad',
                    'rules' => 'required',
                ],
            ];

            $validation->setRules($rules);

            if (!$validation->run($data)) {
                return redirectView("materias/unidades/{$materia_id}", $validation, [['Corrige los errores del formulario', 'error', 'top-end']], $data, 'create');
            }

            // Preparar datos para insertar
            $insertData = [
                'materia_id' => $materia_id,
                'numero_unidad' => $numero_unidad,
                'nombre' => $nombre,
                'objetivo' => $objetivo,
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Insertar unidad
            $inserted = $this->unidadModel->insert($insertData);

            if (!$inserted) {
                throw new \RuntimeException('No se pudo guardar la unidad.');
            }

            return redirectView("materias/unidades/{$materia_id}", null, [['Unidad creada exitosamente', 'success', 'center']], null);

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::guardarUnidad] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return redirectView("materias/unidades/{$materia_id}", null, [['Error al crear la unidad: ' . $e->getMessage(), 'error', 'top-end']], $data, 'create');
        }
    }

    /**
     * Actualizar una unidad existente
     */
    public function actualizarUnidad($materia_id, $unidad_id)
    {
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
            $usuarioId = 2;

            // Verificar que la materia pertenece al usuario
            if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
                throw new \RuntimeException('No tienes permiso para modificar esta materia.');
            }

            // Verificar si existe la unidad
            $unidad = $this->unidadModel->find($unidad_id);
            if (!$unidad || $unidad['materia_id'] != $materia_id) {
                throw new \RuntimeException('Unidad no encontrada.');
            }

            $validation = \Config\Services::validation();

            // Reglas de validación
            $rules = [
                'numero_unidad' => [
                    'label' => 'Número de Unidad',
                    'rules' => 'required|numeric',
                ],
                'nombre' => [
                    'label' => 'Nombre de la Unidad',
                    'rules' => 'required|min_length[3]|max_length[100]',
                ],
                'objetivo' => [
                    'label' => 'Objetivo de la Unidad',
                    'rules' => 'required|min_length[10]|max_length[500]',
                ],
            ];

            $validation->setRules($rules);

            if (!$validation->run($data)) {
                return redirectView("materias/unidades/{$materia_id}", $validation, [['Corrige los errores del formulario', 'error', 'top-end']], $data, 'update');
            }

            // Actualizar unidad
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

            return redirectView("materias/unidades/{$materia_id}", null, [['Unidad actualizada exitosamente', 'success', 'center']], null);

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::actualizarUnidad] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return redirectView("materias/unidades/{$materia_id}", null, [['Error al actualizar la unidad: ' . $e->getMessage(), 'error', 'top-end']], $data, 'update');
        }
    }

    /**
     * Eliminar una unidad y sus temas asociados
     */
    public function eliminarUnidad($materia_id, $unidad_id)
    {
        try {
            $usuarioId = 2;

            // Verificar que la materia pertenece al usuario
            if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
                throw new \RuntimeException('No tienes permiso para modificar esta materia.');
            }

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



    /**
     * SECCIÓN TEMAS
     */

    /**
     * Mostrar formulario para nuevo tema
     */
    public function nuevoTema($materia_id, $unidad_id)
    {
        $usuarioId = 2;

        // Verificar permisos
        if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
            return redirect()->to('/materias')->with('error', 'Acceso no autorizado');
        }

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
        $numero_tema = trim($this->request->getPost('numero_tema'));
        $nombre = trim($this->request->getPost('nombre'));

        $data = [
            'unidad_id' => $unidad_id,
            'numero_tema' => $numero_tema,
            'nombre' => $nombre,
        ];

        try {
            $usuarioId = 2;

            // Verificar permisos
            if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
                throw new \RuntimeException('No tienes permiso para modificar esta materia.');
            }

            // Verificar que la unidad pertenece a la materia
            $unidad = $this->unidadModel->find($unidad_id);
            if (!$unidad || $unidad['materia_id'] != $materia_id) {
                throw new \RuntimeException('Unidad no encontrada.');
            }

            $validation = \Config\Services::validation();

            // Reglas de validación
            $rules = [
                'numero_tema' => [
                    'label' => 'Número de Tema',
                    'rules' => 'required|numeric',
                ],
                'nombre' => [
                    'label' => 'Nombre del Tema',
                    'rules' => 'required',
                ],
            ];

            $validation->setRules($rules);

            if (!$validation->run($data)) {
                return redirectView("materias/unidades/{$materia_id}/{$unidad_id}", $validation, [['Corrige los errores del formulario', 'error', 'top-end']], $data, 'create');
            }

            // Preparar datos para insertar
            $insertData = [
                'unidad_id' => $unidad_id,
                'numero_tema' => $numero_tema,
                'nombre' => $nombre,
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Insertar tema
            $inserted = $this->temaModel->insert($insertData);

            if (!$inserted) {
                throw new \RuntimeException('No se pudo guardar el tema.');
            }

            return redirectView("materias/unidades/{$materia_id}", null, [['Tema creado exitosamente', 'success', 'center']], null);

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::guardarTema] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return redirectView("materias/unidades/{$materia_id}/{$unidad_id}", null, [['Error al crear el tema: ' . $e->getMessage(), 'error', 'top-end']], $data, 'create');
        }
    }

    /**
     * Mostrar formulario para editar tema
     */
    public function editarTema($materia_id, $unidad_id, $tema_id)
    {
        $usuarioId = 2;

        // Verificar permisos
        if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
            return redirect()->to('/materias')->with('error', 'Acceso no autorizado');
        }

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
        $numero_tema = trim($this->request->getPost('numero_tema'));
        $nombre = trim($this->request->getPost('nombre'));
        $descripcion = trim($this->request->getPost('descripcion'));

        $data = [
            'tema_id' => $tema_id,
            'unidad_id' => $unidad_id,
            'numero_tema' => $numero_tema,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
        ];

        try {
            $usuarioId = 2;

            // Verificar permisos
            if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
                throw new \RuntimeException('No tienes permiso para modificar esta materia.');
            }

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

            $validation = \Config\Services::validation();

            // Reglas de validación
            $rules = [
                'numero_tema' => [
                    'label' => 'Número de Tema',
                    'rules' => 'required|numeric',
                ],
                'nombre' => [
                    'label' => 'Nombre del Tema',
                    'rules' => 'required|min_length[3]|max_length[100]',
                ],
            ];

            $validation->setRules($rules);

            if (!$validation->run($data)) {
                return redirectView("materias/editar-tema/{$materia_id}/{$unidad_id}/{$tema_id}", $validation, [['Corrige los errores del formulario', 'error', 'top-end']], $data, 'update');
            }

            // Actualizar tema
            $updateData = [
                'numero_tema' => $numero_tema,
                'nombre' => $nombre,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $updated = $this->temaModel->update($tema_id, $updateData);

            if (!$updated) {
                throw new \RuntimeException('No se pudo actualizar el tema.');
            }

            return redirectView("materias/unidades/{$materia_id}", null, [['Tema actualizado exitosamente', 'success', 'center']], null);

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::actualizarTema] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return redirectView("materias/editar-tema/{$materia_id}/{$unidad_id}/{$tema_id}", null, [['Error al actualizar el tema: ' . $e->getMessage(), 'error', 'top-end']], $data, 'update');
        }
    }

    /**
     * Eliminar un tema
     */
    public function eliminarTema($materia_id, $unidad_id, $tema_id)
    {
        try {
            $usuarioId = 2;

            // Verificar permisos
            if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
                throw new \RuntimeException('No tienes permiso para modificar esta materia.');
            }

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
        $usuarioId = 2;

        if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
            return redirect()->to('/materias')->with('error', 'Acceso no autorizado');
        }

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
        $usuarioId = 2;

        if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
            return redirect()->to('/materias')->with('error', 'Acceso no autorizado');
        }

        $materia = $this->materiaModel->find($materia_id);

        $data = [
            'title' => 'Nueva Referencia Bibliográfica',
            'materia' => $materia,
            'validation' => \Config\Services::validation()
        ];

        return view('client/materias/form_bibliografia', $data);
    }


    /**
     * Guardar nueva bibliografía
     */
    public function guardarBibliografia($materia_id)
    {
        $tipo = trim($this->request->getPost('tipo'));
        $autor = trim($this->request->getPost('autor'));
        $titulo = trim($this->request->getPost('titulo'));
        $editorial = trim($this->request->getPost('editorial'));
        $anio = trim($this->request->getPost('anio'));
        $isbn = trim($this->request->getPost('isbn'));
        $enlace = trim($this->request->getPost('enlace'));
        $descripcion = trim($this->request->getPost('descripcion'));

        $data = [
            'materia_id' => $materia_id,
            'tipo' => $tipo,
            'autor' => $autor,
            'titulo' => $titulo,
            'editorial' => $editorial,
            'anio' => $anio,
            'isbn' => $isbn,
            'enlace' => $enlace,
            'descripcion' => $descripcion
        ];

        try {
            $usuarioId = 2;

            // Verificar que la materia pertenece al usuario
            if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
                throw new \RuntimeException('No tienes permiso para modificar esta materia.');
            }

            $validation = \Config\Services::validation();

            // Reglas de validación
            $rules = [
                'tipo' => [
                    'label' => 'Tipo',
                    'rules' => 'required|in_list[basica,complementaria,electronica,otros]',
                ],
                'autor' => [
                    'label' => 'Autor',
                    'rules' => 'required|min_length[3]|max_length[200]',
                ],
                'titulo' => [
                    'label' => 'Título',
                    'rules' => 'required|min_length[3]|max_length[255]',
                ],
                'editorial' => [
                    'label' => 'Editorial',
                    'rules' => 'permit_empty|max_length[100]',
                ],
                'anio' => [
                    'label' => 'Año',
                    'rules' => 'permit_empty|numeric|min_length[4]|max_length[4]',
                ],
                'isbn' => [
                    'label' => 'ISBN',
                    'rules' => 'permit_empty|max_length[20]',
                ],
                'enlace' => [
                    'label' => 'Enlace',
                    'rules' => 'permit_empty|valid_url|max_length[255]',
                ],
                'descripcion' => [
                    'label' => 'Descripción',
                    'rules' => 'permit_empty|max_length[500]',
                ],
            ];

            $validation->setRules($rules);

            if (!$validation->run($data)) {
                return redirectView("materias/nueva-bibliografia/{$materia_id}", $validation, [['Corrige los errores del formulario', 'error', 'top-end']], $data, 'create');
            }

            // Preparar datos para insertar
            $insertData = [
                'materia_id' => $materia_id,
                'tipo' => $tipo,
                'autor' => $autor,
                'titulo' => $titulo,
                'editorial' => $editorial,
                'anio' => $anio,
                'isbn' => $isbn,
                'enlace' => $enlace,
                'descripcion' => $descripcion,
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Insertar bibliografía
            $inserted = $this->bibliografiaModel->insert($insertData);

            if (!$inserted) {
                throw new \RuntimeException('No se pudo guardar la referencia bibliográfica.');
            }

            return redirectView("materias/bibliografia/{$materia_id}", null, [['Referencia bibliográfica guardada exitosamente', 'success', 'center']], null);

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::guardarBibliografia] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return redirectView("materias/nueva-bibliografia/{$materia_id}", null, [['Error al guardar la referencia: ' . $e->getMessage(), 'error', 'top-end']], $data, 'create');
        }
    }

    /**
     * Mostrar formulario para editar bibliografía
     */
    public function editarBibliografia($materia_id, $bibliografia_id)
    {
        $usuarioId = 2;

        // Verificar que la materia pertenece al usuario
        if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
            return redirect()->to('/materias')->with('error', 'Acceso no autorizado');
        }

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
        $tipo = trim($this->request->getPost('tipo'));
        $autor = trim($this->request->getPost('autor'));
        $titulo = trim($this->request->getPost('titulo'));
        $editorial = trim($this->request->getPost('editorial'));
        $anio = trim($this->request->getPost('anio'));
        $isbn = trim($this->request->getPost('isbn'));
        $enlace = trim($this->request->getPost('enlace'));
        $descripcion = trim($this->request->getPost('descripcion'));

        $data = [
            'bibliografia_id' => $bibliografia_id,
            'materia_id' => $materia_id,
            'tipo' => $tipo,
            'autor' => $autor,
            'titulo' => $titulo,
            'editorial' => $editorial,
            'anio' => $anio,
            'isbn' => $isbn,
            'enlace' => $enlace,
            'descripcion' => $descripcion
        ];

        try {
            $usuarioId = 2;

            // Verificar que la materia pertenece al usuario
            if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
                throw new \RuntimeException('No tienes permiso para modificar esta materia.');
            }

            // Verificar que la bibliografía existe y pertenece a la materia
            $bibliografia = $this->bibliografiaModel->find($bibliografia_id);
            if (!$bibliografia || $bibliografia['materia_id'] != $materia_id) {
                throw new \RuntimeException('Referencia bibliográfica no encontrada.');
            }

            $validation = \Config\Services::validation();

            // Reglas de validación (las mismas que en guardar)
            $rules = [
                'tipo' => [
                    'label' => 'Tipo',
                    'rules' => 'required|in_list[basica,complementaria,electronica,otros]',
                ],
                'autor' => [
                    'label' => 'Autor',
                    'rules' => 'required|min_length[3]|max_length[200]',
                ],
                'titulo' => [
                    'label' => 'Título',
                    'rules' => 'required|min_length[3]|max_length[255]',
                ],
                'editorial' => [
                    'label' => 'Editorial',
                    'rules' => 'permit_empty|max_length[100]',
                ],
                'anio' => [
                    'label' => 'Año',
                    'rules' => 'permit_empty|numeric|min_length[4]|max_length[4]',
                ],
                'isbn' => [
                    'label' => 'ISBN',
                    'rules' => 'permit_empty|max_length[20]',
                ],
                'enlace' => [
                    'label' => 'Enlace',
                    'rules' => 'permit_empty|valid_url|max_length[255]',
                ],
                'descripcion' => [
                    'label' => 'Descripción',
                    'rules' => 'permit_empty|max_length[500]',
                ],
            ];

            $validation->setRules($rules);

            if (!$validation->run($data)) {
                return redirectView("materias/editar-bibliografia/{$materia_id}/{$bibliografia_id}", $validation, [['Corrige los errores del formulario', 'error', 'top-end']], $data, 'update');
            }

            // Preparar datos para actualizar
            $updateData = [
                'tipo' => $tipo,
                'autor' => $autor,
                'titulo' => $titulo,
                'editorial' => $editorial,
                'anio' => $anio,
                'isbn' => $isbn,
                'enlace' => $enlace,
                'descripcion' => $descripcion,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Actualizar bibliografía
            $updated = $this->bibliografiaModel->update($bibliografia_id, $updateData);

            if (!$updated) {
                throw new \RuntimeException('No se pudo actualizar la referencia bibliográfica.');
            }

            return redirectView("materias/bibliografia/{$materia_id}", null, [['Referencia bibliográfica actualizada exitosamente', 'success', 'center']], null);

        } catch (\Exception $e) {
            log_message('error', '[MateriasController::actualizarBibliografia] ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return redirectView("materias/editar-bibliografia/{$materia_id}/{$bibliografia_id}", null, [['Error al actualizar la referencia: ' . $e->getMessage(), 'error', 'top-end']], $data, 'update');
        }
    }

    /**
     * Eliminar bibliografía
     */
    public function eliminarBibliografia($materia_id, $bibliografia_id)
    {
        try {
            $usuarioId = 2;

            // Verificar que la materia pertenece al usuario
            if (!$this->materiaModel->belongsToUser($materia_id, $usuarioId)) {
                throw new \RuntimeException('No tienes permiso para modificar esta materia.');
            }

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


    /**
     * Generar documento Word
     */
    // Método para generar el documento Word
    public function generarWord($materia_id)
    {
        try {
            $usuarioId = 2;
            $materia = $this->materiaModel->getMateriaWithRelations($materia_id, $usuarioId);

            if (!$materia) {
                return redirect()->back()->with('error', 'Materia no encontrada o no tienes permiso');
            }

            // Crear nuevo documento Word
            $phpWord = new PhpWord();
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
            $currentUnidad = null;
            foreach ($materia['unidades'] as $unidad) {
                if (!isset($currentUnidad) || $currentUnidad != $unidad['numero_unidad']) {
                    $currentUnidad = $unidad['numero_unidad'];
                    $section->addText("Unidad {$unidad['numero_unidad']}: {$unidad['nombre']}", $fontStyleBold);
                    $section->addText("Objetivo: {$unidad['objetivo']}");
                }

                if (!empty($unidad['tema_nombre'])) {
                    $section->addText("   - Tema {$unidad['numero_tema']}: {$unidad['tema_nombre']}");
                }
            }
            $section->addTextBreak(2);

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
