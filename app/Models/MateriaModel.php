<?php

namespace App\Models;

use CodeIgniter\Model;

class MateriaModel extends Model
{
    protected $table = 'materias';
    protected $primaryKey = 'id';
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

    // Obtener materias con sus relaciones
    public function getMateriaWithRelations($materia_id)
    {
        $materia = $this->find($materia_id);

        if (!$materia) {
            return null;
        }

        // Obtener objetivos con sus resultados
        $builder = $this->db->table('objetivos');
        $materia['objetivos'] = $builder->select('objetivos.*, resultados.descripcion as resultado')
            ->join('resultados', 'resultados.objetivo_id = objetivos.objetivo_id', 'left')
            ->where('objetivos.materia_id', $materia_id)
            ->orderBy('objetivos.numero_objetivo')
            ->get()
            ->getResultArray();

        // Obtener unidades con sus temas
        $builder = $this->db->table('unidades');
        $materia['unidades'] = $builder->select('unidades.*, temas.nombre as tema_nombre, temas.numero_tema')
            ->join('temas', 'temas.unidad_id = unidades.unidad_id', 'left')
            ->where('unidades.materia_id', $materia_id)
            ->orderBy('unidades.numero_unidad')
            ->orderBy('temas.numero_tema')
            ->get()
            ->getResultArray();

        // Obtener bibliografías
        $builder = $this->db->table('bibliografias');
        $materia['bibliografias'] = $builder->where('materia_id', $materia_id)
            ->get()
            ->getResultArray();

        return $materia;
    }
}
