<?php
$timeValue = static fn (mixed $value): string => substr((string) $value, 0, 5);
$isActive = (int) ($reminder['is_active'] ?? 1) === 1;
$repeatValue = (string) ($reminder['repeat_type'] ?? 'daily');
$totalIntervalMinutes = (int) ($reminder['interval_minutes'] ?? 0);
$intervalHours = array_key_exists('interval_hours', $reminder)
    ? (int) $reminder['interval_hours']
    : intdiv($totalIntervalMinutes, 60);
$intervalMinutes = array_key_exists('interval_remainder_minutes', $reminder)
    ? (int) $reminder['interval_remainder_minutes']
    : ($totalIntervalMinutes % 60);
?>
<label>
    <span><?= $e(__('label.title')) ?></span>
    <input type="text" name="title" value="<?= $e($reminder['title'] ?? '') ?>" required>
    <?php if (!empty($errors['title'])): ?>
        <small class="field-error"><?= $e($errors['title']) ?></small>
    <?php endif; ?>
</label>

<label>
    <span><?= $e(__('label.note')) ?></span>
    <textarea name="note" rows="3"><?= $e($reminder['note'] ?? '') ?></textarea>
</label>

<div class="form-grid">
    <label>
        <span><?= $e(__('reminder.remind_time')) ?></span>
        <input type="time" name="remind_time" value="<?= $e($timeValue($reminder['remind_time'] ?? '09:00:00')) ?>" required>
        <?php if (!empty($errors['remind_time'])): ?>
            <small class="field-error"><?= $e($errors['remind_time']) ?></small>
        <?php endif; ?>
    </label>

    <label>
        <span><?= $e(__('reminder.repeat_type')) ?></span>
        <select name="repeat_type" data-reminder-repeat>
            <?php foreach (['none', 'daily', 'weekly', 'interval'] as $repeatType): ?>
                <option value="<?= $e($repeatType) ?>" <?= $repeatValue === $repeatType ? 'selected' : '' ?>>
                    <?= $e(__('reminder.repeat.' . $repeatType)) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
</div>

<label data-reminder-weekly <?= $repeatValue === 'weekly' ? '' : 'hidden' ?>>
    <span><?= $e(__('reminder.day_of_week')) ?></span>
    <select name="day_of_week">
        <option value=""><?= $e(__('reminder.day_optional')) ?></option>
        <?php for ($day = 0; $day <= 6; $day++): ?>
            <option value="<?= $e($day) ?>" <?= ($reminder['day_of_week'] !== null && (int) $reminder['day_of_week'] === $day) ? 'selected' : '' ?>>
                <?= $e(__('day.' . $day)) ?>
            </option>
        <?php endfor; ?>
    </select>
    <?php if (!empty($errors['day_of_week'])): ?>
        <small class="field-error"><?= $e($errors['day_of_week']) ?></small>
    <?php endif; ?>
</label>

<div class="form-grid" data-reminder-interval <?= $repeatValue === 'interval' ? '' : 'hidden' ?>>
    <label>
        <span><?= $e(__('reminder.interval_hours')) ?></span>
        <input type="number" name="interval_hours" value="<?= $e($intervalHours) ?>" min="0" max="23" step="1">
    </label>
    <label>
        <span><?= $e(__('reminder.interval_minutes')) ?></span>
        <input type="number" name="interval_minutes" value="<?= $e($intervalMinutes) ?>" min="0" max="59" step="1">
    </label>
    <?php if (!empty($errors['interval_minutes'])): ?>
        <small class="field-error"><?= $e($errors['interval_minutes']) ?></small>
    <?php endif; ?>
</div>

<label class="checkbox-row">
    <input type="checkbox" name="is_active" value="1" <?= $isActive ? 'checked' : '' ?>>
    <span><?= $e(__('reminder.is_active')) ?></span>
</label>
