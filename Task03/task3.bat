#!/bin/bash
chcp 65001 


sqlite3 movies_rating.db < db_init.sql


echo 1. Фильмы, имеющие хотя бы одну оценку (первые 10, по году и названию)
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT DISTINCT m.title, m.year FROM movies m JOIN ratings r ON m.id = r.movie_id ORDER BY m.year, m.title LIMIT 10;"
echo.

echo 2. Пользователи, чьи фамилии начинаются на 'A' (первые 5, по дате регистрации)
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT id, name, email, gender, register_date, occupation FROM users WHERE substr(name, instr(name,' ')+1,1)='A' ORDER BY register_date LIMIT 5;"
echo.

echo 3. Читаемый список рейтингов (первые 50)
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT u.name AS expert, m.title, m.year, r.rating, strftime('%Y-%m-%d', r.timestamp, 'unixepoch') AS rated_date FROM ratings r JOIN users u ON r.user_id = u.id JOIN movies m ON r.movie_id = m.id ORDER BY u.name, m.title, r.rating LIMIT 50;"
echo.

echo 4. Фильмы с тегами (первые 40)
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT m.title, m.year, t.tag FROM tags t JOIN movies m ON t.movie_id = m.id ORDER BY m.year, m.title, t.tag LIMIT 40;"
echo.

echo 5. Самые свежие фильмы (максимальный год в базе)
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT title, year FROM movies WHERE year = (SELECT MAX(year) FROM movies);"
echo.

echo 6. Драмы после 2005 года, понравившиеся женщинам (оценка больше 4.5)
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT m.title, m.year, COUNT(*) AS high_female_ratings FROM ratings r JOIN users u ON r.user_id = u.id JOIN movies m ON r.movie_id = m.id WHERE m.genres LIKE '%Drama%' AND m.year > 2005 AND u.gender = 'F' AND r.rating >= 4.5 GROUP BY m.id, m.title, m.year ORDER BY m.year, m.title;"
echo.

echo 7. Количество регистраций по годам + годы с максимумом и минимумом
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "WITH reg AS (SELECT strftime('%Y', register_date) AS reg_year, COUNT(*) AS cnt FROM users GROUP BY reg_year) SELECT * FROM reg ORDER BY reg_year;"
echo --- Год(ы) максимальных регистраций ---
sqlite3 movies_rating.db -box -echo "WITH reg AS (SELECT strftime('%Y', register_date) AS reg_year, COUNT(*) AS cnt FROM users GROUP BY reg_year) SELECT reg_year, cnt FROM reg WHERE cnt = (SELECT MAX(cnt) FROM reg);"
echo --- Год(ы) минимальных регистраций ---
sqlite3 movies_rating.db -box -echo "WITH reg AS (SELECT strftime('%Y', register_date) AS reg_year, COUNT(*) AS cnt FROM users GROUP BY reg_year) SELECT reg_year, cnt FROM reg WHERE cnt = (SELECT MIN(cnt) FROM reg);"


pause
