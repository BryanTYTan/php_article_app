-- default_data.sql
-- Insert default users

INSERT INTO users (username, email, password_hash, is_administrator)
VALUES
('admin', 'admin@example.com', 'hash_pass_123', TRUE);

-- Insert a default Article
INSERT INTO articles(title, content, creator_id)
VALUES
('My First Post', "Hello world, here you can share your stories!", 1);
