<?php include 'public/gui_design/000_header.php' ?>
<?php 
$global_count = 1;
?>
<!-- GREETING -->
<div class="row-fluid">
    <!-- block -->
    <div class="block">
        <div class="navbar navbar-inner block-header">
            <div class="muted"><center>MÔN HỌC: <?php echo $data['subject_name']; ?></center></div>
        </div>
        <div class="block-content collapse in">
          <form action="" method="POST">
            <div class="span12">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Tên điểm thành phần</th>
                      <th>Tên biến công thức</th>
                      <th>Các outcome liên quan</th>
                    </tr>
                  </thead>
                  <tbody id="element_list">
                    <?php
                    if(sizeof($data['elements'])>0)
                    {
                      $count=1;
                      foreach ($data['elements'] as $element)
                      {
                        echo '<tr id="tr_element_'.$count.'">';
                        echo '<td>'.$count.'</td>';
                        echo '<td><input type="text" placeholder="Nhập tên" name="name'.$count.'" id="name'.$count.'" value="'.$element['name'].'" style="width: 100%;"></td>';
                        echo '<td id="var'.$count.'">score'.$count.'</td>';
                        echo '<td>';
                        echo '<select name="outcome'.$count.'[]" id="outcome'.$count.'" multiple>';
                        $count_out=1;
                        $outcome_index=$count-1;
                        $outcomes = $data['outcome_of_ele'][$outcome_index];
                        
                        foreach($data['outcomes'] as $outcome)
                        {
                          $found=false;
                          foreach($outcomes as $out_point)
                          {
                            if ($out_point == $count_out)
                            {
                                $found=true;
                            }
                          }
                          if ($found)
                          {
                            echo '<option value="'.$count_out.'" selected>'.$outcome['description'].'</option>';
                          } else {
                            echo '<option value="'.$count_out.'">'.$outcome['description'].'</option>';
                          }
                          $count_out=$count_out+1;
                        }
                        echo '</select>';
                        echo '</tr>';
                        $global_count=$count;
                        $count=$count+1;
                      }
                    } else {
                      echo '<tr id="tr_element_1">';
                      echo '<td>1</td>';
                      echo '<td><input type="text" placeholder="Nhập tên" name="name1" id="name1" value="" style="width: 100%;"></td>';
                      echo '<td id="var1">score1</td>';
                      echo '<td>';
                      echo '<select name="outcome1[]" id="outcome1" multiple>';
                      $count_out=1;
                      foreach($data['outcomes'] as $outcome)
                      {
                        echo '<option value="'.$count_out.'">'.$outcome['description'].'</option>';
                        $count_out=$count_out+1;
                      }
                      echo '</select>';
                      echo '</tr>';
                    }
                    ?>
                  </tbody>
                </table>
            </div>
            <input hidden name="element_count" id="element_count" value=1>
            <?php
            if(isset($_GET['update']))
            {
              echo '<input hidden name="update" id="update" value=1>';
            }
            ?>
            <a onclick="addElement()">Thêm thành phần điểm</a><br>
            <br>
            Công thức điểm tổng kết
            <br>
            <input type="text" placeholder="Công thức tính" name="fomular" style="width:100%;" <?php if(isset($data['subject_fomular'])) echo 'value="'.$data['subject_fomular'].'"'; ?> required>
            <?php if(!isset($_GET['update'])) 
            echo '<center><button type="submit">LƯU</button></center>';
            ?>
          </form>
        </div>
    </div>
    <!-- /block -->
</div>

<script>
var element_count=<?php echo $global_count;?>;

function addElement() {
  element_count+=1;
  var element_tbody = document.getElementById('element_list');
  var new_element = document.getElementById("tr_element_1").cloneNode(true);
  new_element.id = "tr_element_" + element_count;

  var name_input = new_element.getElementsByTagName("input")[0];
  name_input.id = "name" + element_count;
  name_input.name = "name" + element_count;
  name_input.value = "";

  var outcome_input = new_element.getElementsByTagName("select")[0];
  outcome_input.id = "outcome" + element_count;
  outcome_input.name = "outcome" + element_count + "[]";
  outcome_input.value = "1";

  var var_name = new_element.getElementsByTagName("td")[2];
  var_name.id = "var" + element_count;
  var_name.innerHTML = "score" + element_count;

  var element_order = new_element.getElementsByTagName("td")[0];
  element_order.innerHTML=element_count;

  element_tbody.appendChild(new_element);

  document.getElementById("element_count").value=element_count;
}
</script>

<?php include 'public/gui_design/000_footer.php' ?>