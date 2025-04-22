<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AttendanceModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAttendanceController extends BaseController
{
    protected $attendanceModel;
    protected $userModel;

    public function __construct()
    {
        $this->attendanceModel = new AttendanceModel();
        $this->userModel = new UsersModel();
    }

    public function index()
    {
        // Obtener lista de usuarios para el filtro
        $users = $this->userModel->select('id, name')->orderBy('name', 'ASC')->findAll();

        return view('admin/attendances', [
            'users' => $users
        ]);
    }

    public function getAttendances()
    {
        try {
            $request = $this->request;

            if (!$request->isAJAX()) {
                throw new \RuntimeException('Solicitud no válida');
            }

            // Convertir parámetros a enteros
            $draw = (int) ($request->getPost('draw') ?? 1);
            $start = (int) ($request->getPost('start') ?? 0);
            $length = (int) ($request->getPost('length') ?? 10);
            $searchValue = $request->getPost('search')['value'] ?? '';

            // Filtros
            $dateRange = $request->getPost('dateRange');
            $status = $request->getPost('status');
            $userId = $request->getPost('userId');

            $builder = $this->attendanceModel
                ->select('attendances.*, users.name as user_name')
                ->join('users', 'users.id = attendances.user_id');

            // Filtro de fechas
            if (!empty($dateRange)) {
                $dates = explode(' - ', $dateRange);
                if (count($dates) === 2) {
                    $builder->where('date >=', trim($dates[0]))
                        ->where('date <=', trim($dates[1]));
                }
            }

            // Filtro de estado
            if (!empty($status)) {
                $builder->where('status', $status);
            }

            // Filtro de usuario
            if (!empty($userId) && is_numeric($userId)) {
                $builder->where('user_id', (int) $userId);
            }

            // Búsqueda
            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('users.name', $searchValue)
                    ->orLike('date', $searchValue)
                    ->orLike('time_in', $searchValue)
                    ->orLike('time_out', $searchValue)
                    ->orLike('status', $searchValue)
                    ->groupEnd();
            }

            // Total sin paginación
            $totalRecords = $builder->countAllResults(false);

            // Paginación - asegurarse de usar integers
            $builder->orderBy('date', 'DESC')
                ->orderBy('users.name', 'ASC')
                ->limit($length, $start);

            $attendances = $builder->get()->getResultArray();

            // Formatear datos
            $data = array_map(function ($attendance) {
                return [
                    'id' => $attendance['id'],
                    'user_name' => $attendance['user_name'],
                    'date' => $attendance['date'],
                    'time_in' => $attendance['time_in'] ?? '-',
                    'time_out' => $attendance['time_out'] ?? '-',
                    'worked_hours' => $attendance['worked_hours'] ?? '-',
                    'status' => $attendance['status']
                ];
            }, $attendances);

            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en getAttendances: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'draw' => $request->getPost('draw') ?? 0,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function validateA()
    {
        $request = $this->request;

        if ($request->getPost('id')) {
            // Validación individual
            $attendanceId = $request->getPost('id');
            return $this->validateSingle($attendanceId);
        } elseif ($request->getPost('ids')) {
            // Validación múltiple
            $attendanceIds = $request->getPost('ids');
            return $this->validateMultiple($attendanceIds);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'No se especificaron asistencias para validar'
        ]);
    }

    protected function validateSingle($attendanceId)
    {
        $attendance = $this->attendanceModel->find($attendanceId);

        if (!$attendance) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Asistencia no encontrada'
            ]);
        }

        if ($attendance['status'] === 'validada') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'La asistencia ya está validada'
            ]);
        }

        $this->attendanceModel->update($attendanceId, [
            'status' => 'validada',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Asistencia validada correctamente'
        ]);
    }

    protected function validateMultiple($attendanceIds)
    {
        if (empty($attendanceIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No se seleccionaron asistencias'
            ]);
        }

        // Validar que todas las asistencias existan y estén pendientes
        $pendingAttendances = $this->attendanceModel
            ->whereIn('id', $attendanceIds)
            ->where('status', 'pendiente')
            ->findAll();

        if (count($pendingAttendances) === 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No hay asistencias pendientes para validar'
            ]);
        }

        // Actualizar todas las asistencias seleccionadas
        $this->attendanceModel
            ->whereIn('id', $attendanceIds)
            ->set(['status' => 'validada', 'updated_at' => date('Y-m-d H:i:s')])
            ->update();

        return $this->response->setJSON([
            'success' => true,
            'message' => count($pendingAttendances) . ' asistencias validadas correctamente'
        ]);
    }
}
