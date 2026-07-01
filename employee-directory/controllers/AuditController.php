<?php
// ============================================================
// controllers/AuditController.php
// ============================================================

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/models/ActivityLog.php';
require_once BASE_PATH . '/helpers/Auth.php';

class AuditController extends Controller {

    public function index(): void {
        $this->requireAuth();
        if (!Auth::isAdmin()) {
            $this->redirect('/dashboard');
        }

        $logModel = new ActivityLog();
        $logs = $logModel->getAllLogs();

        $this->view('audit.logs', [
            'title' => 'System Audit Logs',
            'logs'  => $logs
        ]);
    }
}
