<?php

namespace App\Models;

use CodeIgniter\Model;

class ObjetivoModel extends Model
{
    protected $table = 'objetivos';
    protected $primaryKey = 'objetivo_id';
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

    /**
     * Obtener objetivos con sus resultados
     */
    public function getObjetivosWithResultados($materia_id)
    {
        return $this->db->table('objetivos')
            ->select('objetivos.*, resultados.descripcion as resultado')
            ->join('resultados', 'resultados.objetivo_id = objetivos.objetivo_id', 'left')
            ->where('objetivos.materia_id', $materia_id)
            ->orderBy('objetivos.numero_objetivo')
            ->get()
            ->getResultArray();
    }

    public function getObjetivoWithResultado($objetivo_id)
    {
        $objetivo = $this->find($objetivo_id);

        if (!$objetivo) {
            return null;
        }

        // Obtener el resultado asociado (si existe)
        $resultado = $this->db->table('resultados')
            ->where('objetivo_id', $objetivo_id)
            ->get()
            ->getRowArray();

        if ($resultado) {
            $objetivo['resultado'] = $resultado['descripcion'];
        } else {
            $objetivo['resultado'] = '';
        }

        return $objetivo;
    }
}
