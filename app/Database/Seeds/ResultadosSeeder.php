<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ResultadosSeeder extends Seeder
{
    public function run()
    {
        // Obtener objetivos de Matemáticas I
        $objetivos = $this->db->table('objetivos')
            ->join('materias', 'materias.materia_id = objetivos.materia_id')
            ->where('materias.nombre', 'Matemáticas I')
            ->orderBy('objetivos.numero_objetivo')
            ->get()
            ->getResult();

        $data = [];

        foreach ($objetivos as $objetivo) {
            $data[] = [
                'objetivo_id' => $objetivo->objetivo_id,
                'descripcion' => $objetivo->numero_objetivo == 1 ?
                    'El estudiante será capaz de analizar y resolver problemas matemáticos que involucren la continuidad y diferenciabilidad de funciones, aplicando criterios para encontrar máximos y mínimos locales y absolutos en funciones de una o varias variables.' :
                    'El estudiante podrá aplicar métodos de integración para resolver problemas de ingeniería, utilizando técnicas de integración simple, impropia y múltiple, con capacidad para interpretar e implementar las soluciones obtenidas en contextos de ingeniería.',
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        $this->db->table('resultados')->insertBatch($data);
    }
}
