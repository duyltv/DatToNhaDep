<?php include 'public/gui_design/000_header.php' ?>

<!-- GREETING -->
<div class="row-fluid">
    <!-- block -->
    <div class="block">
        <div class="navbar navbar-inner block-header">
            <div class="muted"><center>CHỈNH SỬA THÀNH VIÊN</center></div>
        </div>
        <div class="block-content collapse in">
          <form action="index.php?c=user&a=edit" method="POST">
          <center>
            <table>
              <tr>
                <td>Mã thành viên </td>
                <td><input type="text" placeholder="Mã thành viên" name="user_id" required value="<?php if(isset($data)) echo $data['user_id']; ?>"> </td>
              </tr>
              <tr>
                <td>Tên đầy đủ </td>
                <td><input type="text" placeholder="Tên đầy đủ" name="fullname" required value="<?php if(isset($data)) echo $data['fullname']; ?>"> </td>
              </tr>
              <tr>
                <td>Email </td>
                <td><input type="text" placeholder="Email" name="email" required value="<?php if(isset($data)) echo $data['email']; ?>"> </td>
              </tr>
              <tr>
                <td>Mật khẩu </td>
                <td><input type="password" placeholder="Mật khẩu" name="password" required value="<?php if(isset($data)) echo $data['password']; ?>"> </td>
              </tr>
              <tr>
                <td>Phân quyền </td>
                <td>
                  <select name="role" value="<?php if(isset($data)) echo $data['role']; ?>">
                  <option value=1 <?php if(isset($data)) if($data['role']==1) echo "selected"; ?>>Sinh viên</option>
                  <option value=2 <?php if(isset($data)) if($data['role']==2) echo "selected"; ?>>Giảng viên</option>
                  <option value=3 <?php if(isset($data)) if($data['role']==3) echo "selected"; ?>>Quản trị viên</option>
                  </select>
                </td>
              </tr>
            </table>
            </center>
            <center><button type="submit">LƯU</button></center>
          </form>
        </div>
    </div>
    <!-- /block -->
</div>

<?php include 'public/gui_design/000_footer.php' ?>