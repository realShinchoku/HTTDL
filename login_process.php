<?php
    // Kiểm tra đăng nhập
    session_start();
    if(!isset($_SESSION["username"])){
        header("Location:../index.php");
    }
    // Lấy thông tin đã nhập
    $username = $_POST['username'];
    $pass   = $_POST['password'];
    // Kết nối cơ sở dữ liệu
    $conn = mysqli_connect('localhost','root','','shop');
    if(!$conn){
        die("Không thể kết nối");
    }

    // Mã hóa mật khẩu bằng md5()
    $pass_md5 = md5($pass);
    
    $sql = "SELECT * FROM user WHERE username = '$username' AND password = '$pass_md5'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0){
        $_SESSION["username"] = $username;
        $row = mysqli_fetch_assoc($result);
        if ($row['permission']){
            header("Location:home_admin.php");
        }else{
            header("Location:index.php");
        }
    }else{
        echo '<div style="text-align:center">';
        echo '<h1>Sai tên đăng nhập hoặc mật khẩu.</h1>';
        echo '<a href="login.php">Thử lại</a>';
        echo '</div>';
    }
?>