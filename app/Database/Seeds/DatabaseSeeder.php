<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
        $this->call('UsersSeeder');
        $this->call('MateriasSeeder');
        $this->call('ObjetivosSeeder');
        $this->call('ResultadosSeeder');
        $this->call('UnidadesSeeder');
        $this->call('TemasSeeder');
        $this->call('BibliografiasSeeder');
    }
}
