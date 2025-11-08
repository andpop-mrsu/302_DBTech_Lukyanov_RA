#!/bin/bash
chcp 65001

sqlite3 movies_rating.db < db_init.sql

echo "1. Для каждого фильма выведите его название, год выпуска и средний рейтинг. Дополнительно добавьте столбец rank_by_avg_rating, в котором укажите ранг фильма среди всех фильмов по убыванию среднего рейтинга (фильмы с одинаковым средним рейтингом должны получить одинаковый ранг). Используйте оконную функцию RANK() или DENSE_RANK(). В результирующем наборе данных оставить 10 фильмов с наибольшим рангом.
"
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "WITH movie_avg AS (SELECT m.id, m.title, m.year, AVG(r.rating) AS avg_rating FROM movies m JOIN ratings r ON m.id = r.movie_id GROUP BY m.id, m.title, m.year) SELECT title, year, ROUND(avg_rating, 2) AS avg_rating, RANK() OVER (ORDER BY avg_rating DESC) AS rank_by_avg_rating FROM movie_avg ORDER BY rank_by_avg_rating LIMIT 10;"
echo " "

echo "2. С помощью рекурсивного CTE выделить все жанры фильмов, имеющиеся в таблице movies. Для каждого жанра рассчитать средний рейтинг avg_rating фильмов в этом жанре. Выведите genre, avg_rating и ранг жанра по убыванию среднего рейтинга, используя оконную функцию RANK()."
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "WITH RECURSIVE split_genres(movie_id, genre, rest) AS (SELECT id, '', genres || '|' FROM movies UNION ALL SELECT movie_id, SUBSTR(rest, 0, INSTR(rest, '|')), SUBSTR(rest, INSTR(rest, '|') + 1) FROM split_genres WHERE rest <> '') SELECT genre, ROUND(AVG(r.rating), 2) AS avg_rating, RANK() OVER (ORDER BY AVG(r.rating) DESC) AS rank_by_avg_rating FROM split_genres sg JOIN movies m ON m.id = sg.movie_id JOIN ratings r ON r.movie_id = m.id WHERE genre <> '' GROUP BY genre ORDER BY avg_rating DESC;"
echo " "

echo "3. Посчитайте количество фильмов в каждом жанре. Выведите два столбца: genre и movie_count, отсортировав результат по убыванию количества фильмов."
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "WITH RECURSIVE split_genres(movie_id, genre, rest) AS (SELECT id, '', genres || '|' FROM movies UNION ALL SELECT movie_id, SUBSTR(rest, 0, INSTR(rest, '|')), SUBSTR(rest, INSTR(rest, '|') + 1) FROM split_genres WHERE rest <> '') SELECT genre, COUNT(DISTINCT movie_id) AS movie_count FROM split_genres WHERE genre <> '' GROUP BY genre ORDER BY movie_count DESC;"
echo " "

echo "4. Найдите жанры, в которых чаще всего оставляют теги (комментарии). Для этого подсчитайте общее количество записей в таблице tags для фильмов каждого жанра. Выведите genre, tag_count и долю этого жанра в общем числе тегов (tag_share), выраженную в процентах."
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "WITH RECURSIVE split_genres(movie_id, genre, rest) AS (SELECT id, '', genres || '|' FROM movies UNION ALL SELECT movie_id, SUBSTR(rest, 0, INSTR(rest, '|')), SUBSTR(rest, INSTR(rest, '|') + 1) FROM split_genres WHERE rest <> ''), genre_tags AS (SELECT sg.genre, COUNT(t.id) AS tag_count FROM split_genres sg JOIN tags t ON t.movie_id = sg.movie_id WHERE sg.genre <> '' GROUP BY sg.genre), total AS (SELECT SUM(tag_count) AS total_tags FROM genre_tags) SELECT genre, tag_count, ROUND(tag_count * 100.0 / total_tags, 2) AS tag_share FROM genre_tags, total ORDER BY tag_count DESC;"
echo " "

echo "5. Для каждого пользователя рассчитайте: общее количество выставленных оценок, средний выставленный рейтинг, дату первой и последней оценки (по полю timestamp в таблице ratings). Выведите user_id, rating_count, avg_rating, first_rating_date, last_rating_date. Отсортируйте результат по убыванию количества оценок и выведите только 10 первых строк."
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT user_id, COUNT(rating) AS rating_count, ROUND(AVG(rating), 2) AS avg_rating, datetime(MIN(timestamp), 'unixepoch') AS first_rating_date, datetime(MAX(timestamp), 'unixepoch') AS last_rating_date FROM ratings GROUP BY user_id ORDER BY rating_count DESC LIMIT 10;"
echo " "

echo "6. Сегментируйте пользователей по типу поведения:

«Комментаторы» — пользователи, у которых количество тегов (tags) больше количества оценок (ratings),
«Оценщики» — наоборот, оценок больше, чем тегов,
«Активные» — и оценок, и тегов ≥ 10,
«Пассивные» — и оценок, и тегов < 5. Выведите user_id, общее число оценок, общее число тегов и категорию поведения. Используйте CASE."
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "WITH user_activity AS (SELECT u.id AS user_id, COUNT(DISTINCT r.id) AS rating_count, COUNT(DISTINCT t.id) AS tag_count FROM users u LEFT JOIN ratings r ON u.id = r.user_id LEFT JOIN tags t ON u.id = t.user_id GROUP BY u.id) SELECT user_id, rating_count, tag_count, CASE WHEN tag_count > rating_count THEN 'Комментатор' WHEN rating_count > tag_count THEN 'Оценщик' WHEN rating_count >= 10 AND tag_count >= 10 THEN 'Активный' WHEN rating_count < 5 AND tag_count < 5 THEN 'Пассивный' ELSE 'Неопределено' END AS category FROM user_activity ORDER BY user_id;"
echo " "

echo "7. Для каждого пользователя выведите его имя и последний фильм, который он оценил (по времени из ratings.timestamp). Если пользователь не оценивал ни одного фильма, он всё равно должен быть в результате (с NULL в полях фильма). Результат: user_id, name, last_rated_movie_title, last_rating_timestamp."
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "WITH last_rating AS (SELECT user_id, MAX(timestamp) AS last_rating_timestamp FROM ratings GROUP BY user_id) SELECT u.id AS user_id, u.name, m.title AS last_rated_movie_title, datetime(r.timestamp, 'unixepoch') AS last_rating_timestamp FROM users u LEFT JOIN last_rating lr ON lr.user_id = u.id LEFT JOIN ratings r ON r.user_id = u.id AND r.timestamp = lr.last_rating_timestamp LEFT JOIN movies m ON m.id = r.movie_id ORDER BY u.id;"
echo " "
 
