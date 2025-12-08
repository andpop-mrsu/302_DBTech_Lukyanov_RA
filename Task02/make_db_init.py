import csv

def extract_year(title):
    start = title.rfind("(")
    end = title.rfind(")")
    if start != -1 and end != -1 and end > start:
        year_str = title[start + 1:end]
        if year_str.isdigit() and len(year_str) == 4:
            return int(year_str)
    return None


def main():
    sql = []

    # Drop tables
    sql += [
        "DROP TABLE IF EXISTS movie_genres;",
        "DROP TABLE IF EXISTS genres;",
        "DROP TABLE IF EXISTS ratings;",
        "DROP TABLE IF EXISTS tags;",
        "DROP TABLE IF EXISTS users;",
        "DROP TABLE IF EXISTS occupations;",
        "DROP TABLE IF EXISTS movies;"
    ]

    # Create normalized tables
    sql.append("""
CREATE TABLE movies (
    id INTEGER PRIMARY KEY,
    title TEXT NOT NULL,
    year INTEGER
);
""")

    sql.append("""
CREATE TABLE genres (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT UNIQUE NOT NULL
);
""")

    sql.append("""
CREATE TABLE movie_genres (
    movie_id INTEGER,
    genre_id INTEGER,
    PRIMARY KEY(movie_id, genre_id),
    FOREIGN KEY(movie_id) REFERENCES movies(id),
    FOREIGN KEY(genre_id) REFERENCES genres(id)
);
""")

    sql.append("""
CREATE TABLE occupations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT UNIQUE NOT NULL
);
""")

    sql.append("""
CREATE TABLE users (
    id INTEGER PRIMARY KEY,
    name TEXT,
    email TEXT,
    gender TEXT,
    register_date TEXT,
    occupation_id INTEGER,
    FOREIGN KEY(occupation_id) REFERENCES occupations(id)
);
""")

    sql.append("""
CREATE TABLE ratings (
    id INTEGER PRIMARY KEY,
    user_id INTEGER,
    movie_id INTEGER,
    rating REAL,
    timestamp INTEGER,
    FOREIGN KEY(user_id) REFERENCES users(id),
    FOREIGN KEY(movie_id) REFERENCES movies(id)
);
""")

    sql.append("""
CREATE TABLE tags (
    id INTEGER PRIMARY KEY,
    user_id INTEGER,
    movie_id INTEGER,
    tag TEXT,
    timestamp INTEGER,
    FOREIGN KEY(user_id) REFERENCES users(id),
    FOREIGN KEY(movie_id) REFERENCES movies(id)
);
""")

    # Dictionaries for normalization
    genre_dict = {}
    occupation_dict = {}
    occupation_counter = 1

    # Users
    with open("dataset/users.txt", "r", encoding="utf-8") as f:
        for line in f:
            parts = line.strip().split("|")
            if len(parts) == 6:
                user_id, name, email, gender, regdate, occupation = parts
                name, email, occupation = (
                    name.replace("'", "''"),
                    email.replace("'", "''"),
                    occupation.replace("'", "''")
                )

                if occupation not in occupation_dict:
                    sql.append(
                        f"INSERT INTO occupations VALUES ({occupation_counter}, '{occupation}');"
                    )
                    occupation_dict[occupation] = occupation_counter
                    occupation_counter += 1

                sql.append(
                    f"INSERT INTO users VALUES ({user_id}, '{name}', '{email}', '{gender}', '{regdate}', {occupation_dict[occupation]});"
                )

    # Movies + genres link-table
    with open("dataset/movies.csv", "r", encoding="utf-8") as f:
        reader = csv.DictReader(f)
        for row in reader:
            movie_id = row["movieId"]
            title = row["title"].replace("'", "''")
            year = extract_year(title)
            genres = row["genres"].split("|")

            sql.append(f"INSERT INTO movies VALUES ({movie_id}, '{title}', {year or 'NULL'});")

            for g in genres:
                g = g.replace("'", "''")
                if g not in genre_dict:
                    genre_dict[g] = len(genre_dict) + 1
                    sql.append(f"INSERT INTO genres VALUES ({genre_dict[g]}, '{g}');")
                sql.append(f"INSERT INTO movie_genres VALUES ({movie_id}, {genre_dict[g]});")

    # Ratings
    with open("dataset/ratings.csv", "r", encoding="utf-8") as f:
        reader = csv.DictReader(f)
        i = 1
        for row in reader:
            sql.append(
                f"INSERT INTO ratings VALUES ({i}, {row['userId']}, {row['movieId']}, {row['rating']}, {row['timestamp']});"
            )
            i += 1

    # Tags
    with open("dataset/tags.csv", "r", encoding="utf-8") as f:
        reader = csv.DictReader(f)
        i = 1
        for row in reader:
            tag = row['tag'].replace("'", "''")
            sql.append(
                f"INSERT INTO tags VALUES ({i}, {row['userId']}, {row['movieId']}, '{tag}', {row['timestamp']});"
            )
            i += 1

    # Save output
    with open("db_init_3nf.sql", "w", encoding="utf-8") as out:
        out.write("\n".join(sql))


if __name__ == "__main__":
    main()
