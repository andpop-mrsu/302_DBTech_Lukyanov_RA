import csv

def extract_year(title):
    start = title.rfind("(")
    end = title.rfind(")")
    if start != -1 and end != -1 and end > start:
        year_str = title[start+1:end]
        if year_str.isdigit() and len(year_str) == 4:
            return int(year_str)
    return None

def main():
    sql_lines = []


    sql_lines.append("DROP TABLE IF EXISTS movies;")
    sql_lines.append("DROP TABLE IF EXISTS ratings;")
    sql_lines.append("DROP TABLE IF EXISTS tags;")
    sql_lines.append("DROP TABLE IF EXISTS users;")


    sql_lines.append("""
CREATE TABLE movies (
    id INTEGER PRIMARY KEY,
    title TEXT NOT NULL,
    year INTEGER,
    genres TEXT
);""")

    sql_lines.append("""
CREATE TABLE ratings (
    id INTEGER PRIMARY KEY,
    user_id INTEGER,
    movie_id INTEGER,
    rating REAL,
    timestamp INTEGER
);""")

    sql_lines.append("""
CREATE TABLE tags (
    id INTEGER PRIMARY KEY,
    user_id INTEGER,
    movie_id INTEGER,
    tag TEXT,
    timestamp INTEGER
);""")

    sql_lines.append("""
CREATE TABLE users (
    id INTEGER PRIMARY KEY,
    name TEXT,
    email TEXT,
    gender TEXT,
    register_date TEXT,
    occupation TEXT
);""")


    with open("dataset/users.txt", "r", encoding="utf-8") as f:
        for line in f:
            parts = line.strip().split("|")
            if len(parts) == 6:
                user_id, name, email, gender, register_date, occupation = parts
                name = name.replace("'", "''")
                email = email.replace("'", "''")
                occupation = occupation.replace("'", "''")
                sql_lines.append(
                    f"INSERT INTO users VALUES ({user_id}, '{name}', '{email}', '{gender}', '{register_date}', '{occupation}');"
                )

    with open("dataset/movies.csv", "r", encoding="utf-8") as f:
        reader = csv.DictReader(f)
        for row in reader:
            movie_id = row["movieId"]
            title = row["title"].replace("'", "''")
            year = extract_year(title)
            genres = row["genres"].replace("'", "''")
            sql_lines.append(f"INSERT INTO movies VALUES ({movie_id}, '{title}', {year if year else 'NULL'}, '{genres}');")


    with open("dataset/ratings.csv", "r", encoding="utf-8") as f:
        reader = csv.DictReader(f)
        i = 1
        for row in reader:
            sql_lines.append(f"INSERT INTO ratings VALUES ({i}, {row['userId']}, {row['movieId']}, {row['rating']}, {row['timestamp']});")
            i += 1


    with open("dataset/tags.csv", "r", encoding="utf-8") as f:
        reader = csv.DictReader(f)
        i = 1
        for row in reader:
            tag = row['tag'].replace("'", "''")
            sql_lines.append(f"INSERT INTO tags VALUES ({i}, {row['userId']}, {row['movieId']}, '{tag}', {row['timestamp']});")
            i += 1

    with open("db_init.sql", "w", encoding="utf-8") as out:
        out.write("\n".join(sql_lines))

if __name__ == "__main__":
    main()
