<?php
// ============================================================
// helpers/Validator.php — Input Validation
// ============================================================

class Validator {
    private array $errors = [];
    private array $data   = [];

    public function __construct(array $data) {
        $this->data = $data;
    }

    // Run validation rules
    public function validate(array $rules): self {
        foreach ($rules as $field => $ruleSet) {
            $ruleList = is_string($ruleSet) ? explode('|', $ruleSet) : $ruleSet;
            $value    = $this->data[$field] ?? null;

            foreach ($ruleList as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }
        return $this;
    }

    private function applyRule(string $field, mixed $value, string $rule): void {
        $label = ucfirst(str_replace('_', ' ', $field));

        // Parse rule:param
        [$ruleName, $param] = array_pad(explode(':', $rule, 2), 2, null);

        switch ($ruleName) {
            case 'required':
                if ($value === null || $value === '') {
                    $this->errors[$field][] = "{$label} is required.";
                }
                break;

            case 'email':
                if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = "{$label} must be a valid email address.";
                }
                break;

            case 'min':
                if (strlen((string)$value) < (int)$param) {
                    $this->errors[$field][] = "{$label} must be at least {$param} characters.";
                }
                break;

            case 'max':
                if (strlen((string)$value) > (int)$param) {
                    $this->errors[$field][] = "{$label} must not exceed {$param} characters.";
                }
                break;

            case 'numeric':
                if ($value !== '' && !is_numeric($value)) {
                    $this->errors[$field][] = "{$label} must be a number.";
                }
                break;

            case 'alpha_dash':
                if ($value !== '' && !preg_match('/^[a-zA-Z0-9_\-]+$/', (string)$value)) {
                    $this->errors[$field][] = "{$label} may only contain letters, numbers, dashes, and underscores.";
                }
                break;

            case 'phone':
                if ($value !== '' && !preg_match('/^[\d\s\+\-\(\)]{7,20}$/', (string)$value)) {
                    $this->errors[$field][] = "{$label} must be a valid phone number.";
                }
                break;

            case 'date':
                if ($value !== '' && !strtotime((string)$value)) {
                    $this->errors[$field][] = "{$label} must be a valid date.";
                }
                break;

            case 'in':
                $allowed = explode(',', $param);
                if ($value !== '' && !in_array($value, $allowed)) {
                    $this->errors[$field][] = "{$label} has an invalid value.";
                }
                break;
        }
    }

    public function fails(): bool {
        return !empty($this->errors);
    }

    public function passes(): bool {
        return empty($this->errors);
    }

    public function errors(): array {
        return $this->errors;
    }

    public function firstError(string $field): string {
        return $this->errors[$field][0] ?? '';
    }
}
