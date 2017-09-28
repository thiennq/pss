<?php
require_once("../models/Customer.php");
require_once("../models/Region.php");
require_once("../models/SubRegion.php");


$customers = Customer::all();
foreach ($customers as $key => $value) {
  $region = Region::find($value->region);
  $value->region = $region->name;
  $subregion = SubRegion::find($value->subregion);
  $value->subregion = $subregion->name;
}
$stt = 1;
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=customers.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<meta charset="utf-8" />
<table>
<thead>
    <tr>
        <td>STT</td>
        <td>Tên</td>
        <td>Số điện thoại</td>
        <td>Email</td>
        <td>Địa chỉ</td>
        <td>Quận/Huyện</td>
        <td>Tỉnh/Thành phố</td>
    </tr>
</thead>
<tbody>
    <?php foreach ($customers as $key => $row):?>
    <tr>
        <td><?php echo $stt;
        $stt++;
          ?></td>
        <td><?php echo $row['name']?></td>
        <td><?php echo $row['phone']?></td>
        <td><?php echo $row['email']?></td>
        <td><?php echo $row['address']?></td>
        <td><?php echo $row['subregion']?></td>
        <td><?php echo $row['region']?></td>
    </tr>
    <?php endforeach;?>
</tbody>
</table>
