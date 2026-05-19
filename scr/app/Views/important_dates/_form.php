<?php
$isRepeatYearly = (int) ($importantDate['repeat_yearly'] ?? 0) === 1;
?>
<label>
    <span><?= $e(__('label.title')) ?></span>
    <input type="text" name="title" value="<?= $e($importantDate['title'] ?? '') ?>" required>
    <?php if (!empty($errors['title'])): ?>
        <small class="field-error"><?= $e($errors['title']) ?></small>
    <?php endif; ?>
</label>

<div class="form-grid">
    <label>
        <span><?= $e(__('important_date.event_date')) ?></span>
        <input type="date" name="event_date" value="<?= $e($importantDate['event_date'] ?? date('Y-m-d')) ?>" required>
        <?php if (!empty($errors['event_date'])): ?>
            <small class="field-error"><?= $e($errors['event_date']) ?></small>
        <?php endif; ?>
    </label>

    <label>
        <span><?= $e(__('important_date.type')) ?></span>
        <select name="type">
            <?php foreach ($types as $type): ?>
                <option value="<?= $e($type) ?>" <?= ($importantDate['type'] ?? 'other') === $type ? 'selected' : '' ?>>
                    <?= $e(__('important_date.type.' . $type)) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['type'])): ?>
            <small class="field-error"><?= $e($errors['type']) ?></small>
        <?php endif; ?>
    </label>
</div>

<div class="form-grid">
    <label>
        <span><?= $e(__('important_date.remind_before_days')) ?></span>
        <input type="number" name="remind_before_days" min="0" max="3650" value="<?= $e($importantDate['remind_before_days'] ?? 7) ?>">
        <?php if (!empty($errors['remind_before_days'])): ?>
            <small class="field-error"><?= $e($errors['remind_before_days']) ?></small>
        <?php endif; ?>
    </label>

    <label class="checkbox-row important-date-repeat">
        <input type="checkbox" name="repeat_yearly" value="1" <?= $isRepeatYearly ? 'checked' : '' ?>>
        <span><?= $e(__('important_date.repeat_yearly')) ?></span>
    </label>
</div>

<label>
    <span><?= $e(__('label.note')) ?></span>
    <textarea name="note" rows="4"><?= $e($importantDate['note'] ?? '') ?></textarea>
</label>
