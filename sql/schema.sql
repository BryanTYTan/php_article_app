CREATE SCHEMA article_app;
SET search_path TO article_app, public;

-- User Table
CREATE TABLE users (
    user_id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(50) UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    is_administrator BOOLEAN DEFAULT FALSE
);

-- Article Table
CREATE TABLE articles (
    article_id SERIAL PRIMARY KEY,
    created_at TIMESTAMP DEFAULT NOW(),
    title VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    creator_id INT NOT NULL REFERENCES users(user_id)
);

-- Comment Table
CREATE TABLE comments (
    comment_id SERIAL PRIMARY KEY,
    created_at TIMESTAMP DEFAULT NOW(),
    content TEXT NOT NULL,
    creator_id INT NOT NULL REFERENCES users(user_id),
    article_id INT NOT NULL REFERENCES articles(article_id)
);

-- Indexes to speed up lookups / filters
CREATE INDEX idx_article_title on articles(title);
