
<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
    <ul class="nav navbar-nav">
      <li><a href="#">Total Shop Wallet : RM <?= number_format($shop_comm['total'], 2, '.', ''); ?></a></li>
      <!-- <li><a href="#">Total Commission Claimed : RM <?= number_format($claim_total['total'], 2, '.', ''); ?></a></li> -->
    </ul>
    <form class="navbar-form navbar-right">
      <div class="form-group">
        <!-- <input type="text" id="claimInput" class="form-control" placeholer="RM" value="<?= number_format(($shop_comm['total'] + $rp_total['total']) - $claim_total['total'], 2, '.', ''); ?>"> -->
        <input type="text" id="claimInput" class="form-control" placeholer="RM" value="">
      </div>
      <a onclick="pay_amount()" class="btn btn-success">Claim</a>
    </form>
  </div><!-- /.container-fluid -->
</nav>


<!-- <div class="portlet box blue">
  <div class="portlet-title">
    <div class="caption">

    </div>
  </div>
  <div class="portlet-body">
    <table class="table table-bordered" id="sample_4">
      <thead>
        <th class="text-center">ID</th>
        <th class="text-center">AMOUNT (RM)</th>
        <th class="text-center">CLAIM DATE</th>
        <th class="text-center">STATUS</th>
        <th class="text-center">PAY DATE</th>
        <th class="text-center">NOTE</th>
      </thead>
      <tbody>
        <?php
        $i=1;

          if(!empty($claim_list)){
            foreach ($claim_list as $row){?>
              <tr class="text-center">
                <td>#<?= $row['id'] ?></td>
                <td><?= $row['claim'] ?></td>
                <td><?= date('d-m-Y H:i', strtotime($row['date_claim']))  ?></td>
                <td><?= ($row['status']==0) ? '<span class="label label-warning">Pending</span>' : '<span class="label label-success">Complete</span>' ?></td>
                <td><?= (($row['date_pay']) != NULL) ? date('d-m-Y H:i', strtotime($row['date_pay'])) : '' ?></td>
                <td><?= $row['note'] ? $row['note'] : '' ?></td>
              </tr>
              <?php 
            }
          } ?>
      </tbody>
    </table>
  </div>
</div> -->



