<?php echo Asset::js('Chart.min.js'); ?>
<?php echo Asset::js('ctfscore-solvedstatus.js'); ?>

<div id="errmsg"></div>

<div class="row">
    <div class="col-md-12">
	<canvas id="myChart" width="1000" height="550"></canvas>
    </div>
</div>


<script>
    $(function()
      {
	  print_chart();
      });
</script>

