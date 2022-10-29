<?php
session_start();
include 'head.php' ?>

<div class="row container border shadow" style="margin: auto; margin-top:3%">

  <a href="login.php" style="margin:15px;"><i class="fas fa-arrow-left"></i> Quay lại.</a> 
  <h1 class="text-center fw-bold">Đăng Ký</h1>  

  <form enctype="multipart/form-data" action = "reg_process.php" method = "POST">
    <label class="form-label">Tên người dùng:</label>
    <input type="text" class="form-control" name="username"><br>

    <label class="form-label">Email:</label>
    <input type="text" class="form-control" name="email"><br>

    <label class="form-label">Mật khẩu:</label>
    <input type="password" class="form-control" name="password"><br>

    <label class="form-label">Nhập lại mật khẩu:</label>
    <input type="password" class="form-control" name="password2"><br>
    
    <div style ="text-align: center" >
      <button  class="btn btn-info btn-lg" type="submit" name="btnReg">Đăng ký</button>
    </div><br>
</form>
</div>
    