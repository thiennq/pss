<?php
  require_once('../vendor/autoload.php');
  require_once('../framework/database.php');
  require_once('../framework/controller.php');
  require_once('../framework/config.php');
  require_once '../models/Article.php';
  $data = Article::where('type', 'tin-tuc')->orderBy('title', 'asc')->get();
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<script type="text/javascript" src="http://oss.sheetjs.com/js-xlsx/xlsx.core.min.js"></script>
<script type="text/javascript" src="http://sheetjs.com/demos/FileSaver.js"></script>
<script type="text/javascript" src="http://sheetjs.com/demos/Export2Excel.js"></script>
<script type="text/javascript" src="http://sheetjs.com/demos/Blob.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" ></script>
<script>
function doit() { export_table_to_excel('table'); }
</script>
<div class="table-responsive container-fluid">
  <br>
  <br>
  <button class="btn btn-primary" type="submit" onclick="doit();">Export Table</button>
  <br>
  <br>
    <table id="table" class="table table-bordered">
      <thead>
          <tr>
              <td>id</td>
              <td>title</td>
              <td>link</td>
          </tr>
      </thead>
      <tbody>
          <?php foreach ($data as $row):?>
          <tr>
              <td><?php echo $row['id']?></td>
              <td><?php echo $row['title']?></td>
              <td><?php echo HOST . '/' . $row['link']?></td>
          </tr>
          <?php endforeach;?>
      </tbody>
  </table>
</div>
