<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_db";

// Start a session
session_start();

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to handle form submission and prevent XSS
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$login_error = '';
$signup_error = '';
$signup_success = '';
$borrow_message = '';
$return_message = '';
if (isset($_GET['login_error'])) {
    $login_error = "Invalid credentials. Please try again.";
}
if (isset($_GET['signup_error'])) {
    $signup_error = "Error during signup. Username may already exist.";
}
if (isset($_GET['signup_success'])) {
    $signup_success = "Signup successful! Please login.";
}
if (isset($_GET['borrow_success'])) {
    $borrow_message = "Book borrowed successfully!";
}
if (isset($_GET['borrow_error'])) {
    $borrow_message = "Error borrowing book. It may already be borrowed.";
}
if (isset($_GET['return_success'])) {
    $return_message = "Book returned successfully!";
}
if (isset($_GET['return_error'])) {
    $return_message = "Error returning book.";
}

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $user = test_input($_POST['username']);
    $pass = test_input($_POST['password']);
    
    $stmt = $conn->prepare("SELECT `id`, `username`, `password`, `role` FROM `users` WHERE `username` = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();

    if ($user_data && $user_data['password'] === $pass) {
        $_SESSION['user_id'] = $user_data['id'];
        $_SESSION['username'] = $user_data['username'];
        $_SESSION['role'] = $user_data['role'];
        header("Location: index.php");
        exit();
    } else {
        $redirect_url = "index.php?login_error=1";
        header("Location: " . $redirect_url);
        exit();
    }
    $stmt->close();
}

// Handle signup
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $user = test_input($_POST['username']);
    $pass = test_input($_POST['password']);
    
    $stmt = $conn->prepare("INSERT INTO `users` (`username`, `password`, `role`) VALUES (?, ?, 'user')");
    $stmt->bind_param("ss", $user, $pass);
    
    if ($stmt->execute()) {
        // Auto-login after signup
        $user_id = $conn->insert_id;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $user;
        $_SESSION['role'] = 'user';
        header("Location: index.php");
        exit();
    } else {
        header("Location: index.php?signup_error=1");
        exit();
    }
    $stmt->close();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Handle adding a new book (Admin only)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_book']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    $title = test_input($_POST['title']);
    $author = test_input($_POST['author']);
    $isbn = test_input($_POST['isbn']);
    $published_year = test_input($_POST['published_year']);
    $image_url = test_input($_POST['image_url']);
    $available_copies = 1;

    $stmt = $conn->prepare("INSERT INTO `books` (`title`, `author`, `isbn`, `published_year`, `image_url`, `available_copies`) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $title, $author, $isbn, $published_year, $image_url, $available_copies);

    if ($stmt->execute()) {
        $success_message = "Book added successfully!";
    } else {
        $error_message = "Error adding book: " . $conn->error;
    }
    $stmt->close();
}

// Handle borrowing a book (User only)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['borrow_book']) && isset($_SESSION['role']) && $_SESSION['role'] == 'user') {
    $book_id = test_input($_POST['book_id']);
    $user_id = $_SESSION['user_id'];
    $borrow_date = date('Y-m-d');

    // Check if book is available
    $stmt = $conn->prepare("SELECT available_copies FROM books WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
    $stmt->close();

    if ($book && $book['available_copies'] > 0) {
        // Check if user already borrowed this book
        $stmt = $conn->prepare("SELECT borrow_id FROM borrowings WHERE book_id = ? AND user_id = ? AND return_date IS NULL");
        $stmt->bind_param("ii", $book_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            // Update available copies
            $stmt = $conn->prepare("UPDATE books SET available_copies = available_copies - 1 WHERE id = ?");
            $stmt->bind_param("i", $book_id);
            $stmt->execute();
            $stmt->close();

            // Record borrowing
            $stmt = $conn->prepare("INSERT INTO borrowings (book_id, user_id, borrow_date) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $book_id, $user_id, $borrow_date);
            if ($stmt->execute()) {
                header("Location: index.php?borrow_success=1");
                exit();
            } else {
                header("Location: index.php?borrow_error=1");
                exit();
            }
            $stmt->close();
        } else {
            header("Location: index.php?borrow_error=1");
            exit();
        }
    } else {
        header("Location: index.php?borrow_error=1");
        exit();
    }
}

// Handle returning a book (User only)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['return_book']) && isset($_SESSION['role']) && $_SESSION['role'] == 'user') {
    $book_id = test_input($_POST['book_id']);
    $user_id = $_SESSION['user_id'];
    $return_date = date('Y-m-d');

    // Check if user has borrowed this book
    $stmt = $conn->prepare("SELECT borrow_id FROM borrowings WHERE book_id = ? AND user_id = ? AND return_date IS NULL");
    $stmt->bind_param("ii", $book_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Update return date
        $stmt = $conn->prepare("UPDATE borrowings SET return_date = ? WHERE book_id = ? AND user_id = ? AND return_date IS NULL");
        $stmt->bind_param("sii", $return_date, $book_id, $user_id);
        $stmt->execute();
        $stmt->close();

        // Increment available copies
        $stmt = $conn->prepare("UPDATE books SET available_copies = available_copies + 1 WHERE id = ?");
        $stmt->bind_param("i", $book_id);
        if ($stmt->execute()) {
            header("Location: index.php?return_success=1");
            exit();
        } else {
            header("Location: index.php?return_error=1");
            exit();
        }
        $stmt->close();
    } else {
        header("Location: index.php?return_error=1");
        exit();
    }
}

// Fetch all books from the database
$books = [];
$result = $conn->query("SELECT * FROM books ORDER BY title ASC");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Check if book is borrowed by current user
        if (isset($_SESSION['user_id'])) {
            $stmt = $conn->prepare("SELECT borrow_id FROM borrowings WHERE book_id = ? AND user_id = ? AND return_date IS NULL");
            $stmt->bind_param("ii", $row['id'], $_SESSION['user_id']);
            $stmt->execute();
            $result_borrow = $stmt->get_result();
            $row['is_borrowed_by_user'] = $result_borrow->num_rows > 0;
            $stmt->close();
        } else {
            $row['is_borrowed_by_user'] = false;
        }
        $books[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Playfair+Display:wght@700&display=swap');
        
        body {
            font-family: 'Roboto', sans-serif;
            background: url('https://images.unsplash.com/photo-1512820790803-83ca7348f27e?q=80&w=2670&auto=format&fit=crop') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: -1;
        }
        .container {
            max-width: 1200px;
        }
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: rgba(255, 255, 255, 0.9);
        }
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.25);
        }
        .card-header {
            background-color: #2b6777;
            color: white;
            border-radius: 16px 16px 0 0;
            font-family: 'Playfair Display', serif;
            letter-spacing: 1px;
            padding: 1rem 1.5rem;
        }
        .btn-custom {
            background-color: #2b6777;
            border-color: #2b6777;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #3b8a99;
            border-color: #3b8a99;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-borrow {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-borrow:hover {
            background-color: #218838;
            border-color: #218838;
        }
        .btn-return {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-return:hover {
            background-color: #c82333;
            border-color: #c82333;
        }
        .navbar {
            background-color: #2b6777 !important;
        }
        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            letter-spacing: 2px;
        }
        .book-image {
            width: 150px;
            height: 200px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 0.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-control.rounded-3 {
            border-radius: 10px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
            border-radius: 10px;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-book-open"></i> Library Management
            </a>
            <div class="d-flex ms-auto">
                <?php if (isset($_SESSION['username'])): ?>
                    <span class="navbar-text me-3 text-light">
                        Logged in as: <strong class="text-warning"><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                    </span>
                    <a href="?logout" class="btn btn-outline-light rounded-pill">Logout</a>
                <?php else: ?>
                    <span class="navbar-text me-3 text-light">
                        You are not logged in.
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <?php if (!isset($_SESSION['username'])): ?>
            <h1 class="text-center mb-5 text-light" style="font-family: 'Playfair Display', serif; text-shadow: 2px 2px 4px #000;">Library Management System</h1>
            <div class="row justify-content-center g-4">
                <!-- Admin Login -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header text-center">
                            <h3><i class="fas fa-user-shield me-2"></i>Admin Login</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($login_error)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $login_error; ?>
                                </div>
                            <?php endif; ?>
                            <form action="index.php" method="POST">
                                <div class="mb-3">
                                    <label for="adminUsername" class="form-label">Username</label>
                                    <input type="text" class="form-control rounded-3" id="adminUsername" name="username" value="admin" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="adminPassword" class="form-label">Password</label>
                                    <input type="password" class="form-control rounded-3" id="adminPassword" name="password" required>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" name="login" class="btn btn-custom w-100">Login</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- User Signup/Login -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header text-center">
                            <h3><i class="fas fa-user me-2"></i>User Signup/Login</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($signup_success)): ?>
                                <div class="alert alert-success" role="alert">
                                    <?php echo $signup_success; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($signup_error) || !empty($login_error)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo !empty($signup_error) ? $signup_error : $login_error; ?>
                                </div>
                            <?php endif; ?>
                            <!-- Tabs for Signup/Login -->
                            <ul class="nav nav-tabs mb-3" id="userTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Login</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="signup-tab" data-bs-toggle="tab" data-bs-target="#signup" type="button" role="tab">Signup</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="userTabContent">
                                <!-- Login Tab -->
                                <div class="tab-pane fade show active" id="login" role="tabpanel">
                                    <form action="index.php" method="POST">
                                        <div class="mb-3">
                                            <label for="userUsername" class="form-label">Username</label>
                                            <input type="text" class="form-control rounded-3" id="userUsername" name="username" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="userPassword" class="form-label">Password</label>
                                            <input type="password" class="form-control rounded-3" id="userPassword" name="password" required>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <button type="submit" name="login" class="btn btn-custom w-100">Login</button>
                                        </div>
                                    </form>
                                </div>
                                <!-- Signup Tab -->
                                <div class="tab-pane fade" id="signup" role="tabpanel">
                                    <form action="index.php" method="POST">
                                        <div class="mb-3">
                                            <label for="signupUsername" class="form-label">Username</label>
                                            <input type="text" class="form-control rounded-3" id="signupUsername" name="username" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="signupPassword" class="form-label">Password</label>
                                            <input type="password" class="form-control rounded-3" id="signupPassword" name="password" required>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <button type="submit" name="signup" class="btn btn-custom w-100">Signup</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <h1 class="text-center mb-4 text-secondary" style="font-family: 'Playfair Display', serif;">Book Catalog</h1>

            <?php if (!empty($borrow_message)): ?>
                <div class="alert <?php echo strpos($borrow_message, 'success') !== false ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                    <?php echo $borrow_message; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($return_message)): ?>
                <div class="alert <?php echo strpos($return_message, 'success') !== false ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                    <?php echo $return_message; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="mb-0">Add a New Book</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($success_message)): ?>
                            <div class="alert alert-success" role="alert"><?php echo $success_message; ?></div>
                        <?php endif; ?>
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger" role="alert"><?php echo $error_message; ?></div>
                        <?php endif; ?>
                        <form action="index.php" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control rounded-3" id="title" name="title" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="author" class="form-label">Author</label>
                                    <input type="text" class="form-control rounded-3" id="author" name="author" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="isbn" class="form-label">ISBN</label>
                                    <input type="text" class="form-control rounded-3" id="isbn" name="isbn" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="published_year" class="form-label">Published Year</label>
                                    <input type="number" class="form-control rounded-3" id="published_year" name="published_year" required>
                                </div>
                                <div class="col-12">
                                    <label for="image_url" class="form-label">Image URL</label>
                                    <input type="url" class="form-control rounded-3" id="image_url" name="image_url" required>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="add_book" class="btn btn-custom w-100">Add Book</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <?php if (count($books) > 0): ?>
                    <?php foreach ($books as $book): ?>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="card h-100">
                                <img src="<?php echo htmlspecialchars($book['image_url']); ?>" class="card-img-top book-image mx-auto mt-3" alt="Book Cover">
                                <div class="card-body text-center">
                                    <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                                    <p class="card-text text-muted">by <?php echo htmlspecialchars($book['author']); ?></p>
                                    <p class="card-text"><small>Published: <?php echo htmlspecialchars($book['published_year']); ?></small></p>
                                    <p class="card-text">
                                        <strong>Status: </strong>
                                        <?php echo $book['available_copies'] > 0 ? 'Available' : 'Borrowed'; ?>
                                    </p>
                                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'user'): ?>
                                        <?php if ($book['available_copies'] > 0 && !$book['is_borrowed_by_user']): ?>
                                            <form action="index.php" method="POST">
                                                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                                <button type="submit" name="borrow_book" class="btn btn-borrow btn-sm w-100">Borrow</button>
                                            </form>
                                        <?php elseif ($book['is_borrowed_by_user']): ?>
                                            <form action="index.php" method="POST">
                                                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                                <button type="submit" name="return_book" class="btn btn-return btn-sm w-100">Return</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <div class="alert alert-info" role="alert">
                            No books found in the library.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>