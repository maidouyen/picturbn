<?php
$target_dir = "uploads/"; // Đảm bảo thư mục này tồn tại
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0755, true);
}

$image_name = $_POST['name'] ?? 'Unnamed Image'; // Gán giá trị mặc định nếu không có

// Kết nối cơ sở dữ liệu
$conn = new mysqli('localhost', 'root', '', 'db_test'); // Cập nhật tên người dùng và mật khẩu nếu cần

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra các tệp hình ảnh
foreach ($_FILES["fileToUpload"]["name"] as $key => $name) {
    $target_file = $target_dir . basename($name);
    $uploadOk = 1; // Khởi tạo lại biến này cho mỗi tệp

    // Kiểm tra xem tệp hình ảnh có hợp lệ không
    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"][$key]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "Tệp $name không phải là hình ảnh.<br>";
            $uploadOk = 0;
            continue; // Bỏ qua tệp này
        }
    }

    // Kiểm tra xem tệp đã tồn tại chưa
    if (file_exists($target_file)) {
        echo "Xin lỗi, tệp $name đã tồn tại. Vui lòng đổi tên tệp khác.<br>";
        $uploadOk = 0;
        continue; // Bỏ qua tệp này
    }

    // Kiểm tra kích thước tệp (giới hạn 5MB)
    if ($_FILES["fileToUpload"]["size"][$key] > 5000000) {
        echo "Xin lỗi, tệp $name của bạn quá lớn.<br>";
        $uploadOk = 0;
        continue; // Bỏ qua tệp này
    }

    // Chỉ cho phép các định dạng tệp nhất định
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        echo "Xin lỗi, chỉ cho phép các tệp JPG, JPEG, PNG & GIF cho tệp $name.<br>";
        $uploadOk = 0;
        continue; // Bỏ qua tệp này
    }

    // Thử tải lên tệp nếu không có lỗi xảy ra
    if ($uploadOk == 0) {
        echo "Xin lỗi, tệp $name của bạn đã không được tải lên.<br>";
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$key], $target_file)) {
            // Chèn vào cơ sở dữ liệu với tên hình ảnh đã nhập
            $stmt = $conn->prepare("INSERT INTO picture (name, url) VALUES (?, ?)");
            $stmt->bind_param("ss", $image_name, $target_file);
            $stmt->execute();
            $stmt->close();

            // Hiển thị hình ảnh vừa tải lên và tên hình ảnh
            echo "<h4>Tên hình ảnh:</h4><p>" . htmlspecialchars($image_name) . "</p>";
            echo "<img src='" . htmlspecialchars($target_file) . "' alt='" . htmlspecialchars($image_name) . "' style='max-width: 500px; max-height: 500px;'><br>";
        } else {
            echo "Xin lỗi, đã có lỗi xảy ra khi tải lên tệp $name của bạn.<br>";
        }
    }
}

// Hiển thị tất cả hình ảnh đã tải lên cùng với tên
echo "<h3>Tất cả hình ảnh đã tải lên:</h3>";
$images = glob($target_dir . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);
if ($images) {
    echo "<div class='container'><div class='row'>"; // Bắt đầu dòng
    foreach ($images as $index => $image) {
        if ($index > 0 && $index % 4 == 0) {
            echo "</div><div class='row'>"; // Kết thúc dòng và bắt đầu dòng mới
        }
        // Lấy tên từ cơ sở dữ liệu
        $stmt = $conn->prepare("SELECT name FROM picture WHERE url = ?");
        $stmt->bind_param("s", $image);
        $stmt->execute();
        $stmt->bind_result($db_image_name);
        $stmt->fetch();
        $stmt->close();

        echo "<div class='col-md-3 text-center' style='margin-bottom: 20px;'>";
        echo "<img src='" . htmlspecialchars($image) . "' alt='Uploaded Image' class='img-thumbnail'>";
        echo "<p>Tên hình ảnh: " . htmlspecialchars($db_image_name) . "</p>"; // Hiển thị tên hình ảnh từ cơ sở dữ liệu
        echo "</div>";
    }
    echo "</div></div>"; // Kết thúc dòng cuối cùng và container
} else {
    echo "Không có hình ảnh nào được tải lên.";
}

// Đóng kết nối
$conn->close();
?>

<style>
    .img-thumbnail {
        width: 100%; /* Đảm bảo hình ảnh chiếm toàn bộ chiều rộng của cột */
        height: 200px; /* Chiều cao cố định */
        object-fit: cover; /* Giữ tỷ lệ hình ảnh và cắt nếu cần */
    }
</style>

<!-- Thêm Bootstrap CSS vào đây -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
