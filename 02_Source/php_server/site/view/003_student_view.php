<?php include 'public/gui_design/000_header.php' ?>

<!-- GREETING -->
<div class="row-fluid">
    <!-- block -->
    <div class="block">
        <div class="navbar navbar-inner block-header">
            <div class="muted"><center>BẢNG ĐIỂM CÁ NHÂN</center></div>
        </div>
        <div class="block-content collapse in">
            <div class="span12">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Tên môn học</th>
                      <th>Học kỳ</th>
                      <th>Điểm thành phần</th>
                      <th>Điểm tổng kết</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 

                    if(isset($data))
                    {
                      $score_table = $data['score_table'];
                      $count=1;
                      foreach($score_table as $row) 
                      {
                        echo '<tr>';
                        echo '<td>';
                        echo $count;
                        echo '</td>';
                        echo '<td>';
                        echo $row['subject_name'];
                        echo '</td>';
                        echo '<td>';
                        echo $row['semester_id'];
                        echo '</td>';
                        echo '<td>';
                        echo $row['elements_score'];
                        echo '</td>';
                        echo '<td id="total'.$count.'">';
                        echo 0;
                        echo '</td>';
                        echo '</tr>';
                        $count=$count+1;
                      }
                    }
                     ?>
                  </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- /block -->
</div>

<script>

<?php
  $score_table = $data['score_table'];
  $full_score_table = $data['full_score_table'];
  $count=1;
  foreach($score_table as $row) 
  {
    echo 'var score_'.$count.'=[ 0, ';
    $score = array();
    $score[] = $row['subject_id'];
    foreach($full_score_table as $full_row)
    {
      if($row['subject_id'] == $full_row['subject_id'])
      {
        $score[]=$full_row['score'];
        echo $full_row['score'].', ';
      }
    }
    echo '];';
    echo 'var fomular_'.$count.'="'.$row['fomular'].'";';
    echo 'var fomular_value = fomular_'.$count.'.replace(/score([0-9]+)/g,"score_'.$count.'[$1]");';
    echo 'document.getElementById("total'.$count.'").innerHTML=eval(fomular_value);';
  }
?>

</script>

<?php include 'public/gui_design/000_footer.php' ?>