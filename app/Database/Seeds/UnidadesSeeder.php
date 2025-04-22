<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UnidadesSeeder extends Seeder
{
    public function run()
    {
        // Obtener ID de Matemáticas I
        $matematicasId = $this->db->table('materias')
            ->where('nombre', 'Matemáticas I')
            ->get()
            ->getRow()
            ->materia_id;

        $data = [
            [
                'materia_id' => $matematicasId,
                'numero_unidad' => 1,
                'nombre' => 'Cálculo Diferencial y Funciones Reales',
                'objetivo' => 'Desarrollar la capacidad para identificar y trabajar con funciones reales y complejas, introduciendo los conceptos de continuidad y diferenciabilidad en funciones de una o varias variables, fundamentales para la resolución de problemas en ingeniería.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'materia_id' => $matematicasId,
                'numero_unidad' => 2,
                'nombre' => 'Extremos de Funciones y Cálculo de Optimización',
                'objetivo' => 'Capacitar a los estudiantes para encontrar y clasificar los máximos y mínimos de funciones, tanto en una variable como en varias, aplicando conceptos de optimización que son esenciales en la ingeniería y en la toma de decisiones.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            // Agregar más unidades según sea necesario
        ];

        $this->db->table('unidades')->insertBatch($data);
    }
}
