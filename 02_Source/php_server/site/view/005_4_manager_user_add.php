<?php include 'public/gui_design/000_header.php' ?>

<!-- GREETING -->
<div class="row-fluid">
    <!-- block -->
    <div class="block">
        <div class="navbar navbar-inner block-header">
            <div class="muted"><center>THÊM THÀNH VIÊN</center></div>
        </div>
        <div class="block-content collapse in">
          <form action="index.php?c=user&a=add" method="POST">
          <center>
            <table>
              <tr>
                <td>Mã thành viên </td>
                <td><input type="text" placeholder="Mã thành viên" name="user_id" required> </td>
              </tr>
              <tr>
                <td>Tên đầy đủ </td>
                <td><input type="text" placeholder="Tên đầy đủ" name="fullname" required> </td>
              </tr>
              <tr>
                <td>Email </td>
                <td><input type="text" placeholder="Email" name="email" required> </td>
              </tr>
              <tr>
                <td>Mật khẩu </td>
                <td><input type="password" placeholder="Mật khẩu" name="password" required> </td>
              </tr>
              <tr>
                <td>Phân quyền </td>
                <td>
                  <select name="role">
                  <option value=1>Sinh viên</option>
                  <option value=2>Giảng viên</option>
                  <option value=3>Quản trị viên</option>
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