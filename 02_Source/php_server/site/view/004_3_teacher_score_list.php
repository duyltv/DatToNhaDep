<?php include 'public/gui_design/000_header.php' ?>

<!-- GREETING -->
<div class="row-fluid">
    <!-- block -->
    <div class="block">
        <div class="navbar navbar-inner block-header">
            <div class="muted pull-right"></div>
            <div class="pull-right">
              <a href="index.php?c=teacher&a=element&subject_id=<?php echo $data['subject_id']; ?>&update=1"><span class="badge badge-warning">Xem cấu trúc điểm thành phần</span></a>
              <span class="badge badge-warning" style="cursor: pointer;" onclick="exportTableToCSV('<?php echo $data['subject_id'];?>_score_table.csv')">Tải bảng điểm</span>
              <a href="index.php?c=teacher&a=type&subject_id=<?php echo $data['subject_id']; ?>"><span class="badge badge-warning">Nhập điểm</span></a>
              <span class="badge badge-warning" style="cursor: pointer;" onclick='$("#subject_status").show();'>Hiện thống kê</span>
            </div>
        </div>
        <?php
        if(isset($_GET['update']))
        {
          echo '<font color="blue">Điểm của một số sinh viên có sẵn trong hệ thống đã được cập nhật thành công</font>';
        }
        ?>
        <div class="block-content collapse in">
            Môn học: <?php echo $data['subject_name']; ?>
            <br>
            Công thức tính điểm: <?php echo $data['fomular']; ?>
            <div class="span12">
                <table class="table table-hover" id="score_table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Học kỳ</th>
                      <th>MSSV</th>
                      <th>Họ và tên SV</th>
                      <?php 
                        $element_count = 1;
                        if(isset($data))
                        {
                          if(isset($data['score_table']))
                          {
                            $score_title = $data['score_table'][0];
                            $titles = array_keys($score_title);

                            $count=1;
                            foreach($titles as $title) 
                            {
                              if($count > 3)
                              {
                                echo '<th>'.$title.'</th>';
                                $element_count=$element_count+1;
                              }
                              $count=$count+1;
                            }
                            $element_count=$element_count-1;
                          } elseif (isset($data['elements'])) {
                            foreach($data['elements'] as $title) 
                            {
                              echo '<th>'.$title['name'].'</th>';
                            }
                          }
                        }
                      ?>
                      <th>Tổng kết</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                      
                      if(isset($data['score_table']))
                      {
                        $scores = $data['score_table'];
                        $count=1;
                        foreach($scores as $row) 
                        {
                          echo '<tr>';
                          echo '<td>'.$count.'</td>';
                          echo '<td>'.$row['semester_id'].'</td>';
                          echo '<td>'.$row['user_id'].'</td>';
                          echo '<td>'.$row['fullname'].'</td>';

                          $titles = array_values($row);

                          $count_ele=1;
                          foreach($titles as $value) 
                          {
                            if($count_ele > 3)
                            {
                              echo '<td>'.$value.'</td>';
                            }
                            $count_ele=$count_ele+1;
                          }

                          echo '<td id=total'.$count.'></td>';
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
    $score[] = $row['user_id'];
    foreach($full_score_table as $full_row)
    {
      if($row['user_id'] == $full_row['user_id'])
      {
        $score[]=$full_row['score'];
        echo $full_row['score'].', ';
      }
    }
    echo '];';
    echo 'var fomular_'.$count.'="'.$data['fomular'].'";';
    echo 'var fomular_value = fomular_'.$count.'.replace(/score([0-9]+)/g,"score_'.$count.'[$1]");';
    echo 'document.getElementById("total'.$count.'").innerHTML=eval(fomular_value);';
    $count=$count+1;
  }
?>

</script>

<script src="public/gui_design/vendors/jquery-1.9.1.min.js"></script>
<script type="text/javascript">
function downloadCSV(csv, filename) {
    var csvFile;
    var downloadLink;

    // CSV file
    csvFile = new Blob([csv], {type: "text/csv"});

    // Download link
    downloadLink = document.createElement("a");

    // File name
    downloadLink.download = filename;

    // Create a link to the file
    downloadLink.href = window.URL.createObjectURL(csvFile);

    // Hide download link
    downloadLink.style.display = "none";

    // Add the link to DOM
    document.body.appendChild(downloadLink);

    // Click download link
    downloadLink.click();
}

function exportTableToCSV(filename) {
    var csv = [];
    var score_table = document.getElementById("score_table");
    var rows = score_table.querySelectorAll("table tr");
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");
        
        for (var j = 0; j < cols.length; j++) 
            row.push(cols[j].innerText);
        
        csv.push("\ufeff" + row.join(","));        
    }

    // Download CSV file
    downloadCSV(csv.join("\n"), filename);
}
</script>
<div id="subject_status">
<?php
    $count = 0;
    if ($element_count > 1)
    foreach ($data['standard'] as $standard)
    {
      echo $standard['outcome_des'];
      echo '<table>';
      echo '<tr>';
      foreach ($standard['score_element'] as $element)
      {
        echo '<td>';
        echo '<div id="chart_'. $count .'"></div>';
        echo '</td>';
        $count = $count + 1;
      }
      echo '</tr>';
      echo '</table>';
    }
?>
<script src="public/js/loader.js"></script>
<script type="text/javascript">
<?php
    $count = 0;

    foreach ($data['standard'] as $standard)
    {
      foreach ($standard['score_element'] as $element)
      {
        echo 'var chart_name'.$count.' = "";';
        echo 'var total_array'.$count.' = [];';

        $count = $count + 1;
      }
    }
?>

//
// PHP Array description
// $data = array (
//    ['name'] => 'title of chart',
//    ['pass'] => number_of_pass,
//    ['fail'] => number_of_fail
// )
//
function parsePHPArrayToJSArray()
{
  <?php
    $count = 0;

    foreach ($data['standard'] as $standard)
    {
      foreach ($standard['score_element'] as $element)
      {
        $chart_name = $element['score_element_des'];
        $chart_pass = $element['pass'];
        $chart_fail = $element['fail'];

        echo 'total_array'.$count.' = [];';

        echo 'var chart_title' . $count . ' = ["Trạng thái", "Số lượng"];';
        echo 'chart_name' . $count . ' = "' . $chart_name . '";';
        echo 'var chart_pass' . $count . ' = ["Đạt", ' . $chart_pass . '];';
        echo 'var chart_fail' . $count . ' = ["Không đạt", ' . $chart_fail . '];';
        echo 'total_array'.$count.'.push(chart_title' . $count . ');';
        echo 'total_array'.$count.'.push(chart_pass' . $count . ');';
        echo 'total_array'.$count.'.push(chart_fail' . $count . ');';

        $count = $count + 1;
      }
    }
    // Load google charts
    echo 'google.charts.load("current", {"packages":["corechart"]});';
    echo 'google.charts.setOnLoadCallback(drawChartPass);';
  ?>
}

parsePHPArrayToJSArray();
$("#subject_status").hide();

// Draw the chart and set the chart values
function drawChartPass() {
<?php
    $count = 0;

    foreach ($data['standard'] as $standard)
    {  
      foreach ($standard['score_element'] as $element)
      {
        echo 'var data'.$count.' = google.visualization.arrayToDataTable(total_array'.$count.');';

        // Optional; add a title and set the width and height of the chart
        echo "var options".$count." = {'title':chart_name".$count.", 'width':400, 'height':300};";

        // Display the chart inside the <div> element with id="piechart"
        echo 'var chart'.$count.' = new google.visualization.PieChart(document.getElementById("chart_'.$count.'"));';
        echo 'chart'.$count.'.draw(data'.$count.', options'.$count.');';

        $count = $count + 1;
      }
    }
?>
}

</script>
</div>

<?php include 'public/gui_design/000_footer.php' ?>