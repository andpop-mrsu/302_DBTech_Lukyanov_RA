<?php
require_once __DIR__ . '/../db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST'):
    $surname = trim($_POST['surname'] ?? '');
    $firstname = trim($_POST['firstname'] ?? '');
    $patronymic = trim($_POST['patronymic'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');

    if (empty($surname) || empty($firstname) || empty($specialization)):
        $error = 'Пожалуйста, заполните все обязательные поля.';
    else:
        try {
            $stmt = $pdo->prepare("INSERT INTO masters (surname, firstname, patronymic, specialization) VALUES (?, ?, ?, ?)");
            $stmt->execute([$surname, $firstname, $patronymic, $specialization]);
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Ошибка при добавлении мастера: ' . $e->getMessage();
        }
    endif;
endif;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить мастера</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <a href="index.php" class="btn btn-back back-link">← Назад к списку</a>

        <h1>Добавить нового мастера</h1>

        <?php if ($error): ?>
            <div class="message message-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="surname">Фамилия *</label>
                <input type="text" id="surname" name="surname" required value="<?= htmlspecialchars($_POST['surname'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="firstname">Имя *</label>
                <input type="text" id="firstname" name="firstname" required value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="patronymic">Отчество</label>
                <input type="text" id="patronymic" name="patronymic" value="<?= htmlspecialchars($_POST['patronymic'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="specialization">Специализация *</label>
                <input type="text" id="specialization" name="specialization" required value="<?= htmlspecialchars($_POST['specialization'] ?? '') ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-submit">Добавить</button>
                <a href="index.php" class="btn btn-back">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>
