<?php
require_once __DIR__ . '/../../config/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$pdo = getDBConnection();
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$is_admin = isAdmin();

$message = '';
$error = '';

// Handle article creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_article'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title) || empty($content)) {
        $error = "Title and content are required";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO articles (title, content, creator_id) VALUES (?, ?, ?)");
            $stmt->execute([$title, $content, $user_id]);
            $message = "Article created successfully!";
        } catch (PDOException $e) {
            $error = "Failed to create article: " . $e->getMessage();
        }
    }
}

// Handle article deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_article'])) {
    $article_id = (int) $_POST['article_id'];

    try {
        // Check ownership or admin status
        $stmt = $pdo->prepare("SELECT creator_id FROM articles WHERE article_id = ?");
        $stmt->execute([$article_id]);
        $article = $stmt->fetch();

        if ($article && ($article['creator_id'] == $user_id || $is_admin)) {
            // Delete comments first (foreign key constraint)
            $stmt = $pdo->prepare("DELETE FROM comments WHERE article_id = ?");
            $stmt->execute([$article_id]);

            // Delete article
            $stmt = $pdo->prepare("DELETE FROM articles WHERE article_id = ?");
            $stmt->execute([$article_id]);
            $message = "Article deleted successfully!";
        } else {
            $error = "You don't have permission to delete this article";
        }
    } catch (PDOException $e) {
        $error = "Failed to delete article: " . $e->getMessage();
    }
}

// Handle comment creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_comment'])) {
    $comment_content = trim($_POST['comment_content']);
    $article_id = (int) $_POST['article_id'];

    if (empty($comment_content)) {
        $error = "Comment cannot be empty";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO comments (content, creator_id, article_id) VALUES (?, ?, ?)");
            $stmt->execute([$comment_content, $user_id, $article_id]);
            $message = "Comment added successfully!";
        } catch (PDOException $e) {
            $error = "Failed to add comment: " . $e->getMessage();
        }
    }
}

// Handle comment deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
    $comment_id = (int) $_POST['comment_id'];

    try {
        // Check ownership or admin status
        $stmt = $pdo->prepare("SELECT creator_id FROM comments WHERE comment_id = ?");
        $stmt->execute([$comment_id]);
        $comment = $stmt->fetch();

        if ($comment && ($comment['creator_id'] == $user_id || $is_admin)) {
            $stmt = $pdo->prepare("DELETE FROM comments WHERE comment_id = ?");
            $stmt->execute([$comment_id]);
            $message = "Comment deleted successfully!";
        } else {
            $error = "You don't have permission to delete this comment";
        }
    } catch (PDOException $e) {
        $error = "Failed to delete comment: " . $e->getMessage();
    }
}

// Fetch all articles with creator info
$articles_query = "
    SELECT a.*, u.username as creator_name 
    FROM articles a 
    JOIN users u ON a.creator_id = u.user_id 
    ORDER BY a.created_at DESC
";
$articles = $pdo->query($articles_query)->fetchAll();

// Fetch comments for each article
$comments_query = "
    SELECT c.*, u.username as creator_name 
    FROM comments c 
    JOIN users u ON c.creator_id = u.user_id 
    ORDER BY c.created_at ASC
";
$all_comments = $pdo->query($comments_query)->fetchAll();

// Group comments by article_id
$comments_by_article = [];
foreach ($all_comments as $comment) {
    $comments_by_article[$comment['article_id']][] = $comment;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Home - Articles</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }

        .navbar {
            background: #4CAF50;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .navbar h1 {
            font-size: 24px;
        }

        .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .navbar .badge {
            background: #2e7d32;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }

        .navbar a:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .message {
            background: #4CAF50;
            color: white;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .error {
            background: #f44336;
            color: white;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .create-article {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .create-article h2 {
            margin-bottom: 15px;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: Arial, sans-serif;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        button {
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        button:hover {
            background: #45a049;
        }

        .btn-danger {
            background: #f44336;
        }

        .btn-danger:hover {
            background: #da190b;
        }

        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
        }

        .articles-list {
            margin-top: 30px;
        }

        .article {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .article-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .article-title {
            font-size: 24px;
            color: #333;
            margin-bottom: 8px;
        }

        .article-meta {
            font-size: 13px;
            color: #777;
        }

        .article-content {
            color: #555;
            line-height: 1.6;
            margin-bottom: 20px;
            white-space: pre-wrap;
        }

        .comments-section {
            border-top: 2px solid #f0f0f0;
            padding-top: 20px;
            margin-top: 20px;
        }

        .comments-section h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
        }

        .comment {
            background: #f9f9f9;
            padding: 12px 15px;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .comment-author {
            font-weight: bold;
            color: #4CAF50;
            font-size: 14px;
        }

        .comment-date {
            font-size: 12px;
            color: #999;
        }

        .comment-content {
            color: #555;
            font-size: 14px;
            white-space: pre-wrap;
        }

        .comment-form {
            margin-top: 15px;
        }

        .comment-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-height: 80px;
            font-family: Arial, sans-serif;
            margin-bottom: 10px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <h1>Article App</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($username); ?>!</span>
            <?php if ($is_admin): ?>
                <span class="badge">ADMIN</span>
            <?php endif; ?>
            <a href="../auth/logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Create Article Form -->
        <div class="create-article">
            <h2>Create New Article</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Title:</label>
                    <input type="text" name="title" required maxlength="100">
                </div>

                <div class="form-group">
                    <label>Content:</label>
                    <textarea name="content" required></textarea>
                </div>

                <button type="submit" name="create_article">Publish Article</button>
            </form>
        </div>

        <!-- Articles List -->
        <div class="articles-list">
            <h2 style="margin-bottom: 20px; color: #333;">All Articles</h2>

            <?php if (empty($articles)): ?>
                <p style="color: #777;">No articles yet. Be the first to create one!</p>
            <?php else: ?>
                <?php foreach ($articles as $article): ?>
                    <div class="article">
                        <div class="article-header">
                            <div>
                                <h3 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h3>
                                <div class="article-meta">
                                    By <?php echo htmlspecialchars($article['creator_name']); ?> •
                                    <?php echo date('F j, Y \a\t g:i A', strtotime($article['created_at'])); ?>
                                </div>
                            </div>

                            <?php if ($article['creator_id'] == $user_id || $is_admin): ?>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this article?');">
                                    <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
                                    <button type="submit" name="delete_article" class="btn-danger btn-small">Delete</button>
                                </form>
                            <?php endif; ?>
                        </div>

                        <div class="article-content"><?php echo htmlspecialchars($article['content']); ?></div>

                        <!-- Comments Section -->
                        <div class="comments-section">
                            <h3>Comments
                                (<?php echo isset($comments_by_article[$article['article_id']]) ? count($comments_by_article[$article['article_id']]) : 0; ?>)
                            </h3>

                            <!-- Display Comments -->
                            <?php if (isset($comments_by_article[$article['article_id']])): ?>
                                <?php foreach ($comments_by_article[$article['article_id']] as $comment): ?>
                                    <div class="comment">
                                        <div class="comment-header">
                                            <div>
                                                <span
                                                    class="comment-author"><?php echo htmlspecialchars($comment['creator_name']); ?></span>
                                                <span class="comment-date">•
                                                    <?php echo date('M j, Y g:i A', strtotime($comment['created_at'])); ?></span>
                                            </div>

                                            <?php if ($comment['creator_id'] == $user_id || $is_admin): ?>
                                                <form method="POST" onsubmit="return confirm('Delete this comment?');"
                                                    style="display: inline;">
                                                    <input type="hidden" name="comment_id" value="<?php echo $comment['comment_id']; ?>">
                                                    <button type="submit" name="delete_comment" class="btn-danger btn-small">Delete</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                        <div class="comment-content"><?php echo htmlspecialchars($comment['content']); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Add Comment Form -->
                            <div class="comment-form">
                                <form method="POST">
                                    <textarea name="comment_content" placeholder="Write a comment..." required></textarea>
                                    <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
                                    <button type="submit" name="create_comment">Post Comment</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>