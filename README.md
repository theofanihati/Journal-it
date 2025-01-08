# Journal-it

It's a journaling sites based on website. Users could coordinate their journaling activity through this site. It will stores user journal's title, description, created date, even if user has an image. 
With this site, user coud do their journaling activities anytime, anywhere, and on any platform (as long as it has internet). 

## Languages and Framework

This project using HTML, CSS, Javascript, and PHP as main language. Also used [Tailwind CSS](https://tailwindcss.com/) as CSS framework.

## Features
- Auth
- Add new journal
- Save journal
- Delete journal
- Edit Journal
- Search

![Journal-it](/public/img/readme_journalit.png)

## How to implement this repo?
- clone it
- add this SQL script to your database
```sql
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
```
- go live/ access it on browser `http://localhost/Journal-it/public/register.php`
