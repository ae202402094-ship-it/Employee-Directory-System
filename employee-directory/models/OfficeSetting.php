<?php
// ============================================================
// models/OfficeSetting.php
// ============================================================

require_once BASE_PATH . '/core/Model.php';

class OfficeSetting extends Model {
    protected string $table = 'office_settings';

    // Get active geofence settings
    public function getActiveSetting(): array {
        $setting = $this->queryOne("SELECT * FROM office_settings WHERE is_active = 1 LIMIT 1");
        if (!$setting) {
            return [
                'office_name'    => 'Main Headquarters',
                'latitude'       => 6.92140000,
                'longitude'      => 122.07900000,
                'radius_meters'  => 200
            ];
        }
        return $setting;
    }

    // Save geofence coordinates
    public function saveSettings(string $name, float $lat, float $lng, int $radius): bool {
        $this->execute("UPDATE office_settings SET is_active = 0");
        return $this->execute(
            "INSERT INTO office_settings (office_name, latitude, longitude, radius_meters, is_active) VALUES (?, ?, ?, ?, 1)",
            [$name, $lat, $lng, $radius]
        );
    }
}
