<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ObjetivosSeeder extends Seeder
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
                'numero_objetivo' => 1,
                'descripcion' => 'Desarrollar la capacidad de resolución de problemas matemáticos mediante el uso de herramientas algebraicas y de cálculo, enfocándose en el manejo de funciones de una y varias variables reales, con especial énfasis en la continuidad y diferenciabilidad de funciones.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'materia_id' => $matematicasId,
                'numero_objetivo' => 2,
                'descripcion' => 'Promover el aprendizaje y la aplicación de técnicas de integración, incluyendo integrales de una variable, integrales impropias, integrales dobles y triples, así como su uso en el modelado de problemas de ingeniería.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            // Agregar más objetivos según sea necesario
        ];

        $this->db->table('objetivos')->insertBatch($data);
    }
}
