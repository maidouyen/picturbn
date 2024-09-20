<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tải lên hình ảnh</title>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center">Tải lên hình ảnh</h2>
    <form action="upload.php" method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow">
        <div class="form-group">
            <label for="fileToUpload">Chọn hình ảnh để tải lên:</label>
            <input type="file" class="form-control-file" name="fileToUpload[]" id="fileToUpload" multiple required>
        </div>

        <div class="form-group">
            <label for="name">Tên hình ảnh:</label>
            <input type="text" class="form-control" name="name" id="name" placeholder="Tên hình ảnh" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Tải lên hình ảnh</button>
    </form>
</div>
</body>
</html>
