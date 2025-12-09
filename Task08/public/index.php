<?php
require_once __DIR__ . '/../db.php';

$stmt = $pdo->query("SELECT * FROM masters ORDER BY surname");
$masters = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>СТО - Список мастеров</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Список мастеров СТО</h1>

        <table>
            <thead>
                <tr>
                    <th>Фамилия</th>
                    <th>Имя</th>
                    <th>Отчество</th>
                    <th>Специализация</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($masters as $master): ?>
                <tr>
                    <td><?= htmlspecialchars($master['surname']) ?></td>
                    <td><?= htmlspecialchars($master['firstname']) ?></td>
                    <td><?= htmlspecialchars($master['patronymic'] ?? '') ?></td>
                    <td><?= htmlspecialchars($master['specialization']) ?></td>
                    <td class="actions">
                        <a href="edit_master.php?id=<?= $master['id'] ?>" class="btn btn-edit">Редактировать</a>
                        <a href="delete_master.php?id=<?= $master['id'] ?>" class="btn btn-delete">Удалить</a>
                        <a href="schedule.php?master_id=<?= $master['id'] ?>" class="btn btn-schedule">График</a>
                        <a href="works.php?master_id=<?= $master['id'] ?>" class="btn btn-works">Выполненные работы</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="add-button-container">
            <a href="add_master.php" class="btn btn-add">Добавить мастера</a>
        </div>
    </div>
</body>
</html>
