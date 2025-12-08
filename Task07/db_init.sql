-- Создание базы данных для станции технического обслуживания автомобилей

-- Таблица мастеров (работающих и уволенных)
CREATE TABLE Masters (
    master_id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name TEXT NOT NULL,
    hire_date DATE NOT NULL,
    dismissal_date DATE DEFAULT NULL,
    salary_percentage REAL NOT NULL CHECK(salary_percentage > 0 AND salary_percentage <= 100),
    is_active INTEGER NOT NULL DEFAULT 1 CHECK(is_active IN (0, 1))
);

-- Таблица категорий автомобилей
CREATE TABLE ServiceCategories (
    category_id INTEGER PRIMARY KEY AUTOINCREMENT,
    category_name TEXT NOT NULL UNIQUE
);

-- Таблица услуг
CREATE TABLE Services (
    service_id INTEGER PRIMARY KEY AUTOINCREMENT,
    service_name TEXT NOT NULL,
    category_id INTEGER NOT NULL,
    duration_minutes INTEGER NOT NULL CHECK(duration_minutes > 0),
    price REAL NOT NULL CHECK(price > 0),
    FOREIGN KEY (category_id) REFERENCES ServiceCategories(category_id) ON DELETE RESTRICT
);

-- Таблица связи мастеров и услуг (какие услуги может выполнять мастер)
CREATE TABLE MasterSkills (
    master_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    PRIMARY KEY (master_id, service_id),
    FOREIGN KEY (master_id) REFERENCES Masters(master_id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES Services(service_id) ON DELETE CASCADE
);

-- Таблица записей на обслуживание
CREATE TABLE Appointments (
    appointment_id INTEGER PRIMARY KEY AUTOINCREMENT,
    master_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    client_name TEXT NOT NULL,
    client_phone TEXT NOT NULL,
    appointment_datetime DATETIME NOT NULL,
    status TEXT NOT NULL DEFAULT 'scheduled' CHECK(status IN ('scheduled', 'completed', 'cancelled')),
    FOREIGN KEY (master_id) REFERENCES Masters(master_id) ON DELETE RESTRICT,
    FOREIGN KEY (service_id) REFERENCES Services(service_id) ON DELETE RESTRICT
);

-- Таблица выполненных работ
CREATE TABLE CompletedWorks (
    work_id INTEGER PRIMARY KEY AUTOINCREMENT,
    appointment_id INTEGER NOT NULL,
    master_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    completion_datetime DATETIME NOT NULL,
    actual_price REAL NOT NULL CHECK(actual_price > 0),
    FOREIGN KEY (appointment_id) REFERENCES Appointments(appointment_id) ON DELETE RESTRICT,
    FOREIGN KEY (master_id) REFERENCES Masters(master_id) ON DELETE RESTRICT,
    FOREIGN KEY (service_id) REFERENCES Services(service_id) ON DELETE RESTRICT
);

-- Индексы для оптимизации запросов
CREATE INDEX idx_masters_active ON Masters(is_active);
CREATE INDEX idx_appointments_datetime ON Appointments(appointment_datetime);
CREATE INDEX idx_appointments_master ON Appointments(master_id);
CREATE INDEX idx_completed_works_master ON CompletedWorks(master_id);
CREATE INDEX idx_completed_works_datetime ON CompletedWorks(completion_datetime);

-- Заполнение таблицы категорий автомобилей
INSERT INTO ServiceCategories (category_name) VALUES
('Легковые'),
('Внедорожники'),
('Грузовые'),
('Микроавтобусы');

-- Заполнение таблицы мастеров
INSERT INTO Masters (full_name, hire_date, dismissal_date, salary_percentage, is_active) VALUES
('Иванов Иван Иванович', '2020-01-15', NULL, 35.0, 1),
('Петров Петр Петрович', '2019-05-20', NULL, 40.0, 1),
('Сидоров Сидор Сидорович', '2021-03-10', NULL, 30.0, 1),
('Козлов Андрей Михайлович', '2018-07-01', '2023-08-15', 38.0, 0),
('Морозов Алексей Владимирович', '2022-02-01', NULL, 32.0, 1);

-- Заполнение таблицы услуг
-- Услуги для легковых автомобилей
INSERT INTO Services (service_name, category_id, duration_minutes, price) VALUES
('Замена масла', 1, 30, 1500.00),
('Диагностика двигателя', 1, 60, 2000.00),
('Замена тормозных колодок', 1, 90, 3500.00),
('Развал-схождение', 1, 45, 2500.00),
('Замена свечей зажигания', 1, 40, 1800.00);

-- Услуги для внедорожников
INSERT INTO Services (service_name, category_id, duration_minutes, price) VALUES
('Замена масла', 2, 40, 2000.00),
('Диагностика двигателя', 2, 75, 2500.00),
('Замена тормозных колодок', 2, 120, 5000.00),
('Развал-схождение', 2, 60, 3500.00);

-- Услуги для грузовых автомобилей
INSERT INTO Services (service_name, category_id, duration_minutes, price) VALUES
('Замена масла', 3, 60, 3000.00),
('Диагностика двигателя', 3, 90, 4000.00),
('Ремонт подвески', 3, 180, 8000.00);

-- Услуги для микроавтобусов
INSERT INTO Services (service_name, category_id, duration_minutes, price) VALUES
('Замена масла', 4, 45, 2200.00),
('Диагностика двигателя', 4, 70, 2800.00),
('Замена тормозных колодок', 4, 100, 4200.00);

-- Заполнение таблицы навыков мастеров
-- Иванов - универсал для легковых
INSERT INTO MasterSkills (master_id, service_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5);

-- Петров - работает с внедорожниками и легковыми
INSERT INTO MasterSkills (master_id, service_id) VALUES
(2, 1), (2, 3), (2, 4),
(2, 6), (2, 8), (2, 9);

-- Сидоров - специалист по грузовым
INSERT INTO MasterSkills (master_id, service_id) VALUES
(3, 10), (3, 11), (3, 12);

-- Козлов (уволенный) - работал с микроавтобусами
INSERT INTO MasterSkills (master_id, service_id) VALUES
(4, 13), (4, 14), (4, 15);

-- Морозов - диагностика всех типов
INSERT INTO MasterSkills (master_id, service_id) VALUES
(5, 2), (5, 7), (5, 11), (5, 14);

-- Заполнение таблицы записей
INSERT INTO Appointments (master_id, service_id, client_name, client_phone, appointment_datetime, status) VALUES
(1, 1, 'Смирнов А.В.', '+79161234567', '2024-12-10 10:00:00', 'scheduled'),
(1, 3, 'Кузнецов Д.И.', '+79162345678', '2024-12-10 14:00:00', 'scheduled'),
(2, 6, 'Попов Е.С.', '+79163456789', '2024-12-11 09:00:00', 'scheduled'),
(3, 10, 'Васильев Н.П.', '+79164567890', '2024-12-11 11:00:00', 'scheduled'),
(5, 2, 'Федоров О.К.', '+79165678901', '2024-12-12 10:30:00', 'scheduled'),
(1, 2, 'Михайлов С.Т.', '+79166789012', '2024-12-05 10:00:00', 'completed'),
(2, 8, 'Новиков В.Р.', '+79167890123', '2024-12-06 14:00:00', 'completed'),
(3, 11, 'Волков Г.Л.', '+79168901234', '2024-12-07 09:30:00', 'completed');

-- Заполнение таблицы выполненных работ
INSERT INTO CompletedWorks (appointment_id, master_id, service_id, completion_datetime, actual_price) VALUES
(6, 1, 2, '2024-12-05 11:00:00', 2000.00),
(7, 2, 8, '2024-12-06 15:30:00', 5000.00),
(8, 3, 11, '2024-12-07 11:00:00', 4000.00);

-- Добавление выполненных работ уволенным мастером (для истории)
INSERT INTO Appointments (master_id, service_id, client_name, client_phone, appointment_datetime, status) VALUES
(4, 13, 'Соколов И.М.', '+79169012345', '2023-07-15 10:00:00', 'completed');

INSERT INTO CompletedWorks (appointment_id, master_id, service_id, completion_datetime, actual_price) VALUES
(9, 4, 13, '2023-07-15 10:45:00', 2200.00);
