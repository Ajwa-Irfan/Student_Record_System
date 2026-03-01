<?php
ob_start(); 
session_start();

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'student_data_system');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create table if not exists
$createTable = "CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    course VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $createTable);

// Handle Edit - Get student data
$editData = null;
if (isset($_GET['edit'])) {
    $editId = mysqli_real_escape_string($conn, $_GET['edit']);
    $result = mysqli_query($conn, "SELECT * FROM students WHERE id = $editId");
    if (mysqli_num_rows($result) > 0) {
        $editData = mysqli_fetch_assoc($result);
    }
}

// Handle Search
$searchQuery = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $searchQuery = " WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%' OR course LIKE '%$search%'";
}

// Get all students data with search if applied
$query = "SELECT * FROM students" . $searchQuery . " ORDER BY id ASC";
$studentsData = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Record Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
        }

        body {
            background: #f5f5f5;
            padding: 20px;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Header */
        .header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 500;
        }

        .header p {
            font-size: 14px;
            opacity: 0.8;
            margin-top: 5px;
        }

        /* Alerts */
        .alert {
            padding: 12px 20px;
            margin: 20px;
            border-radius: 4px;
            border-left: 4px solid;
        }

        .alert-success {
            background: #dff0d8;
            border-left-color: #3c763d;
            color: #3c763d;
        }

        .alert-error {
            background: #f2dede;
            border-left-color: #a94442;
            color: #a94442;
        }

        /* Form Section */
        .form-section {
            padding: 20px;
            border-bottom: 1px solid #eee;
        }

        .section-title {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3498db;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            background: #3498db;
            color: white;
        }

        .btn:hover {
            background: #2980b9;
        }

        .btn-update {
            background: #27ae60;
        }

        .btn-update:hover {
            background: #229954;
        }

        .btn-cancel {
            background: #95a5a6;
            text-decoration: none;
            display: inline-block;
        }

        .btn-cancel:hover {
            background: #7f8c8d;
        }

        .btn-search {
            background: #f39c12;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
        }

        .btn-search:hover {
            background: #e67e22;
        }

        .btn-reset {
            background: #95a5a6;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .btn-reset:hover {
            background: #7f8c8d;
        }

        /* Edit Section */
        .edit-section {
            background: #f9f9f9;
            margin: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .edit-section h3 {
            font-size: 16px;
            color: #333;
            margin-bottom: 15px;
            font-weight: 500;
        }

        /* Table Section */
        .table-section {
            padding: 20px;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .table-header h2 {
            font-size: 18px;
            color: #333;
            font-weight: 500;
        }

        .search-wrapper {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .search-box {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 250px;
            font-size: 14px;
        }

        .search-form {
            display: flex;
            gap: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #dee2e6;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            color: #555;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .badge {
            background: #e9ecef;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            color: #495057;
        }

        .action-links a {
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 13px;
            margin-right: 5px;
        }

        .edit-link {
            background: #27ae60;
            color: white;
        }

        .delete-link {
            background: #e74c3c;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .search-info {
            background: #f0f7ff;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
            color: #3498db;
        }

        /* Footer */
        .footer {
            background: #f8f9fa;
            padding: 15px 20px;
            border-top: 1px solid #eee;
            font-size: 13px;
            color: #666;
            text-align: center;
            border-radius: 0 0 8px 8px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📚 Student Record Management System</h1>
            <p>Manage your students efficiently</p>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <?php header("refresh:4;url=main.php"); ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                <?php echo $_SESSION['message']; ?>
            </div>
            <?php
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            ?>
        <?php endif; ?>

        <!-- Add Student Form -->
        <div class="form-section">
            <div class="section-title">➕ Add New Student</div>

            <form action="operation.php" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Full Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email Address" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="phone" placeholder="Phone Number">
                    </div>
                    <div class="form-group">
                        <input type="text" name="course" placeholder="Course">
                    </div>
                </div>
                <button type="submit" name="add_student" class="btn">Add Student</button>
            </form>
        </div>

        <!-- Edit Form -->
        <?php if ($editData): ?>
            <div class="edit-section">
                <h3>✏️ Edit Student (ID: <?php echo $editData['id']; ?>)</h3>

                <form action="operation.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">

                    <div class="form-grid">
                        <div class="form-group">
                            <input type="text" name="name" value="<?php echo htmlspecialchars($editData['name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" value="<?php echo htmlspecialchars($editData['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <input type="text" name="phone" value="<?php echo htmlspecialchars($editData['phone']); ?>">
                        </div>
                        <div class="form-group">
                            <input type="text" name="course" value="<?php echo htmlspecialchars($editData['course']); ?>">
                        </div>
                    </div>

                    <button type="submit" name="update_student" class="btn btn-update">Update Student</button>
                    <a href="main.php" class="btn btn-cancel">Cancel</a>
                </form>
            </div>
        <?php endif; ?>

        <!-- Students Table -->
        <div class="table-section">
            <div class="table-header">
                <h2>📋 Students List</h2>

                <!-- PHP Search Form -->
                <div class="search-wrapper">
                    <form method="GET" action="main.php" class="search-form">
                        <input type="text"
                            name="search"
                            class="search-box"
                            placeholder="Search students..."
                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit" class="btn-search">Search</button>
                        <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                            <a href="main.php" class="btn-reset">Reset</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Search Info -->
            <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                <div class="search-info">
                    🔍 Showing results for: "<?php echo htmlspecialchars($_GET['search']); ?>"
                    (<?php echo mysqli_num_rows($studentsData); ?> records found)
                </div>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Course</th>
                        <th>Added On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($studentsData) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($studentsData)): ?>
                            <tr>
                                <td><span class="badge"><?php echo $row['id']; ?></span></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']) ?: '-'; ?></td>
                                <td><?php echo htmlspecialchars($row['course']) ?: '-'; ?></td>
                                <td><?php echo date('d-m-Y', strtotime($row['created_at'])); ?></td>
                                <td class="action-links">
                                    <a href="main.php?edit=<?php echo $row['id']; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>" class="edit-link">Edit</a>
                                    <a href="operation.php?delete=<?php echo $row['id']; ?>" class="delete-link" onclick="return confirm('Delete this student?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="empty-state">
                                <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                                    No students found matching "<?php echo htmlspecialchars($_GET['search']); ?>"
                                <?php else: ?>
                                    No students found. Add your first student!
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Student Record Management System</p>
        </div>
    </div>
</body>

</html>
<?php mysqli_close($conn); ?>