<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <title><?php use_helper('Kses'); echo kses(Setting::get('admin_title'), array()) . ' - ' . ucfirst($ctrl = Dispatcher::getController(Setting::get('default_tab'))); ?></title>
    
    <base href="<?php echo trim(BASE_URL, '?/').'/'; ?>" />

    <link rel="favourites icon" href="<?php echo URL_PUBLIC; ?>favicon.ico">
    <link href="app/backend/assets/stylesheets/admin.css" media="screen" rel="Stylesheet" type="text/css" />
    <link href="app/backend/assets/stylesheets/toolbar.css" media="screen" rel="Stylesheet" type="text/css" />
    <link href="app/backend/assets/themes/<?php echo Setting::get('theme'); ?>/styles.css" id="css_theme" media="screen" rel="Stylesheet" type="text/css" />

    <!-- IE6 PNG support fix -->
    <!--[if lt IE 7]>
        <script type="text/javascript" charset="utf-8" src="app/backend/assets/javascripts/unitpngfix.js"></script>
    <![endif]-->
    <script type="text/javascript" charset="utf-8" src="app/backend/assets/javascripts/prototype.js"></script>
    <script type="text/javascript" charset="utf-8" src="app/backend/assets/javascripts/effects.js"></script>
    <script type="text/javascript" charset="utf-8" src="app/backend/assets/javascripts/dragdrop.js"></script>
    <script type="text/javascript" charset="utf-8" src="app/backend/assets/javascripts/cp-datepicker.js"></script>
    <script type="text/javascript" charset="utf-8" src="app/backend/assets/javascripts/frog.js"></script>
    <script type="text/javascript" charset="utf-8" src="app/backend/assets/javascripts/control.textarea.js"></script>
    
<?php foreach(Plugin::$plugins as $plugin_id => $plugin): ?>
<?php if (file_exists(CORE_ROOT . '/plugins/' . $plugin_id . '/' . $plugin_id . '.js')): ?>
    <script type="text/javascript" charset="utf-8" src="plugins/<?php echo $plugin_id.'/'.$plugin_id; ?>.js"></script>
<?php endif; ?>
<?php foreach(Plugin::$javascripts as $jscript_plugin_id => $javascript): ?>
<?php if ($jscript_plugin_id == $plugin_id) { ?>
    <script type="text/javascript" charset="utf-8" src="plugins/<?php echo $plugin_id.'/'.$javascript; ?>"></script>
<?php } ?>
<?php endforeach; ?>
<?php if (file_exists(CORE_ROOT . '/plugins/' . $plugin_id . '/' . $plugin_id . '.css')): ?>
    <link href="plugins/<?php echo $plugin_id.'/'.$plugin_id; ?>.css" media="screen" rel="Stylesheet" type="text/css" />
<?php endif; ?>
<?php endforeach; ?>

<?php $action = Dispatcher::getAction(); ?>
  </head>
  <body id="body_<?php echo $ctrl.'_'.Dispatcher::getAction(); ?>">
    <div id="header">
      <div id="site-title"><a href="<?php echo get_url(); ?>"><?php echo Setting::get('admin_title'); ?></a></div>
      <div id="mainTabs">
        <ul>
          <li><a href="<?php echo get_url('page'); ?>"<?php if ($ctrl=='page') echo ' class="current"'; ?>><?php echo __('Pages'); ?></a></li>
<?php if (AuthUser::hasPermission('administrator,developer') ): ?>
          <li><a href="<?php echo get_url('snippet'); ?>"<?php if ($ctrl=='snippet') echo ' class="current"'; ?>><?php echo __('Snippets'); ?></a></li>
          <li><a href="<?php echo get_url('layout'); ?>"<?php if ($ctrl=='layout') echo ' class="current"'; ?>><?php echo __('Layouts'); ?></a></li>
<?php endif; ?>

<?php foreach (Plugin::$controllers as $plugin_name => $plugin): ?>
<?php if ($plugin->show_tab && (AuthUser::hasPermission($plugin->permissions) || AuthUser::hasPermission('administrator'))): ?>
          <li class="plugin"><a href="<?php echo get_url('plugin/'.$plugin_name); ?>"<?php if ($ctrl=='plugin' && $action==$plugin_name) echo ' class="current"'; ?>><?php echo __($plugin->label); ?></a></li>
    <?php endif; ?>
<?php endforeach; ?>

<?php if (AuthUser::hasPermission('administrator')): ?> 
          <li class="right"><a href="<?php echo get_url('setting'); ?>"<?php if ($ctrl=='setting') echo ' class="current"'; ?>><?php echo __('Administration'); ?></a></li>
          <li class="right"><a href="<?php echo get_url('user'); ?>"<?php if ($ctrl=='user') echo ' class="current"'; ?>><?php echo __('Users'); ?></a></li>
<?php endif; ?>
        </ul>
      </div>
    </div>
    <div id="main">
      <div id="content-wrapper"><div id="content">
<?php if (Flash::get('error') !== null): ?>
        <div id="error" style="display: none"><?php echo Flash::get('error'); ?></div>
        <script type="text/javascript">Effect.Appear('error', {duration:.5});</script>
<?php endif; ?>
<?php if (Flash::get('success') !== null): ?>
        <div id="success" style="display: none"><?php echo Flash::get('success'); ?></div>
        <script type="text/javascript">Effect.Appear('success', {duration:.5});</script>
<?php endif; ?>
        <!-- content -->
        <?php echo $content_for_layout; ?>
        <!-- end content -->
      </div></div>
      <div id="sidebar-wrapper"><div id="sidebar">
          <!-- sidebar -->
          <?php echo isset($sidebar) ? $sidebar: '&nbsp;'; ?>
          <!-- end sidebar -->
        </div></div>
    </div>

    <hr class="hidden" />
    <div id="footer">
      <p>
      <?php echo __('Thank you for using'); ?> <a href="http://www.madebyfrog.com/" target="_blank">Frog CMS</a> <?php echo FROG_VERSION; ?> | <a href="http://forum.madebyfrog.com/" target="_blank"><?php echo __('Feedback'); ?></a>
      </p>
<?php if (DEBUG): ?>
      <p class="stats"> <?php echo __('Page rendered in'); ?> <?php echo execution_time(); ?> <?php echo __('seconds'); ?>
      | <?php echo __('Memory usage:'); ?> <?php echo memory_usage(); ?></p>
<?php endif; ?>

      <p id="site-links">
        <?php echo __('You are currently logged in as'); ?> <a href="<?php echo get_url('user/edit/'.AuthUser::getId()); ?>"><?php echo AuthUser::getRecord()->name; ?></a>
        <span class="separator"> | </span>
        <a href="<?php echo get_url('login/logout'); ?>"><?php echo __('Log Out'); ?></a>
        <span class="separator"> | </span>
        <a href="<?php echo URL_PUBLIC; ?>" target="_blank"><?php echo __('View Site'); ?></a>
      </p>
    </div>
  </body>
</html>