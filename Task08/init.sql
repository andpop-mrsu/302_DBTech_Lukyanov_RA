CREATE TABLE IF NOT EXISTS masters (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    surname TEXT NOT NULL,
    firstname TEXT NOT NULL,
    patronymic TEXT,
    specialization TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS work_schedule (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    master_id INTEGER NOT NULL,
    day_of_week TEXT NOT NULL,
    start_time TEXT NOT NULL,
    end_time TEXT NOT NULL,
    FOREIGN KEY (master_id) REFERENCES masters(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS completed_works (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    master_id INTEGER NOT NULL,
    service_name TEXT NOT NULL,
    work_date DATE NOT NULL,
    cost REAL NOT NULL,
    FOREIGN KEY (master_id) REFERENCES masters(id) ON DELETE CASCADE
);

INSERT INTO masters (surname, firstname, patronymic, specialization) VALUES
    ('Иванов', 'Петр', 'Сергеевич', 'Моторист'),
    ('Петров', 'Алексей', 'Иванович', 'Электрик'),
    ('Сидоров', 'Дмитрий', 'Александрович', 'Кузовщик');

INSERT INTO work_schedule (master_id, day_of_week, start_time, end_time) VALUES
    (1, 'Понедельник', '09:00', '18:00'),
    (1, 'Вторник', '09:00', '18:00'),
    (1, 'Среда', '09:00', '18:00'),
    (2, 'Понедельник', '10:00', '19:00'),
    (2, 'Четверг', '10:00', '19:00'),
    (3, 'Пятница', '08:00', '17:00');

INSERT INTO completed_works (master_id, service_name, work_date, cost) VALUES
    (1, 'Замена масла', '2024-12-01', 2500.00),
    (1, 'Ремонт двигателя', '2024-12-05', 15000.00),
    (2, 'Диагностика электрики', '2024-12-02', 1500.00),
    (3, 'Покраска бампера', '2024-12-03', 8000.00);
