<?php include 'public/gui_design/000_header.php' ?>

<!-- GREETING -->
<div class="row-fluid">
    <!-- block -->
    <div class="block">
        <div class="navbar navbar-inner block-header">
            <div class="muted pull-right"></div>
            <div class="pull-right"><a href="index.php?c=teacher&a=newsubject"><span class="badge badge-warning">Tạo môn học mới</span></a></div>
        </div>
        <div class="block-content collapse in">
            Chọn môn học cần quản lý
            <div class="span12">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Tên môn học</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 

                    if(isset($data))
                    {
                      $subjects = $data['subjects'];
                      $count=1;
                      foreach($subjects as $row) 
                      {
                        echo '<tr style="cursor: pointer;" onclick="window.location.href=\'index.php?c=teacher&a=scores&subject_id=' . $row['subject_id'] . '\';">';
                        echo '<td>';
                        echo $count;
                        echo '</td>';
                        echo '<td>';
                        echo $row['subject_name'];
                        echo '</td>';
                        echo '</a>';
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

<?php include 'public/gui_design/000_footer.php' ?>