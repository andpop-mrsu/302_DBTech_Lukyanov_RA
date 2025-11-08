INSERT OR IGNORE INTO users (name, email, gender, register_date, occupation_id)
VALUES
('Копеин Александр Сергеевич', 'kopeinas@gmail.com', 'male', date('now'),
 (SELECT id FROM occupations ORDER BY id LIMIT 1)),
('Лоханов Иван Вячеславович', 'lokhanoviv@gmail.com', 'male', date('now'),
 (SELECT id FROM occupations ORDER BY id LIMIT 1)),
('Лукьянов Роман Александрович', 'lukyanovr@gmail.com', 'male', date('now'),
 (SELECT id FROM occupations ORDER BY id LIMIT 1)),
('Маркин Константин Романович', 'markin@gmail.com', 'male', date('now'),
 (SELECT id FROM occupations ORDER BY id LIMIT 1)),
('Романов Дмитрий Алексеевич', 'romanovdmitry@gmail.com', 'male', date('now'),
 (SELECT id FROM occupations ORDER BY id LIMIT 1));


INSERT OR IGNORE INTO movies (title, year)
VALUES
('Джентльмены удачи (1971)', 1971),
('Бриллиантовая рука (1969)', 1969),
('Операция Ы и другие приключения Шурика (1965)', 1965);


INSERT OR IGNORE INTO genres (name) VALUES ('Comedy');
INSERT OR IGNORE INTO genres (name) VALUES ('Crime');
INSERT OR IGNORE INTO genres (name) VALUES ('Adventure');


INSERT OR IGNORE INTO movie_genres (movie_id, genre_id)
SELECT m.id, g.id FROM movies m JOIN genres g ON g.name = 'Comedy'
WHERE m.title = 'Джентльмены удачи (1971)';

INSERT OR IGNORE INTO movie_genres (movie_id, genre_id)
SELECT m.id, g.id FROM movies m JOIN genres g ON g.name = 'Crime'
WHERE m.title = 'Бриллиантовая рука (1969)';

INSERT OR IGNORE INTO movie_genres (movie_id, genre_id)
SELECT m.id, g.id FROM movies m JOIN genres g ON g.name = 'Adventure'
WHERE m.title = 'Операция Ы и другие приключения Шурика (1965)';


INSERT INTO ratings (user_id, movie_id, rating, timestamp)
SELECT u.id, m.id, 5.0, strftime('%s','now')
FROM users u JOIN movies m ON m.title = 'Джентльмены удачи (1971)'
WHERE u.email = 'lukyanovr@gmail.com'
AND NOT EXISTS (
    SELECT 1 FROM ratings r WHERE r.user_id = u.id AND r.movie_id = m.id
);

INSERT INTO ratings (user_id, movie_id, rating, timestamp)
SELECT u.id, m.id, 4.9, strftime('%s','now')
FROM users u JOIN movies m ON m.title = 'Бриллиантовая рука (1969)'
WHERE u.email = 'lukyanovr@gmail.com'
AND NOT EXISTS (
    SELECT 1 FROM ratings r WHERE r.user_id = u.id AND r.movie_id = m.id
);

INSERT INTO ratings (user_id, movie_id, rating, timestamp)
SELECT u.id, m.id, 4.8, strftime('%s','now')
FROM users u JOIN movies m ON m.title = 'Операция Ы и другие приключения Шурика (1965)'
WHERE u.email = 'lukyanovr@gmail.com'
AND NOT EXISTS (
    SELECT 1 FROM ratings r WHERE r.user_id = u.id AND r.movie_id = m.id
);
