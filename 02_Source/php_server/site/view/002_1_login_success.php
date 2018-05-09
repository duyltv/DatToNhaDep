<?php include 'public/gui_design/000_header.php' ?>

<!-- GREETING -->
<div class="row-fluid">
    <!-- block -->
    <div class="block">
        <div class="navbar navbar-inner block-header">
            <div class="muted"><center>ĐĂNG NHẬP HỆ THỐNG</center></div>
        </div>
        <div class="block-content collapse in">
            <center id="login_message">
                Đăng nhập thành công. Đang chuyển hướng về lại
            </center>
        </div>
    </div>
    <!-- /block -->
</div>
<script>
var seconds = 0;

function incrementSeconds() {
    seconds += 1;
    var login_message=document.getElementById('login_message');
    login_message.innerHTML=login_message.innerHTML+'.';
    if (seconds == 5) 
    {
        window.location = "index.php";
    }
}

var cancel = setInterval(incrementSeconds, 1000);
</script>

<?php include 'public/gui_design/000_footer.php' ?>