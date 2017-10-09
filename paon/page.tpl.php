<?php // $Id: page.tpl.php,v 1.0 2009/05/14 00:13:31 jcscott Exp $ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
        "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $language->language; ?>" lang="<?php echo $language->language; ?>" dir="<?php echo $language->dir; ?>">
  <head>
    <?php echo $head; ?>
    <title><?php echo $head_title; ?></title>
    <?php echo $styles; ?>
    <?php echo $scripts; ?>
    <!--[if lt IE 7]>
      <?php echo phptemplate_get_ie_styles(); ?>
    <![endif]-->
  </head>
  <body>

<!-- Layout -->
	<div id="container">
		<div id="header">
			<div id="signin" class="clear-block">
				<?=get_signIn_form(1);?>
			</div>

			<div id="logo-floater">
			<?php
			  // Prepare header
			  $site_fields = array();
			  if ($site_name) {
				$site_fields[] = check_plain($site_name);
			  }
			  if ($site_slogan) {
				$site_fields[] = check_plain($site_slogan);
			  }
			  $site_title = implode(' ', $site_fields);
			  if ($site_fields) {
				$site_fields[0] = '<span>'. $site_fields[0] .'</span>';
			  }
			  $site_html = implode(' ', $site_fields);
	
			  if ($logo || $site_title) {
				print '<h1><a href="'. check_url($front_page) .'" title="'. $site_title .'">';
				if ($logo) {
				  print '<img src="'. check_url($logo) .'" alt="'. $site_title .'" id="logo" />';
				}
				print $site_html .'</a></h1>';
			  }
			?>
			</div>

			<div class="info">
				<span>call Us: 760.729.7377</span>
				Email us: <?=$site_email_link;?>
			</div>

			<div id="primary-menu">
				<?php echo theme('links', $primary_links, array('class' => 'links primary-links')); ?>
			</div>
		</div> <!-- /header -->
	
		<div id="content">
			<?php if ($mission): echo '<div id="mission">'. $mission .'</div>'; endif; ?>

			<?php if ($admin_mode && $title) : ?>
              <div class="title-wrapper"><h2><?=$title;?></h2></div>
			<?php endif; ?>

			<?php if ($tabs || $tabs2) :?>
			  <div id="tabs-wrapper">
			  <?php if ($tabs) : ?>
				<ul class="tabs primary"><?=$tabs;?></ul>
			  <?php endif; ?>
			  <?php if ($tabs2) : ?>
				<ul class="tabs secondary"><?=$tabs2;?></ul>
			  <?php endif; ?>
			  </div>
			<?php endif; ?>

			<?php if ($show_messages && $messages): echo $messages; endif; ?>

			<?php echo $help; ?>

			<div id="winebar" class="clear-block">
				<div id="content-left">
					<?php echo $content; ?>
				</div>

              <?php if ($right) : ?>
				<div id="right-region"><?=$right;?></div>
             <?php endif; ?>
			</div>
		</div> <!-- /content -->

		<?php if ($admin && $is_admin): ?>
			<div id="admin-float"><?=$admin;?></div>
		<?php endif; ?>

		<div id="footer">
			<?=($footer) ? $footer : '';?>

			<div class="info">
				call Us: 760.729.7377<br />
				Email us: <?=$site_email_link;?>
			</div>
			<div class="copyright">
				PAON &copy;<?=date('Y');?><br />
				<a href="#">Privacy Policy</a> | <a href="#">Terms and Conditions</a>
			</div>
		</div>
	</div> <!-- /container -->
	<!-- /layout -->
	<?php echo $closure; ?>
	</body>
</html>
