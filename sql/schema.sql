DROP TABLE IF EXISTS Product;
DROP TABLE IF EXISTS Article;
DROP TABLE IF EXISTS Comment;
DROP TABLE IF EXISTS User_Article;
DROP TABLE IF EXISTS Article_Comment;

CREATE TABLE User (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    username TEXT NOT NULL,
    user_password TEXT NOT NULL,
    user_email TEXT NOT NULL,
    is_administrator BOOLEAN NOT NULL DEFAULT FALSE,
);

CREATE TABLE Article (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    title TEXT NOT NULL,
    content TEXT NOT NULL,
    FOREIGN KEY (created_by) REFERENCES User (id)
);

CREATE TABLE Comment (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    content TEXT NOT NULL,
    FOREIGN KEY (created_by) REFERENCES User (id)
);

CREATE TABLE User_Article (
    user_id INTEGER NOT NULL,
    article_id INTEGER NOT NULL,
    PRIMARY KEY (user_id, article_id),
    FOREIGN KEY (user_id) REFERENCES User (id),
    FOREIGN KEY (article_id) REFERENCES Article (id)
);

CREATE TABLE Article_Comment (
    user_id INTEGER NOT NULL,
    comment_id INTEGER NOT NULL,
    PRIMARY KEY (user_id, comment_id),
    FOREIGN KEY (user_id) REFERENCES User (id),
    FOREIGN KEY (comment_id) REFERENCES Comment (id)
);

