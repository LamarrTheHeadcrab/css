<?php
/***********************************************************************/

// Some info about your mod.
$mod_title      = 'User nickname custom CSS styles';
$mod_version    = '1.0.0';
$release_date   = '2018-10-04';
$author         = 'Lamarr';
$author_email   = 'http://lwplxqzvmgu43uff.onion/';

// Set this to false if you haven't implemented the restore function (see below)
$mod_restore	= true;

// This following function will be called when the user presses the "Install" button
function install()
{
	global $db, $db_type, $pun_config;

	$db->add_field('users', 'custom_style', 'text', true, '') or error('Unable to add column "custom_style" to table "users"', __FILE__, __LINE__, $db->error());
	$db->add_field('groups', 'g_custom_style', 'text', true, '') or error('Unable to add column "g_custom_style" to table "groups"', __FILE__, __LINE__, $db->error());
	$db->query('INSERT IGNORE INTO '.$db->prefix.'config VALUES (\'o_banned_custom_style\', \'\')', true) or error('Unable to add row "o_banned_custom_style" to table "config"', __FILE__, __LINE__, $db->error());
	
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require PUN_ROOT.'include/cache.php';

	generate_config_cache();
}

// This following function will be called when the user presses the "Restore" button (only if $mod_restore is true (see above))
function restore()
{
	global $db, $db_type, $pun_config;

	$db->drop_field('users', 'custom_style') or error('Unable to drop column "custom_style" from table "users"', __FILE__, __LINE__, $db->error());
	$db->drop_field('groups', 'g_custom_style') or error('Unable to drop column "g_custom_style" from table "groups"', __FILE__, __LINE__, $db->error());
	$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_banned_custom_style\'', true) or error('Unable to remove row "o_banned_custom_style" from table "config"', __FILE__, __LINE__, $db->error());
	
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require PUN_ROOT.'include/cache.php';

	generate_config_cache();
}

/***********************************************************************/

// DO NOT EDIT ANYTHING BELOW THIS LINE!


// Circumvent maintenance mode
define('PUN_TURN_OFF_MAINT', 1);
define('PUN_ROOT', './');
require PUN_ROOT.'include/common.php';

// We want the complete error message if the script fails
if (!defined('PUN_DEBUG'))
	define('PUN_DEBUG', 1);

$style = (isset($pun_user)) ? $pun_user['style'] : $pun_config['o_default_style'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo pun_htmlspecialchars($mod_title) ?> installation</title>
<link rel="stylesheet" type="text/css" href="style/<?php echo $style.'.css' ?>" />
</head>
<body>

<div id="punwrap">
<div id="puninstall" class="pun" style="margin: 10% 20% auto 20%">

<?php

if (isset($_POST['form_sent']))
{
	if (isset($_POST['install']))
	{
		// Run the install function (defined above)
		install();

?>
<div class="block">
	<h2><span>Installation successful</span></h2>
	<div class="box">
		<div class="inbox">
			<p>Your database has been successfully prepared for <?php echo pun_htmlspecialchars($mod_title) ?>. See readme.txt for further instructions.</p>
		</div>
	</div>
</div>
<?php

	}
	else
	{
		// Run the restore function (defined above)
		restore();

?>
<div class="block">
	<h2><span>Restore successful</span></h2>
	<div class="box">
		<div class="inbox">
			<p>Your database has been successfully restored.</p>
		</div>
	</div>
</div>
<?php

	}
}
else
{

?>
<div class="blockform">
	<h2><span>Mod installation</span></h2>
	<div class="box">
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
			<div><input type="hidden" name="form_sent" value="1" /></div>
			<div class="inform">
				<p>This script will update your database to work with the following modification:</p>
				<p><strong>Mod title:</strong> <?php echo pun_htmlspecialchars($mod_title.' '.$mod_version) ?></p>
				<p><strong>Author:</strong> <?php echo pun_htmlspecialchars($author) ?> (<a href="<?php echo pun_htmlspecialchars($author_email) ?>"><?php echo pun_htmlspecialchars($author_email) ?></a>)</p>
				<p><strong>Disclaimer:</strong> Mods are not officially supported by FluxBB. Mods generally can't be uninstalled without running SQL queries manually against the database. Make backups of all data you deem necessary before installing.</p>
<?php if ($mod_restore): ?>
				<p>If you've previously installed this mod and would like to uninstall it, you can click the Restore button below to restore the database.</p>
<?php endif; ?>
			</div>
			<p class="buttons"><input type="submit" name="install" value="Install" /><?php if ($mod_restore): ?><input type="submit" name="restore" value="Restore" /><?php endif; ?></p>
		</form>
	</div>
</div>
<?php

}

?>

</div>
</div>

</body>
</html>
<?php

// End the transaction
$db->end_transaction();

// Close the db connection (and free up any result data)
$db->close();
