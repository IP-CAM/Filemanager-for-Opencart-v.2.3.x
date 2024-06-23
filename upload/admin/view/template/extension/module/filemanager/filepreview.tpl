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
    <?php if (isset($saveSuccess) && $saveSuccess === true) { ?>
      <div style="color:white;background:green;padding:16px"><?php echo $language->get('save') ?></div>
    <?php } ?>
    <h3 style="padding-top:16px;padding-bottom:16px;"><?php echo $fileDir ?></h3>
    <form action="<?php echo $editUrl ?>" method="POST" accept-charset="UTF-8"
      enctype="application/x-www-form-urlencoded">
      <textarea name="code" style="width:100%;height:70vh"><?php echo $content ?></textarea>
      <input type="submit" <?php echo $canModify ? '' : 'disabled' ?> />
    </form>
  </div>

  <?php echo $footer; ?>