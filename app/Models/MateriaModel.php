<?php

namespace App\Models;

use CodeIgniter\Model;

class MateriaModel extends Model
{
    protected $table = 'materias';
    protected $primaryKey = 'materia_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = false;
    protected $allowedFields = [];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = false;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];
    public function getMateriaWithRelations($materia_id, $usuario_id)
    {
        try {
            // Verificar existencia de la materia
            $materia = $this->where('materia_id', $materia_id)
                ->where('usuario_id', $usuario_id)
                ->first();

            if (!$materia) {
                throw new \Exception("No se encontró la materia con ID $materia_id para el usuario ID $usuario_id");
            }

            // Objetivos con resultados
            $builder = $this->db->table('objetivos');
            $materia['objetivos'] = $builder->select('objetivos.*, resultados.descripcion as resultado')
                ->join('resultados', 'resultados.objetivo_id = objetivos.objetivo_id', 'left')
                ->where('objetivos.materia_id', $materia_id)
                ->orderBy('objetivos.numero_objetivo')
                ->get()
                ->getResultArray();

            // Unidades con temas
            $builder = $this->db->table('unidades');
            $unidades = $builder->where('materia_id', $materia_id)
                ->orderBy('numero_unidad')
                ->get()
                ->getResultArray();

            foreach ($unidades as &$unidad) {
                $builder = $this->db->table('temas');
                $unidad['temas'] = $builder->where('unidad_id', $unidad['unidad_id'])
                    ->orderBy('numero_tema')
                    ->get()
                    ->getResultArray();
            }

            $materia['unidades'] = $unidades;

            // Bibliografía
            $builder = $this->db->table('bibliografias');
            $materia['bibliografias'] = $builder->where('materia_id', $materia_id)
                ->get()
                ->getResultArray();

            return $materia;

        } catch (\Throwable $e) {
            // Registrar el error exacto
            log_message('error', 'Error en getMateriaWithRelations: ' . $e->getMessage());
            throw $e; // Lanza el error con el mensaje original para verlo en el controlador o pantalla en entorno de desarrollo
        }
    }


    /**
     * Verificar si una materia pertenece a un usuario
     */
    public function belongsToUser($materia_id, $usuario_id)
    {
        return $this->where('materia_id', $materia_id)
            ->where('usuario_id', $usuario_id)
            ->countAllResults() > 0;
    }

}
