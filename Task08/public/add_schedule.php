<?php
require_once __DIR__ . '/../db.php';

$master_id = $_GET['master_id'] ?? 0;
$error = '';


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


if ($_SERVER['REQUEST_METHOD'] === 'POST'):
    $day_of_week = $_POST['day_of_week'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';

    if (empty($day_of_week) || empty($start_time) || empty($end_time)):
        $error = 'Пожалуйста, заполните все поля.';
    else:
        try {
            $stmt = $pdo->prepare("INSERT INTO work_schedule (master_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
            $stmt->execute([$master_id, $day_of_week, $start_time, $end_time]);
            header('Location: schedule.php?master_id=' . $master_id);
            exit;
        } catch (PDOException $e) {
            $error = 'Ошибка при добавлении записи: ' . $e->getMessage();
        }
    endif;
endif;

$days = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить запись в график</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <a href="schedule.php?master_id=<?= $master_id ?>" class="btn btn-back back-link">← Назад к графику</a>

        <h1>Добавить запись в график</h1>

        <div class="info-box">
            <p><strong>Мастер:</strong> <?= htmlspecialchars($master['surname'] . ' ' . $master['firstname']) ?></p>
        </div>

        <?php if ($error): ?>
            <div class="message message-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="day_of_week">День недели *</label>
                <select id="day_of_week" name="day_of_week" required>
                    <option value="">Выберите день</option>
                    <?php foreach ($days as $day): ?>
                        <option value="<?= $day ?>" <?= (($_POST['day_of_week'] ?? '') === $day) ? 'selected' : '' ?>>
                            <?= $day ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="start_time">Начало работы *</label>
                <input type="time" id="start_time" name="start_time" required value="<?= htmlspecialchars($_POST['start_time'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="end_time">Конец работы *</label>
                <input type="time" id="end_time" name="end_time" required value="<?= htmlspecialchars($_POST['end_time'] ?? '') ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-submit">Добавить</button>
                <a href="schedule.php?master_id=<?= $master_id ?>" class="btn btn-back">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>
