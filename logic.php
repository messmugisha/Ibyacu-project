<?php
include 'conn.php';

if (isset($_POST['register'])) {

    // Read form inputs safely
    $username   = $_POST['username'] ?? '';
    $email      = $_POST['email'] ?? '';
    $location   = $_POST['location'] ?? '';
    $contacts   = $_POST['contacts'] ?? '';
    $bio        = $_POST['bio'] ?? '';
    $pass       = $_POST['password'] ?? '';
    $confirm    = $_POST['confirm_password'] ?? '';

    if ($pass !== $confirm) {
        echo "<script>alert('Ijambo banga ntabwo rihuya!'); window.history.back();</script>";
        exit();
    }

   // CHECK IF EMAIl ALREADY EXISTS
    $checkUser = $conn->query("SELECT * FROM users WHERE email = '$email'");

    if ($checkUser->num_rows > 0) {
        echo "<script>alert('Izina ryakoreshejwe!'); window.history.back();</script>";
        exit();
    }

    $hash = password_hash($pass, PASSWORD_DEFAULT);

    $sql = "
        INSERT INTO users 
        (username, email, location, contacts, bio, password)
        VALUES 
        ('$username', '$email', '$location', '$contacts', '$bio', '$hash')
    ";

    if ($conn->query($sql)) {
        echo "<script>alert('Kwiyandikisha byagenze neza!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Habaye ikibazo: {$conn->error}'); window.history.back();</script>";
    }
}
?>


<?php
session_start();
include 'conn.php';

if (isset($_POST['login'])) {

    $username = $_POST['username'] ?? '';
    $password = $_POST['key'] ?? '';

    // Check if user exists
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {

        $user = mysqli_fetch_assoc($result);

        // Verify password
        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];

            echo "<script>alert('KWINJIRA BYAGENZE NEZA'); window.location='dash.php';</script>";

        } else {
            echo "<script>alert('IJAMBO BANGA SIRYO'); window.history.back();</script>";
        }

    } else {
        echo "<script>alert('Bisankaho na Konti ufite'); window.history.back();</script>";
    }
}
?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conn.php';

if (!function_exists('redirectWithProductMessage')) {
    function redirectWithProductMessage(string $status, string $message, string $query = ''): void
    {
        $_SESSION['product_flash'] = [
            'status'  => $status,
            'message' => $message,
        ];

        $location = 'dash.php';
        if ($query !== '') {
            $location .= '?' . $query;
        }

        if (headers_sent()) {
            echo "<script>window.location.href='" . htmlspecialchars($location, ENT_QUOTES) . "';</script>";
            exit();
        }

        header("Location: {$location}");
        exit();
    }
}

if (!function_exists('handleProductImageUpload')) {
    function handleProductImageUpload(array $file, ?string $currentPath = ''): array
    {
        $result = [
            'path'      => $currentPath ?? '',
            'error'     => null,
            'replaced'  => false,
            'old_path'  => $currentPath ?? '',
        ];

        if (empty($file['name']) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $result;
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            $result['error'] = 'Image upload failed. Please try again.';
            return $result;
        }

        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            $result['error'] = 'Image is too large. Maximum allowed size is 2MB.';
            return $result;
        }

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $detectedType = $finfo->file($file['tmp_name']);

        if (!isset($allowed[$detectedType])) {
            $result['error'] = 'Unsupported image format. Use JPG, PNG or WEBP.';
            return $result;
        }

        $targetDir = __DIR__ . '/uploads';
        if (!is_dir($targetDir) && !mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
            $result['error'] = 'Failed to prepare upload directory.';
            return $result;
        }

        $fileName = uniqid('pdt_', true) . '.' . $allowed[$detectedType];
        $destination = $targetDir . '/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $result['error'] = 'Unable to save the uploaded file.';
            return $result;
        }

        $result['path'] = 'uploads/' . $fileName;
        $result['replaced'] = true;

        return $result;
    }
}

$userId = $_SESSION['user_id'] ?? null;

if (isset($_POST['create_product'])) {
    if (!$userId) {
        redirectWithProductMessage('error', 'Please login first.', '');
    }

    $productName = trim($_POST['product_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = (float) ($_POST['price'] ?? 0);
    $quantity    = (int) ($_POST['quantity'] ?? 0);

    if ($productName === '' || $description === '') {
        redirectWithProductMessage('error', 'All fields are required.');
    }

    if ($price < 0 || $quantity < 0) {
        redirectWithProductMessage('error', 'Price and quantity must be positive values.');
    }

    $upload = handleProductImageUpload($_FILES['image'] ?? []);
    if ($upload['error']) {
        redirectWithProductMessage('error', $upload['error']);
    }

    $imagePath = $upload['path'];

    $stmt = $conn->prepare("
        INSERT INTO pdts (user_id, product_name, description, price, quantity, image)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param('issdis', $userId, $productName, $description, $price, $quantity, $imagePath);

    if ($stmt->execute()) {
        redirectWithProductMessage('success', 'Product posted successfully.');
    } else {
        redirectWithProductMessage('error', 'Failed to save product: ' . $stmt->error);
    }
}

if (isset($_POST['update_product'])) {
    if (!$userId) {
        redirectWithProductMessage('error', 'Please login first.');
    }

    $productId   = (int) ($_POST['product_id'] ?? 0);
    $productName = trim($_POST['product_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = (float) ($_POST['price'] ?? 0);
    $quantity    = (int) ($_POST['quantity'] ?? 0);

    if ($productId <= 0) {
        redirectWithProductMessage('error', 'Invalid product.', '');
    }

    $existingStmt = $conn->prepare("
        SELECT image FROM pdts WHERE product_id = ? AND user_id = ? LIMIT 1
    ");
    $existingStmt->bind_param('ii', $productId, $userId);
    $existingStmt->execute();
    $existing = $existingStmt->get_result()->fetch_assoc();
    $existingStmt->close();

    if (!$existing) {
        redirectWithProductMessage('error', 'Product not found.');
    }

    $upload = handleProductImageUpload($_FILES['image'] ?? [], $existing['image'] ?? '');
    if ($upload['error']) {
        redirectWithProductMessage('error', $upload['error'], "edit={$productId}");
    }

    $newImage = $upload['path'];

    $stmt = $conn->prepare("
        UPDATE pdts
        SET product_name = ?, description = ?, price = ?, quantity = ?, image = ?
        WHERE product_id = ? AND user_id = ?
    ");
    $stmt->bind_param('ssdisii', $productName, $description, $price, $quantity, $newImage, $productId, $userId);

    if ($stmt->execute()) {
        if ($upload['replaced'] && !empty($existing['image'])) {
            $oldFile = __DIR__ . '/' . $existing['image'];
            if (is_file($oldFile)) {
                @unlink($oldFile);
            }
        }
        redirectWithProductMessage('success', 'Product updated successfully.');
    } else {
        redirectWithProductMessage('error', 'Failed to update product: ' . $stmt->error, "edit={$productId}");
    }
}

if (isset($_POST['delete_product'])) {
    if (!$userId) {
        redirectWithProductMessage('error', 'Please login first.');
    }

    $productId = (int) ($_POST['product_id'] ?? 0);

    if ($productId <= 0) {
        redirectWithProductMessage('error', 'Invalid product.');
    }

    $productStmt = $conn->prepare("
        SELECT image FROM pdts WHERE product_id = ? AND user_id = ? LIMIT 1
    ");
    $productStmt->bind_param('ii', $productId, $userId);
    $productStmt->execute();
    $product = $productStmt->get_result()->fetch_assoc();
    $productStmt->close();

    if (!$product) {
        redirectWithProductMessage('error', 'Product not found.');
    }

    $deleteStmt = $conn->prepare("DELETE FROM pdts WHERE product_id = ? AND user_id = ?");
    $deleteStmt->bind_param('ii', $productId, $userId);

    if ($deleteStmt->execute()) {
        if (!empty($product['image'])) {
            $file = __DIR__ . '/' . $product['image'];
            if (is_file($file)) {
                @unlink($file);
            }
        }
        redirectWithProductMessage('success', 'Product deleted.');
    } else {
        redirectWithProductMessage('error', 'Failed to delete product: ' . $deleteStmt->error);
    }
}
?>

