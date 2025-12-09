<?php
require_once __DIR__ . '/../db.php';

$id = $_GET['id'] ?? 0;
$error = '';


try {
    $stmt = $pdo->prepare("SELECT cw.*, m.surname, m.firstname FROM completed_works cw
                           JOIN masters m ON cw.master_id = m.id
                           WHERE cw.id = ?");
    $stmt->execute([$id]);
    $work = $stmt->fetch();

    if (!$work):
        header('Location: index.php');
        exit;
    endif;
} catch (PDOException $e) {
    die('Ошибка при загрузке данных: ' . $e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] === 'POST'):
    $service_name = trim($_POST['service_name'] ?? '');
    $work_date = $_POST['work_date'] ?? '';
    $cost = $_POST['cost'] ?? '';

    if (empty($service_name) || empty($work_date) || empty($cost)):
        $error = 'Пожалуйста, заполните все поля.';
    elseif (!is_numeric($cost) || $cost < 0):
        $error = 'Стоимость должна быть положительным числом.';
    else:
        try {
            $stmt = $pdo->prepare("UPDATE completed_works SET service_name = ?, work_date = ?, cost = ? WHERE id = ?");
            $stmt->execute([$service_name, $work_date, $cost, $id]);
            header('Location: works.php?master_id=' . $work['master_id']);
            exit;
        } catch (PDOException $e) {
            $error = 'Ошибка при обновлении записи: ' . $e->getMessage();
        }
    endif;

    // Обновляем данные для отображения
    $work['service_name'] = $service_name;
    $work['work_date'] = $work_date;
    $work['cost'] = $cost;
endif;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать выполненную работу</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <a href="works.php?master_id=<?= $work['master_id'] ?>" class="btn btn-back back-link">← Назад к списку работ</a>

        <h1>Редактировать выполненную работу</h1>

        <div class="info-box">
            <p><strong>Мастер:</strong> <?= htmlspecialchars($work['surname'] . ' ' . $work['firstname']) ?></p>
        </div>

        <?php if ($error): ?>
            <div class="message message-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="service_name">Название услуги *</label>
                <input type="text" id="service_name" name="service_name" required value="<?= htmlspecialchars($work['service_name']) ?>">
            </div>

            <div class="form-group">
                <label for="work_date">Дата выполнения *</label>
                <input type="date" id="work_date" name="work_date" required value="<?= htmlspecialchars($work['work_date']) ?>">
            </div>

            <div class="form-group">
                <label for="cost">Стоимость (руб.) *</label>
                <input type="number" id="cost" name="cost" step="0.01" min="0" required value="<?= htmlspecialchars($work['cost']) ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-submit">Сохранить</button>
                <a href="works.php?master_id=<?= $work['master_id'] ?>" class="btn btn-back">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>
