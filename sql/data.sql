INSERT INTO User (username, email, password_hash) VALUES
('alice_author', 'alice@example.com', '$2a$10$abcdefghijklmnopqrstuvwxyzaBcDeFgHiJkLmNoPqRsTu'), 
('bob_writer', 'bob@example.com', '$2a$10$zyxwvtusqrponmlkjihgfedcbaZYXWVUTSRQPONMLKJIHGFE'); 


INSERT INTO posts (author_id, title, content, published_at) VALUES
(1, 'The Joy of SQL', 'SQL is much more than just SELECT. Mastering schema design and DML is key to database management.', '2025-11-01 10:00:00+00'),
(1, 'PostgreSQL Features Deep Dive', 'Exploring JSONB, advanced indexing, and window functions in Postgres.', '2025-11-03 15:30:00+00'),
(2, 'Why Python Loves Postgres', 'A look at the best practices for connecting Django/Flask apps to a robust Postgres backend.', '2025-11-04 09:00:00+00');


INSERT INTO comments (post_id, user_id, comment_text, created_at) VALUES
(1, 2, 'Great post, Alice! I agree that schema design is often overlooked.', '2025-11-01 12:45:00+00'),
(2, 1, 'Thanks for the feedback on window functions. I have an article coming soon!', '2025-11-03 16:00:00+00'),
(3, NULL, 'Fantastic explanation of connection pooling!', '2025-11-04 11:20:00+00');

