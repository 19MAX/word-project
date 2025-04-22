<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TemasSeeder extends Seeder
{
    public function run()
    {
        // Obtener unidades de Matemáticas I
        $unidades = $this->db->table('unidades')
            ->join('materias', 'materias.materia_id = unidades.materia_id')
            ->where('materias.nombre', 'Matemáticas I')
            ->orderBy('unidades.numero_unidad')
            ->get()
            ->getResult();

        $data = [];

        foreach ($unidades as $unidad) {
            if ($unidad->numero_unidad == 1) {
                $data[] = [
                    'unidad_id' => $unidad->unidad_id,
                    'numero_tema' => 1,
                    'nombre' => 'Números y Polinomios reales y complejos',
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                $data[] = [
                    'unidad_id' => $unidad->unidad_id,
                    'numero_tema' => 2,
                    'nombre' => 'Sucesiones de Números reales',
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                // Agregar más temas para la unidad 1
            } elseif ($unidad->numero_unidad == 2) {
                $data[] = [
                    'unidad_id' => $unidad->unidad_id,
                    'numero_tema' => 1,
                    'nombre' => 'Límites funcionales y continuidad de funciones de una y varias variables',
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                // Agregar más temas para la unidad 2
            }
        }

        $this->db->table('temas')->insertBatch($data);
    }
}
