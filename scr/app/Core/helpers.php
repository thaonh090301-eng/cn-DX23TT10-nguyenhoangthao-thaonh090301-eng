<?php

declare(strict_types=1);

use App\Core\Lang;

if (!function_exists('__')) {
    function __(string $key, array $replace = []): string
    {
        return Lang::get($key, $replace);
    }
}

if (!function_exists('display_mapped_value')) {
    function display_mapped_value(string $prefix, mixed $value): string
    {
        $label = trim((string) $value);

        if ($label === '') {
            return '';
        }

        $slug = strtolower((string) preg_replace('/[^a-zA-Z0-9]+/', '_', $label));
        $slug = trim($slug, '_');

        if ($slug === '') {
            return $label;
        }

        $key = $prefix . '.' . $slug;
        $translated = __($key);

        return $translated === $key ? $label : $translated;
    }
}

if (!function_exists('display_category_name')) {
    function display_category_name(mixed $name): string
    {
        return display_mapped_value('category', $name);
    }
}

if (!function_exists('display_activity_title')) {
    function display_activity_title(mixed $title): string
    {
        return display_mapped_value('activity', $title);
    }
}

if (!function_exists('format_app_date')) {
    function format_app_date(mixed $value): string
    {
        $timestamp = app_timestamp($value);

        if ($timestamp === null) {
            return '';
        }

        return Lang::locale() === 'en' ? date('m/d/Y', $timestamp) : date('d/m/Y', $timestamp);
    }
}

if (!function_exists('format_app_time')) {
    function format_app_time(mixed $value): string
    {
        $timestamp = app_timestamp($value);

        if ($timestamp === null) {
            return '';
        }

        return Lang::locale() === 'en' ? date('h:i A', $timestamp) : date('H:i', $timestamp);
    }
}

if (!function_exists('format_app_datetime')) {
    function format_app_datetime(mixed $value): string
    {
        $timestamp = app_timestamp($value);

        if ($timestamp === null) {
            return '';
        }

        return Lang::locale() === 'en' ? date('m/d/Y h:i A', $timestamp) : date('d/m/Y H:i', $timestamp);
    }
}

if (!function_exists('format_duration_minutes')) {
    function format_duration_minutes(mixed $minutes): string
    {
        if ($minutes === null || $minutes === '') {
            return '';
        }

        return (int) $minutes . ' ' . __('unit.min');
    }
}

if (!function_exists('format_reminder_interval')) {
    function format_reminder_interval(int $minutes): string
    {
        if ($minutes <= 0) {
            return '';
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0 && $remainingMinutes > 0) {
            return __('reminder.interval_hours_minutes', [
                'hours' => $hours,
                'minutes' => $remainingMinutes,
            ]);
        }

        if ($hours > 0) {
            return __('reminder.interval_hours_only', ['hours' => $hours]);
        }

        return __('reminder.interval_minutes_only', ['minutes' => $remainingMinutes]);
    }
}

if (!function_exists('display_note')) {
    function display_note(mixed $note): string
    {
        $note = trim((string) $note);

        if ($note === '') {
            return '';
        }

        $legacyScheduleNoteEn = 'Automatically ' . 'recorded from ' . 'schedule';
        $legacyScheduleNoteVi = 'Tự động ' . 'ghi nhận theo ' . 'lịch';

        $mappedNotes = [
            'Created from optimizer suggestion.' => __('note.created_from_optimizer'),
            'Tạo từ gợi ý tối ưu.' => __('note.created_from_optimizer'),
            'Created from schedule' => __('time_report.auto_note'),
            'Tạo từ lịch' => __('time_report.auto_note'),
            $legacyScheduleNoteEn => __('time_report.auto_note'),
            $legacyScheduleNoteVi => __('time_report.auto_note'),
            'Tạo từ chế độ tập trung' => __('note.created_from_focus'),
        ];

        return $mappedNotes[$note] ?? $note;
    }
}

if (!function_exists('localized_note')) {
    function localized_note(string $key): string
    {
        return __($key);
    }
}

if (!function_exists('app_timestamp')) {
    function app_timestamp(mixed $value): ?int
    {
        if ($value instanceof DateTimeInterface) {
            return $value->getTimestamp();
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $timestamp = strtotime($value);

        return $timestamp === false ? null : $timestamp;
    }
}
