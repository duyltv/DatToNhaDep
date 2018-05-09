<?php include 'public/gui_design/000_header.php' ?>

<!-- GREETING -->
<div class="row-fluid">
    <!-- block -->
    <div class="block">
        <div class="navbar navbar-inner block-header">
            <div class="muted"><center>ĐĂNG NHẬP THẤT BẠI</center></div>
        </div>
        <div class="block-content collapse in">
            <center>
                <div>
                Hãy kiểm tra lại thông tin tài khoản và đăng nhập lại.
                <form action="index.php?c=user&a=login" id="login_id" method="POST">
                    <table>
                        <tr>
                            <td>Tên đăng nhập: </td>
                            <td><input type="text" placeholder="Enter Username" name="username" required></td>
                        </tr>
                        <tr>
                            <td>Mật khẩu: </td>
                            <td><input type="password" placeholder="Enter Password" name="password" required></td>
                        </tr>
                    </table>
                    <button type="submit">Đăng nhập</button>
                </form>
                </div>
            </center>
        </div>
    </div>
    <!-- /block -->
</div>

<?php include 'public/gui_design/000_footer.php' ?>