<?php
require_once __DIR__ . '/../db.php';

$error = '';
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
    $surname = trim($_POST['surname'] ?? '');
    $firstname = trim($_POST['firstname'] ?? '');
    $patronymic = trim($_POST['patronymic'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');

    if (empty($surname) || empty($firstname) || empty($specialization)):
        $error = 'Пожалуйста, заполните все обязательные поля.';
    else:
        try {
            $stmt = $pdo->prepare("UPDATE masters SET surname = ?, firstname = ?, patronymic = ?, specialization = ? WHERE id = ?");
            $stmt->execute([$surname, $firstname, $patronymic, $specialization, $id]);
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Ошибка при обновлении данных: ' . $e->getMessage();
        }
    endif;

    $master['surname'] = $surname;
    $master['firstname'] = $firstname;
    $master['patronymic'] = $patronymic;
    $master['specialization'] = $specialization;
endif;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать мастера</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <a href="index.php" class="btn btn-back back-link">← Назад к списку</a>

        <h1>Редактировать мастера</h1>

        <?php if ($error): ?>
            <div class="message message-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="surname">Фамилия *</label>
                <input type="text" id="surname" name="surname" required value="<?= htmlspecialchars($master['surname']) ?>">
            </div>

            <div class="form-group">
                <label for="firstname">Имя *</label>
                <input type="text" id="firstname" name="firstname" required value="<?= htmlspecialchars($master['firstname']) ?>">
            </div>

            <div class="form-group">
                <label for="patronymic">Отчество</label>
                <input type="text" id="patronymic" name="patronymic" value="<?= htmlspecialchars($master['patronymic'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="specialization">Специализация *</label>
                <input type="text" id="specialization" name="specialization" required value="<?= htmlspecialchars($master['specialization']) ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-submit">Сохранить</button>
                <a href="index.php" class="btn btn-back">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>
