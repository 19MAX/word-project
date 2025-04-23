<?php

namespace App\Models;

use CodeIgniter\Model;

class UnidadModel extends Model
{
    protected $table = 'unidades';
    protected $primaryKey = 'unidad_id';
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
     * Obtener unidades con sus temas
     */
    public function getUnidadesWithTemas($materia_id)
    {
        $unidades = $this->where('materia_id', $materia_id)
            ->orderBy('numero_unidad')
            ->findAll();

        if (!empty($unidades)) {
            $temaModel = new TemaModel();

            foreach ($unidades as &$unidad) {
                $unidad['temas'] = $temaModel->where('unidad_id', $unidad['unidad_id'])
                    ->orderBy('numero_tema')
                    ->findAll();
            }
        }

        return $unidades;
    }
}
