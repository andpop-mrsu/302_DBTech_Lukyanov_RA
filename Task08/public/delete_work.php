<?php
require_once __DIR__ . '/../db.php';

$id = $_GET['id'] ?? 0;


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
    try {
        $stmt = $pdo->prepare("DELETE FROM completed_works WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: works.php?master_id=' . $work['master_id']);
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
    <title>Удалить выполненную работу</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <a href="works.php?master_id=<?= $work['master_id'] ?>" class="btn btn-back back-link">← Назад к списку работ</a>

        <h1>Удалить выполненную работу</h1>

        <?php if (isset($error)): ?>
            <div class="message message-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="delete-confirmation">
            <p>Вы уверены, что хотите удалить следующую запись?</p>

            <div class="info-box">
                <p><strong>Мастер:</strong> <?= htmlspecialchars($work['surname'] . ' ' . $work['firstname']) ?></p>
                <p><strong>Название услуги:</strong> <?= htmlspecialchars($work['service_name']) ?></p>
                <p><strong>Дата выполнения:</strong> <?= htmlspecialchars(date('d.m.Y', strtotime($work['work_date']))) ?></p>
                <p><strong>Стоимость:</strong> <?= number_format($work['cost'], 2, '.', ' ') ?> руб.</p>
            </div>

            <form method="POST">
                <div class="form-actions">
                    <button type="submit" class="btn btn-delete">Да, удалить</button>
                    <a href="works.php?master_id=<?= $work['master_id'] ?>" class="btn btn-back">Отмена</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
