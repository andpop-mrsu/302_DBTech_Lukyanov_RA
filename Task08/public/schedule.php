<?php
require_once __DIR__ . '/../db.php';

$master_id = $_GET['master_id'] ?? 0;

try {
    $stmt = $pdo->prepare("SELECT * FROM masters WHERE id = ?");
    $stmt->execute([$master_id]);
    $master = $stmt->fetch();

    if (!$master):
        header('Location: index.php');
        exit;
    endif;
} catch (PDOException $e) {
    die('Ошибка при загрузке данных: ' . $e->getMessage());
}

try {
    $stmt = $pdo->prepare("SELECT * FROM work_schedule WHERE master_id = ? ORDER BY
        CASE day_of_week
            WHEN 'Понедельник' THEN 1
            WHEN 'Вторник' THEN 2
            WHEN 'Среда' THEN 3
            WHEN 'Четверг' THEN 4
            WHEN 'Пятница' THEN 5
            WHEN 'Суббота' THEN 6
            WHEN 'Воскресенье' THEN 7
        END");
    $stmt->execute([$master_id]);
    $schedule = $stmt->fetchAll();
} catch (PDOException $e) {
    die('Ошибка при загрузке графика: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>График работы мастера</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <a href="index.php" class="btn btn-back back-link">← Назад к списку</a>

        <h1>График работы мастера</h1>

        <div class="info-box">
            <p><strong>Мастер:</strong> <?= htmlspecialchars($master['surname'] . ' ' . $master['firstname'] . ' ' . ($master['patronymic'] ?? '')) ?></p>
            <p><strong>Специализация:</strong> <?= htmlspecialchars($master['specialization']) ?></p>
        </div>

        <?php if (empty($schedule)): ?>
            <p>График работы не заполнен.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>День недели</th>
                        <th>Начало работы</th>
                        <th>Конец работы</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedule as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['day_of_week']) ?></td>
                        <td><?= htmlspecialchars($item['start_time']) ?></td>
                        <td><?= htmlspecialchars($item['end_time']) ?></td>
                        <td class="actions">
                            <a href="edit_schedule.php?id=<?= $item['id'] ?>" class="btn btn-edit">Редактировать</a>
                            <a href="delete_schedule.php?id=<?= $item['id'] ?>" class="btn btn-delete">Удалить</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="add-button-container">
            <a href="add_schedule.php?master_id=<?= $master_id ?>" class="btn btn-add">Добавить запись в график</a>
        </div>
    </div>
</body>
</html>
