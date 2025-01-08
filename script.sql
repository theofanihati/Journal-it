DROP DATABASE journal_it;
CREATE DATABASE journal_it;
USE journal_it;

CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(255) NOT NULL UNIQUE,
    user_name VARCHAR(255) NOT NULL UNIQUE,
    user_password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE session_list(
    id_session INT(5) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(255) NOT NULL,
    waktu_masuk DATETIME DEFAULT CURRENT_TIMESTAMP,
    waktu_keluar DATETIME
);

CREATE TABLE journals (
    id_journal INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
    image VARCHAR(255) DEFAULT NULL
);

ALTER TABLE session_list ADD FOREIGN KEY(user_name) REFERENCES users(user_name);
ALTER TABLE journals ADD FOREIGN KEY(user_name) REFERENCES users(user_name);

SELECT * FROM users;
SELECT * FROM session_list;
SELECT * FROM journals;

DROP TABLE journals;