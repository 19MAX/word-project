<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BibliografiasSeeder extends Seeder
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
                'referencia' => 'Cálculo infinitesimal. Volumen 2, Curso práctico (Pérez Carreras, Pedro | Universidad Politécnica de Valencia)',
                'enlace' => 'https://polibuscador.upv.es/discovery/search?institution=UPV&query=any,contains,990000635520203706&vid=34UPV_INST:bibupv',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'materia_id' => $matematicasId,
                'referencia' => 'Cálculo multivariable (Stewart, James)',
                'enlace' => 'https://polibuscador.upv.es/discovery/search?institution=UPV&query=any,contains,990001816770203706&vid=34UPV_INST:bibupv',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            // Agregar más bibliografías según sea necesario
        ];

        $this->db->table('bibliografias')->insertBatch($data);
    }
}
