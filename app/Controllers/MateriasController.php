<?php

namespace App\Controllers;

use App\Models\MateriaModel;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Paragraph;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class MateriasController extends BaseController
{
    protected $materiaModel;

    public function __construct()
    {
        $this->materiaModel = new MateriaModel();
    }

    // Método para generar el documento Word
    public function generarWord($materia_id)
    {
        try {
            $usuarioId = 2;
            $materia = $this->materiaModel->getMateriaWithRelations($materia_id, $usuarioId);

            if (!$materia) {
                return redirect()->back()->with('error', 'Materia no encontrada o no tienes permiso');
            }

            // Crear nuevo documento Word
            $phpWord = new PhpWord();
            // Agregar esto al inicio del método, después de crear $phpWord
            $phpWord->addTitleStyle(1, ['size' => 16, 'bold' => true], ['alignment' => 'center']);
            $phpWord->addTitleStyle(2, ['size' => 14, 'bold' => true], ['spaceAfter' => 240]);
            $phpWord->addFontStyle('boldStyle', ['bold' => true]);
            $phpWord->addParagraphStyle('justifyStyle', ['alignment' => 'both', 'spaceAfter' => Converter::pointToTwip(8)]);
            $section = $phpWord->addSection();

            // Estilos
            $fontStyleBold = ['bold' => true];
            $fontStyleTitle = ['size' => 14, 'bold' => true];
            $paragraphStyleCenter = ['alignment' => 'center'];
            $paragraphStyleJustify = ['alignment' => 'both'];

            // 1. Encabezado con el ciclo
            $section->addText(strtoupper($materia['ciclo']), $fontStyleTitle, $paragraphStyleCenter);
            $section->addTextBreak(1);

            // 2. Nombre de la materia
            $section->addText($materia['nombre'], $fontStyleTitle, $paragraphStyleCenter);
            $section->addTextBreak(2);

            // 3. Descripción de la asignatura
            $section->addText('Descripción de la asignatura', $fontStyleBold);
            $section->addText($materia['descripcion'], null, $paragraphStyleJustify);
            $section->addTextBreak(2);

            // 4. Objetivos de la Asignatura
            $section->addText('Objetivos de la Asignatura:', $fontStyleBold);
            foreach ($materia['objetivos'] as $objetivo) {
                $section->addText("Objetivo {$objetivo['numero_objetivo']}: {$objetivo['descripcion']}");
                if (!empty($objetivo['resultado'])) {
                    $section->addText("   - Resultado esperado: {$objetivo['resultado']}", ['italic' => true]);
                }
                $section->addTextBreak(1);
            }
            $section->addTextBreak(1);

            // 5. Unidades Didácticas
            $section->addText('Distribución en Unidades Didácticas:', $fontStyleBold);
            $currentUnidad = null;
            foreach ($materia['unidades'] as $unidad) {
                if (!isset($currentUnidad) || $currentUnidad != $unidad['numero_unidad']) {
                    $currentUnidad = $unidad['numero_unidad'];
                    $section->addText("Unidad {$unidad['numero_unidad']}: {$unidad['nombre']}", $fontStyleBold);
                    $section->addText("Objetivo: {$unidad['objetivo']}");
                }

                if (!empty($unidad['tema_nombre'])) {
                    $section->addText("   - Tema {$unidad['numero_tema']}: {$unidad['tema_nombre']}");
                }
            }
            $section->addTextBreak(2);

            // 6. Bibliografía
            $section->addText('Bibliografía', $fontStyleBold);
            foreach ($materia['bibliografias'] as $bibliografia) {
                $section->addText("- {$bibliografia['referencia']}");
                if (!empty($bibliografia['enlace'])) {
                    $section->addText("  Enlace: {$bibliografia['enlace']}", ['color' => '0000FF', 'underline' => 'single']);
                }
            }

            // Guardar el documento temporalmente
            $filename = 'Materia_' . url_title($materia['nombre'], '_') . '.docx';
            $temp_file = tempnam(sys_get_temp_dir(), 'phpword');
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($temp_file);

            // Descargar el archivo
            return $this->response->download($filename, file_get_contents($temp_file));
        } catch (\Throwable $e) {
            log_message('error', 'Error generando documento Word: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Ocurrió un error al generar el documento. Revisa los logs.');
        }
    }

    // ... (otros métodos del controlador que ya tenías)
}
