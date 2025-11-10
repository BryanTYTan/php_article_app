-- default_data.sql
-- Insert default users

SET search_path TO article_app;

-- Insert a default Article
INSERT INTO articles(title, content, creator_id)
VALUES
('My First Post', 'Hello world, here you can share your stories!', 1);
