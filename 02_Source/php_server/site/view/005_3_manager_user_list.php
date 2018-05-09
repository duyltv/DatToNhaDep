<?php include 'public/gui_design/000_header.php' ?>

<!-- GREETING -->
<div class="row-fluid">
    <!-- block -->
    <div class="block">
        <div class="navbar navbar-inner block-header">
            <div class="muted pull-right"></div>
            <div class="pull-right">
              <a href="index.php?c=user&a=add"><span class="badge badge-warning">Thêm thành viên</span></a>
            </div>
        </div>
        <div class="block-content collapse in">
            Danh sách các thành viên
            <div class="span12">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Tên đầy đủ</th>
                      <th>Mã số</th>
                      <th>Tên đăng nhập</th>
                      <th>Email</th>
                      <th>Phân quyền</th>
                      <th>Thao tác</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 

                      if(isset($data['users']))
                      {
                        $users = $data['users'];
                        $count=1;
                        foreach($users as $row) 
                        {
                          echo '<tr>';
                          echo '<td>'.$count.'</td>';
                          echo '<td>'.$row['fullname'].'</td>';
                          echo '<td>'.$row['id'].'</td>';
                          echo '<td>'.$row['username'].'</td>';
                          echo '<td>'.$row['email'].'</td>';
                          echo '<td>';
                          if ($row['role'] == 1)
                          {
                            echo 'Sinh viên';
                          } elseif ($row['role'] == 2)
                          {
                            echo 'Giảng viên';
                          } else 
                          {
                            echo 'Quản trị viên';
                          }
                          echo '</td>';
                          echo '<td><a href="index.php?c=user&a=edit&user_id='.$row['id'].'">Sửa</a>|<a href="index.php?c=user&a=delete&user_id='.$row['id'].'">Xóa</a></td>';
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