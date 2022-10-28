<?php 
    // Lấy thông tin đã nhập
    $username = $_POST['username'];
    $email =$_POST['email'];
    $pass1 = $_POST['password'];
    $pass2 = $_POST['password2'];
    // Kết nối db
    $conn = mysqli_connect('localhost','root','','shop');
    if(!$conn){
        die("Không thể kết nối");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        include 'head.php';
        echo '<div style="text-align:center">';
        echo '<h1>Sai Email.</h1>';
        echo '<a href="register.php">Thử lại</a>';
        echo '</div>';
        include 'foot.php';
        return;
    }
    if ($pass1 != $pass2){
        include 'head.php';
        echo '<div style="text-align:center">';
        echo '<h1>2 mật khẩu không khớp.</h1>';
        echo '<a href="register.php">Thử lại</a>';
        echo '</div>';
        include 'foot.php';
        return;
    }
    if (strlen($email) < 3){
        include 'head.php';
        echo '<div style="text-align:center">';
        echo '<h1>Email Quá Ngắn.</h1>';
        echo '<a href="register.php">Thử lại</a>';
        echo '</div>';
        include 'foot.php';
        return;
    }
    $sql = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($conn,$sql);
    if(mysqli_num_rows($result) != 0){
        include 'head.php';
        echo '<div style="text-align:center">';
        echo '<h1>Email đã tồn tại.</h1>';
        echo '<a href="register.php">Thử lại</a>';
        echo '</div>';
        include 'foot.php';
        return;
    }
    $sql = "SELECT * FROM user WHERE username = '$username'";
    $result = mysqli_query($conn,$sql);
    if(mysqli_num_rows($result) != 0){
        include 'head.php';
        echo '<div style="text-align:center">';
        echo '<h1>Tên đăng nhập đã tồn tại.</h1>';
        echo '<a href="register.php">Thử lại</a>';
        echo '</div>';
        include 'foot.php';
        return;
    }
    if (strlen($pass1) < 6){
        include 'head.php';
        echo '<div style="text-align:center">';
        echo '<h1>Mật Khẩu Quá Ngắn.</h1>';
        echo '<a href="register.php">Thử lại</a>';
        echo '</div>';
        include 'foot.php';
        return;
    }
    
    $sql = "SELECT * FROM user WHERE username LIKE $username";
    $result = mysqli_query($conn,$sql);
    if (!$result){

            $pass_md5 = md5($pass1);
            $sql = "INSERT INTO user VALUES ('$username','$pass_md5','$email', '0')";
            $result = mysqli_query($conn, $sql);
            include 'head.php';
            echo '<div style="text-align:center">';
            echo '<h1>Đăng Ký Thành Công.</h1>';
            echo '<a href="login.php">Đăng Nhập</a>';
            echo '</div>';
            include 'foot.php';
            return;
    }else{
        include 'head.php';
        echo '<div style="text-align:center">';
        echo '<h1>Tên người dùng phải có ký tự chữ.</h1>';
        echo '<a href="register.php">Thử lại</a>';
        echo '</div>';
        include 'foot.php';
        return;
    }
?>
<?php include 'foot.php' ?>