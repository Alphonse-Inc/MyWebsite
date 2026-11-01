<?php

declare(strict_types=1);

namespace App\Core;

final class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    /** @param array<string, mixed> $data */
    public function __construct(private array $data)
    {
    }

    public function required(string $field, string $label): self
    {
        $value = trim((string) ($this->data[$field] ?? ''));
        if ($value === '') {
            $this->errors[$field] = $label . ' is required.';
        }
        return $this;
    }

    public function max(string $field, int $max, string $label): self
    {
        $value = (string) ($this->data[$field] ?? '');
        if (mb_strlen($value) > $max) {
            $this->errors[$field] = $label . ' must be ' . $max . ' characters or fewer.';
        }
        return $this;
    }

    public function email(string $field, string $label = 'Email'): self
    {
        $value = trim((string) ($this->data[$field] ?? ''));
        if ($value !== '' && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[$field] = $label . ' must be a valid email address.';
        }
        return $this;
    }

    public function httpUrl(string $field, string $label = 'Link'): self
    {
        $value = trim((string) ($this->data[$field] ?? ''));
        if ($value === '') {
            return $this;
        }
        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            $this->errors[$field] = $label . ' must be a valid URL.';
            return $this;
        }
        $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));
        if (!in_array($scheme, ['http', 'https'], true)) {
            $this->errors[$field] = $label . ' must start with http:// or https://.';
        }
        return $this;
    }

    public function slug(string $field): self
    {
        $value = trim((string) ($this->data[$field] ?? ''));
        if ($value !== '' && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value) !== 1) {
            $this->errors[$field] = 'Slug may only contain lowercase letters, numbers, and hyphens.';
        }
        return $this;
    }

    /** @param list<string> $allowed */
    public function in(string $field, array $allowed, string $label): self
    {
        $value = (string) ($this->data[$field] ?? '');
        if ($value !== '' && !in_array($value, $allowed, true)) {
            $this->errors[$field] = $label . ' is not an allowed value.';
        }
        return $this;
    }

    public function year(string $field): self
    {
        $value = trim((string) ($this->data[$field] ?? ''));
        if ($value === '') {
            return $this;
        }
        if (preg_match('/^\d{4}$/', $value) !== 1) {
            $this->errors[$field] = 'Year must be a four-digit year.';
            return $this;
        }
        $year = (int) $value;
        if ($year < 1970 || $year > 2100) {
            $this->errors[$field] = 'Year is out of range.';
        }
        return $this;
    }

    public function level(string $field): self
    {
        $value = trim((string) ($this->data[$field] ?? ''));
        if ($value === '') {
            return $this;
        }
        if (!ctype_digit($value) || (int) $value > 100) {
            $this->errors[$field] = 'Level must be a number from 0 to 100.';
        }
        return $this;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    /** @return array<string, string> */
    public function errors(): array
    {
        return $this->errors;
    }
}
