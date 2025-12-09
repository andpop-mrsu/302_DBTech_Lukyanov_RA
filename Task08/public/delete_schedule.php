<?php
require_once __DIR__ . '/../db.php';

$id = $_GET['id'] ?? 0;


try {
    $stmt = $pdo->prepare("SELECT ws.*, m.surname, m.firstname FROM work_schedule ws
                           JOIN masters m ON ws.master_id = m.id
                           WHERE ws.id = ?");
    $stmt->execute([$id]);
    $schedule = $stmt->fetch();

    if (!$schedule):
        header('Location: index.php');
        exit;
    endif;
} catch (PDOException $e) {
    die('Ошибка при загрузке данных: ' . $e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] === 'POST'):
    try {
        $stmt = $pdo->prepare("DELETE FROM work_schedule WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: schedule.php?master_id=' . $schedule['master_id']);
        exit;
    } catch (PDOException $e) {
        $error = 'Ошибка при удалении: ' . $e->getMessage();
    }
endif;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удалить запись графика</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <a href="schedule.php?master_id=<?= $schedule['master_id'] ?>" class="btn btn-back back-link">← Назад к графику</a>

        <h1>Удалить запись графика</h1>

        <?php if (isset($error)): ?>
            <div class="message message-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="delete-confirmation">
            <p>Вы уверены, что хотите удалить следующую запись?</p>

            <div class="info-box">
                <p><strong>Мастер:</strong> <?= htmlspecialchars($schedule['surname'] . ' ' . $schedule['firstname']) ?></p>
                <p><strong>День недели:</strong> <?= htmlspecialchars($schedule['day_of_week']) ?></p>
                <p><strong>Время работы:</strong> <?= htmlspecialchars($schedule['start_time']) ?> - <?= htmlspecialchars($schedule['end_time']) ?></p>
            </div>

            <form method="POST">
                <div class="form-actions">
                    <button type="submit" class="btn btn-delete">Да, удалить</button>
                    <a href="schedule.php?master_id=<?= $schedule['master_id'] ?>" class="btn btn-back">Отмена</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
