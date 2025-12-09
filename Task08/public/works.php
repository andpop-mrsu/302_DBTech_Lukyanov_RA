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
    $stmt = $pdo->prepare("SELECT * FROM completed_works WHERE master_id = ? ORDER BY work_date DESC");
    $stmt->execute([$master_id]);
    $works = $stmt->fetchAll();
} catch (PDOException $e) {
    die('Ошибка при загрузке работ: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Выполненные работы</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <a href="index.php" class="btn btn-back back-link">← Назад к списку</a>

        <h1>Выполненные работы</h1>

        <div class="info-box">
            <p><strong>Мастер:</strong> <?= htmlspecialchars($master['surname'] . ' ' . $master['firstname'] . ' ' . ($master['patronymic'] ?? '')) ?></p>
            <p><strong>Специализация:</strong> <?= htmlspecialchars($master['specialization']) ?></p>
        </div>

        <?php if (empty($works)): ?>
            <p>Выполненные работы не найдены.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Название услуги</th>
                        <th>Дата выполнения</th>
                        <th>Стоимость (руб.)</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($works as $work): ?>
                    <tr>
                        <td><?= htmlspecialchars($work['service_name']) ?></td>
                        <td><?= htmlspecialchars(date('d.m.Y', strtotime($work['work_date']))) ?></td>
                        <td><?= number_format($work['cost'], 2, '.', ' ') ?></td>
                        <td class="actions">
                            <a href="edit_work.php?id=<?= $work['id'] ?>" class="btn btn-edit">Редактировать</a>
                            <a href="delete_work.php?id=<?= $work['id'] ?>" class="btn btn-delete">Удалить</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="add-button-container">
            <a href="add_work.php?master_id=<?= $master_id ?>" class="btn btn-add">Добавить выполненную работу</a>
        </div>
    </div>
</body>
</html>
