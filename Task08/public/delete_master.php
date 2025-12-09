<?php
require_once __DIR__ . '/../db.php';

$id = $_GET['id'] ?? 0;

try {
    $stmt = $pdo->prepare("SELECT * FROM masters WHERE id = ?");
    $stmt->execute([$id]);
    $master = $stmt->fetch();

    if (!$master):
        header('Location: index.php');
        exit;
    endif;
} catch (PDOException $e) {
    die('Ошибка при загрузке данных: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'):
    try {
        $stmt = $pdo->prepare("DELETE FROM masters WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: index.php');
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
    <title>Удалить мастера</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <a href="index.php" class="btn btn-back back-link">← Назад к списку</a>

        <h1>Удалить мастера</h1>

        <?php if (isset($error)): ?>
            <div class="message message-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="delete-confirmation">
            <p>Вы уверены, что хотите удалить следующего мастера?</p>

            <div class="info-box">
                <p><strong>Фамилия:</strong> <?= htmlspecialchars($master['surname']) ?></p>
                <p><strong>Имя:</strong> <?= htmlspecialchars($master['firstname']) ?></p>
                <p><strong>Отчество:</strong> <?= htmlspecialchars($master['patronymic'] ?? '') ?></p>
                <p><strong>Специализация:</strong> <?= htmlspecialchars($master['specialization']) ?></p>
            </div>

            <p><strong>Внимание!</strong> Будут также удалены все записи графика работы и выполненных работ этого мастера.</p>

            <form method="POST">
                <div class="form-actions">
                    <button type="submit" class="btn btn-delete">Да, удалить</button>
                    <a href="index.php" class="btn btn-back">Отмена</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
