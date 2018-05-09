<?php include 'public/gui_design/000_header.php' ?>

<!-- GREETING -->
<div class="row-fluid">
    <!-- block -->
    <div class="block">
        <div class="navbar navbar-inner block-header">
            <div class="muted"><center>TẠO MÔN HỌC MỚI</center></div>
        </div>
        <div class="block-content collapse in">
          <form action="index.php?c=teacher&a=newsubject" method="POST">
            <table>
              <tr>
                <td>Mã môn học </td>
                <td><input type="text" placeholder="Mã môn học" name="sid" required> </td>
              </tr>
              <tr>
                <td>Tên môn học </td>
                <td><input type="text" placeholder="Tên môn học" name="sname" required> </td>
              </tr>
              <tr>
                <td>Mô tả môn học </td>
                <td><input type="text" placeholder="Mô tả môn học" name="sdes" required> </td>
              </tr>
              <tr>
                <td>Học kỳ hiện tại </td>
                <td><input type="text" placeholder="Mã học kỳ" name="ssem" required> </td>
              </tr>
            </table>
            <div class="span12">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th width="95%">Outcome môn học</th>
                    </tr>
                  </thead>
                  <tbody id="outcome_list">
                    <tr id="tr_out_1">
                      <td>1</td>
                      <td><input type="text" placeholder="Nhập chuẩn đầu ra môn học (outcome)" name="outcome1" value="" style="width: 100%;"></td>
                    </tr>
                  </tbody>
                </table>
            </div>
            <input hidden name="out_count" id="out_count" value=1>
            <a style="cursor: pointer;" onclick="addOutcome()">Thêm outcome</a><br>
            <center><button type="submit">TẠO MÔN HỌC</button></center>
          </form>
        </div>
    </div>
    <!-- /block -->
</div>

<script>
var out_count=1;

function addOutcome() {
  out_count+=1;
  var outcome_tbody = document.getElementById('outcome_list');
  var new_out = document.getElementById("tr_out_1").cloneNode(true);
  new_out.id = "tr_out_" + out_count;

  var outcome_input = new_out.getElementsByTagName("input")[0];
  outcome_input.name = "outcome" + out_count;
  outcome_input.value = "";

  var outcome_order = new_out.getElementsByTagName("td")[0];
  outcome_order.innerHTML=out_count;

  outcome_tbody.appendChild(new_out);

  document.getElementById("out_count").value=out_count;
}
</script>

<?php include 'public/gui_design/000_footer.php' ?>