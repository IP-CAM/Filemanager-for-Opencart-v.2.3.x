<?php echo $header; ?><?php echo $column_left; ?>

<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><i class="fa fa-file-code-o"></i>&nbsp;<?php echo $language->get('plugin_name'); ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
          <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>

      </ul>
      <div style="float:right;margin-top:10px;">
        <!--  -->
      </div>
    </div>
  </div>

  <div style="padding:16px">
    <h3><?php echo $language->get('base_directory') ?>: <?php echo $baseDir ?> (<?php echo count($files) ?>)</h3>

    <?php foreach ($files as $file) { ?>
      <a href="<?php echo $editUrl . "&filename=" . $file ?>" target="_blank"><?php echo $file ?></a></br />
    <?php } ?>
  </div>
</div>

<?php echo $footer; ?>