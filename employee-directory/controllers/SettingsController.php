<?php
// ============================================================
// controllers/SettingsController.php
// ============================================================

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/models/OfficeSetting.php';
require_once BASE_PATH . '/models/ActivityLog.php';
require_once BASE_PATH . '/helpers/Auth.php';

class SettingsController extends Controller {

    public function geofence(): void {
        $this->requireAuth();
        if (!Auth::isAdmin() && !Auth::isHR()) {
            $this->redirect('/dashboard');
        }

        $settingModel = new OfficeSetting();
        $setting = $settingModel->getActiveSetting();

        $this->view('settings.geofence', [
            'title'   => 'Geofencing Coordinates Configuration',
            'setting' => $setting
        ]);
    }

    public function saveGeofence(): void {
        $this->requireAuth();
        if (!Auth::isAdmin() && !Auth::isHR()) {
            $this->redirect('/dashboard');
        }

        $name   = $this->input('office_name', 'Main Office');
        $lat    = (float)$this->input('latitude');
        $lng    = (float)$this->input('longitude');
        $radius = (int)$this->input('radius_meters', 200);

        if (!$lat || !$lng || !$radius) {
            $this->redirect('/settings/geofence');
        }

        $settingModel = new OfficeSetting();
        $success = $settingModel->saveSettings($name, $lat, $lng, $radius);

        if ($success) {
            ActivityLog::log('UPDATE_GEOFENCE', "Updated office geofence boundary: {$name} ({$lat}, {$lng}) with radius {$radius}m");
        }

        $this->redirect('/settings/geofence');
    }
}
