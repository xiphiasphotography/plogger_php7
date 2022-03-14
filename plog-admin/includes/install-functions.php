<?php
if (basename($_SERVER['PHP_SELF']) == basename( __FILE__ )) {
	// ignorance is bliss
	exit();
}

require_once(PLOGGER_DIR.'plog-admin/plog-admin-functions.php');

/**** Common Functions ****/

function maybe_add_column($table, $column, $add_sql) {
	$sql = "SELECT COLUMN_NAME FROM information_schema.COLUMNS where TABLE_NAME = '".$table."'";
	$res = run_query($sql, false, '');
	$found = false;
	while($row = $res->fetch(PDO::FETCH_NUM)) {
		if ($row[0] == $column) $found = true;
	}
	if (!$found) {
		run_query("ALTER TABLE $table ADD \"$column\" ".$add_sql);
		return plog_tr('Added new field to database').': '.$column;
	} else {
		if (defined('PLOGGER_DEBUG')) {
			return 'Field <strong>'.$column.'</strong> already exists, ignoring.';
		}
	}
}

function maybe_add_index($table, $index, $column) {
	$sql = "SELECT COLUMN_NAME FROM information_schema.COLUMNS where TABLE_NAME = '".$table."'";
	$res = run_query($sql, false, '');
	$found = false;
	while($row = $res->fetch(PDO::FETCH_NUM)) {
		if ($row[0] == $index) $found = true;
	}
	if (!$found) {
		run_query("CREATE INDEX \"$index\" ON \"$table\" USING btree($column)");
		return plog_tr('Added new field to database').': '.$index;
	} else {
		if (defined('PLOGGER_DEBUG')) {
			return 'Field <strong>'.$index.'</strong> already exists, ignoring.';
		}
	}
}

function maybe_drop_column($table, $column) {
	$sql = "SELECT COLUMN_NAME FROM information_schema.COLUMNS where TABLE_NAME = '".$table."'";
	$res = run_query($sql, false, '');
	$found = false;
	while($row = $res->fetch(PDO::FETCH_NUM)) { 
		if ($row[0] == $column) $found = true;
	}
	if ($found) {
		$sql = "ALTER TABLE $table DROP \"$column\"";
		run_query($sql);
		return plog_tr('Dropped column').': '.$column;
	} else {
		if (defined('PLOGGER_DEBUG')) {
			return $column.' does not exist';
		}
	}
}

function maybe_add_table($table, $add_sql, $options = '') {
	$sql = "SELECT COLUMN_NAME FROM information_schema.COLUMNS where TABLE_NAME = '".$table."'";
	$res = run_query($sql, false, '');
	if (empty($res->fetch())) {
		$q = "CREATE table \"$table\" ($add_sql) $options";
		$result = run_query($q, false, '');
		if ($result) {
			return true;
		}
	} else {
		if (defined('PLOGGER_DEBUG')) {
			return 'Table <strong>'.$table.'</strong> already exists, ignoring.';
		}
	}
}

function gd_missing() {
	require_once(PLOGGER_DIR.'/plog-includes/lib/phpthumb/phpthumb.functions.php');
	// This is copied over from phpthumb
	return phpthumb_functions::gd_version() < 1;
}

function check_requirements() {
	$errors = array();

	// Check that the session variable can be read
	if (!isset($_SESSION['plogger_session'])) {
		$save_path = ini_get('session.save_path');

		if (!defined('SESSION_SAVE_PATH')) {
			$sample_text = ' ('.sprintf(plog_tr('see %s if your %s does not contain this variable'), 'plog-config-sample.php', 'plog-config.php').')';
		} else {
			$sample_text = '';
		}

		// Check that session.save_path is set (not set by default on PHP5)
		if (empty($save_path)) {
			$errors[] = sprintf( plog_tr('The PHP %s variable is not set in your php.ini file.'), '<strong>session.save_path</strong>').' '.sprintf(plog_tr('You can attempt to set this by adding a writable directory path to the %s variable in %s or contact your webhost on how to set this system variable.'), '<strong>SESSION_SAVE_PATH</strong>', 'plog-config.php'.$sample_text);
		} else {
			$errors[] = sprintf(plog_tr('PHP session cookies are not being set. Please check that session cookies are enabled on your browser or verify that your %s variable is set up correctly.'), '<strong>session.save_path</strong>').' '.sprintf(plog_tr('You can attempt to set this by adding a writable directory path to the %s variable in %s or contact your webhost on how to set this system variable.'), '<strong>SESSION_SAVE_PATH</strong>', 'plog-config.php'.$sample_text);
		}
	}

	// Check that the GD library is available
	if (gd_missing()) {
		$errors[] = plog_tr('PHP GD module was not detected.');
	}

	// Check that PDO is available
	if (!extension_loaded('PDO')) {
		$errors[] = plog_tr('PDO module was not detected.');
	}

	// Make sure we have permission to read these folders/files
	$files_to_read = array('./plog-admin', './plog-admin/css', './plog-admin/images', './plog-content/images', './plog-content/thumbs', './plog-content/uploads', './plog-includes', './plog-includes/lib');
	foreach($files_to_read as $file) {
		if (!is_readable(PLOGGER_DIR.$file)) {
			$errors[] = sprintf(plog_tr('The path %s is not readable by the web server.'), '<strong>'.realpath(PLOGGER_DIR.$file).'</strong>');
		}
	}

	// Workaround for upgrading from beta1 since there are conflicting function in plog-functions.php and beta1 plog-connect.php
	if (function_exists('is_safe_mode')) {
		// If safe mode enabled, we will use the FTP workarounds to deal with folder permissions
		if (!is_safe_mode()) {
			// Make sure we have permission to write to these folders
			$files_to_write = array('./plog-content/images', './plog-content/thumbs');
			$i = 0;
			foreach($files_to_write as $file) {
				if (!is_writable(PLOGGER_DIR.$file)) {
					$errors[] = sprintf(plog_tr('The path %s is not writable by the web server.'), '<strong>'.realpath(PLOGGER_DIR.$file).'</strong>');
				} else if (is_open_perms(realpath(PLOGGER_DIR.$file))) {
					$_SESSION['plogger_close_perms'][basename($file)] = realpath(PLOGGER_DIR.$file);
				}
			}
			if (isset($_SESSION['plogger_close_perms'])) {
				if (!is_writable(PLOGGER_DIR.'plog-content/')) {
					$errors[] = sprintf(plog_tr('Please temporarily CHMOD the %s directory to 0777 to allow Plogger to create initial directories for increased security. You will be prompted to CHMOD the directory back to 0755 after installation is complete.'), '<strong>plog-content/</strong>');
				}
			}
		}
	}

	return $errors;
}

function check_sql_form($form) {
	$errors = array();

	if (empty($form['db_host'])) {
		$errors[] = plog_tr('Please enter the name of your database host.');
	}

	if (empty($form['db_user'])) {
		$errors[] = plog_tr('Please enter the database username.');
	}

	if (empty($form['db_name'])) {
		$errors[] = plog_tr('Please enter the database name.');
	}
	
	return $errors;
}

function check_ftp_form($form) {
	$errors = array();

	if (empty($form['ftp_host'])) {
		$errors[] = plog_tr('Please enter the name of your FTP host.');
	}

	if (empty($form['ftp_user'])) {
		$errors[] = plog_tr('Please enter the FTP username.');
	}

	if (empty($form['ftp_pass'])) {
		$errors[] = plog_tr('Please enter the FTP password.');
	}

	if (!empty($form['ftp_path'])) {
		if (substr($form['ftp_path'], 0, 1) != '/'){
			$form['ftp_path'] = '/'.$form['ftp_path'];
		}
		if (substr($form['ftp_path'], -1) != '/'){
			$form['ftp_path'] = $form['ftp_path'].'/';
		}
	}

	return array('errors' => $errors, 'form' => $form);
}

function check_ftp($host, $user, $pass, $path) {
	$errors = array();

	$connection = @ftp_connect($host);
	if (!$connection) {
		$errors[] = sprintf(plog_tr('Cannot connect to FTP host %s. Please check your FTP Host:'), '<strong>'.$host.'</strong>');
	} else {
		$login = @ftp_login($connection, $user, $pass);
		if (!$login) {
			$errors[] = sprintf( plog_tr('Cannot login to FTP host %s with username %s and password %s. Please check your FTP Username: and FTP Password:'), '<strong>'.$host.'</strong>', '<strong>'.$user.'</strong>', '<strong>'.$pass.'</strong>');
		} else {
			$checkdir = @ftp_chdir($connection, $path.'plog-content/images/'); // Check to see if the plog-content/images/ folder is accessible
			if (!$checkdir) {
				$errors[] = sprintf(plog_tr('Cannot find the Plogger %s directory along the path %s. Please check your FTP path to Plogger base folder (from FTP login):'), '<strong>plog-content/images/</strong>', '<strong>'.$path.'</strong>');
			}
		}
	}
	@ftp_close($connection);
	return $errors;
}

/**** Install Functions ****/

function do_install($form) {
	$form = array_map('stripslashes', $form);
	$form = array_map('trim', $form);

	// First check the requirements
	$errors = check_requirements();
	if (sizeof($errors) > 0) {
		echo "\t" . '<p class="errors">'.plog_tr('Plogger cannot be installed until the following problems are resolved').':</p>';
		echo "\n\n\t\t" . '<ul class="info">';
		foreach($errors as $error) {
			echo "\n\t\t\t" . '<li class="margin-5">'.$error.'</li>';
		}
		echo "\n\t\t" . '</ul>';
		echo "\n\n\t" . '<form method="get" action="'.$_SERVER['REQUEST_URI'].'">
		<p><input class="submit" type="submit" value="'.plog_tr('Try again').'" /></p>
	</form>' . "\n";
		return false;
	}

	$ok = false;
	$errors = array();

	// If we've already defined the database information, pass the values and skip them on the form
	if (defined('PLOGGER_DB_HOST')) {
		$pdocheck = check_pdo(PLOGGER_DB_TYPE, PLOGGER_DB_HOST, PLOGGER_DB_USER, PLOGGER_DB_PW, PLOGGER_DB_NAME, PLOGGER_DB_PORT);
		if (count($pdocheck) > 0) {
			$sql_fail = true;
		} else {
			unset($_SESSION['plogger_config']);
		}
		// Set the form values equal to config values if already set
		if (empty($form['db_host'])) {
			$form['db_host'] = PLOGGER_DB_HOST;
		}
		if (empty($form['db_user'])) {
			$form['db_user'] = PLOGGER_DB_USER;
		}
		if (empty($form['db_pass'])) {
			$form['db_pass'] = PLOGGER_DB_PW;
		}
		if (empty($form['db_name'])) {
			$form['db_name'] = PLOGGER_DB_NAME;
		}
		if (empty($form['db_type'])) {
			$form['db_type'] = PLOGGER_DB_TYPE;
		}
		if (empty($form['db_port'])) {
			$form['db_port'] = PLOGGER_DB_PORT;
		}		
	}

	if (isset($form['action']) && $form['action'] == 'install') {
		if (!defined('PLOGGER_DB_HOST') || isset($sql_fail)) {
			$sql_form_check = check_sql_form($form);
			if (!empty($sql_form_check)) {
				$errors = array_merge($errors, $sql_form_check);
			}
		}

		if (empty($form['gallery_name'])) {
			$errors[] = plog_tr('Please enter the name for your gallery.');
		}

		if (empty($form['admin_email'])) {
			$errors[] = plog_tr('Please enter your email address.');
		}

		if (empty($form['admin_username'])) {
			$errors[] = plog_tr('Please enter a username.');
		}

		if (empty($form['admin_password'])) {
			$errors[] = plog_tr('Please enter a password.');
		}

		if ($form['admin_password'] != $form['admin_password_confirm']) {
			$errors[] = plog_tr('Your passwords do not match. Please try again.');
		}

		if (is_safe_mode()) {
			// If safe_mode enabled, check the FTP information form inputs
			$ftp_form_check = check_ftp_form($form);
			$form = $ftp_form_check['form'];
			if (!empty($ftp_form_check['form']['errors'])) {
				$errors = array_merge($errors, $ftp_form_check['form']['errors']);
			}
		}

		if (empty($errors)) {
			$sql_errors = check_pdo($form['db_type'], $form['db_host'], $form['db_user'], $form['db_pass'], $form['db_name'], $form['db_port']);
			if (is_safe_mode()) {
				$ftp_errors = check_ftp($form['ftp_host'], $form['ftp_user'], $form['ftp_pass'], $form['ftp_path']);
			} else {
				$ftp_errors = array();
			}
			$errors = array_merge($sql_errors, $ftp_errors);
			$ok = empty($errors);
		}

		if (!$ok) {
			echo '<ul class="errors" style="background-image: none;">' . "\n\t" . '<li class="margin-5">';
			echo join("</li>\n\t<li class=\"margin-5\">", $errors);
			echo "</li>\n</ul>\n\n";
		} else {
			$_SESSION['install_values'] = array(
				'gallery_name' => $form['gallery_name'],
				'admin_email' => $form['admin_email'],
				'admin_password' => $form['admin_password'],
				'admin_username' => $form['admin_username']
			);
			if (is_safe_mode()) {
				$_SESSION['ftp_values'] = array(
					'ftp_host' => $form['ftp_host'],
					'ftp_user' => $form['ftp_user'],
					'ftp_pass' => $form['ftp_pass'],
					'ftp_path' => $form['ftp_path']
				);
			}

			if (!defined('PLOGGER_DB_HOST') || isset($sql_fail)) {
				// Serve the config file and ask user to upload it to webhost
				$_SESSION['plogger_config'] = create_config_file($form['db_host'], $form['db_user'], $form['db_pass'], $form['db_name'], $form['db_type'], $form['db_port']);
			}
			return true;
		}
	}

	include(PLOGGER_DIR.'plog-admin/includes/install-form-setup.php');
	return false;
}

function create_tables($dbtype) {
	if ($dbtype == 'mysql') {
		maybe_add_table(
		PLOGGER_TABLE_PREFIX.'collections'
		,"\"name\" varchar(128) NOT NULL,
		\"description\" varchar(255) NOT NULL,
		\"path\" varchar(255) NOT NULL,
		\"id\" int(11) NOT NULL auto_increment,
		\"thumbnail_id\" int(11) NOT NULL DEFAULT '0',
		PRIMARY KEY (\"id\")"
		,"Engine=MyISAM");

		maybe_add_table(
		PLOGGER_TABLE_PREFIX.'albums'
		," \"name\" varchar(128) NOT NULL,
		\"id\" int(11) NOT NULL auto_increment,
		\"description\" varchar(255) NOT NULL,
		\"path\" varchar(255) NOT NULL,
		\"parent_id\" int(11) NOT NULL default '0',
		\"thumbnail_id\" int(11) NOT NULL default '0',
		PRIMARY KEY (\"id\"),
		INDEX pid_idx (\"parent_id\")"
		,"Engine=MyISAM");

		maybe_add_table(
		PLOGGER_TABLE_PREFIX.'pictures'
		,"\"path\" varchar(255) NOT NULL,
		\"parent_album\" int(11) NOT NULL default '0',
		\"parent_collection\" int(11) NOT NULL default '0',
		\"caption\" text NOT NULL,
		\"description\" text NOT NULL,
		\"id\" int(11) NOT NULL auto_increment,
		\"date_modified\" timestamp NOT NULL,
		\"date_submitted\" timestamp NOT NULL,
		\"exif_date_taken\" timestamp NULL,
		\"exif_camera\" varchar(64) NOT NULL,
		\"exif_shutterspeed\" varchar(64) NOT NULL,
		\"exif_focallength\" varchar(64) NOT NULL,
		\"exif_flash\" varchar(64) NOT NULL,
		\"exif_aperture\" varchar(64) NOT NULL,
		\"exif_iso\" varchar(64) NOT NULL,
		\"allow_comments\" int(11) NOT NULL default '1',
		PRIMARY KEY (\"id\"),
		INDEX pa_idx (\"parent_album\"),
		INDEX pc_idx (\"parent_collection\")"
		,"Engine=MyISAM");

		maybe_add_table(
		PLOGGER_TABLE_PREFIX.'comments'
		,"\"id\" int(11) NOT NULL auto_increment,
		\"parent_id\" int(11) NOT NULL default '0',
		\"author\" varchar(64) NOT NULL,
		\"email\" varchar(64) NOT NULL,
		\"url\" varchar(64) NOT NULL,
		\"date\" datetime NOT NULL,
		\"comment\" longtext NOT NULL,
		\"ip\" char(64),
		\"approved\" tinyint default '1',
		PRIMARY KEY (\"id\"),
		INDEX pid_idx (\"parent_id\"),
		INDEX approved_idx (\"approved\")"
		,"Engine=MyISAM");

		maybe_add_table(
		PLOGGER_TABLE_PREFIX.'config'
		,"\"gallery_name\" varchar(255) NOT NULL,
		\"gallery_url\" varchar(255) NOT NULL,
		\"cdn_url\" varchar(255) NOT NULL,
		\"admin_username\" varchar(64) NOT NULL,
		\"admin_email\" varchar(50) NOT NULL,
		\"admin_password\" varchar(64) NOT NULL,
		\"activation_key\" varchar(64) NOT NULL,
		\"date_format\" varchar(64) NOT NULL,
		\"compression\" int(11) NOT NULL default '75',
		\"thumb_num\" int(11) NOT NULL default '0',
		\"default_sortby\" varchar(20) NOT NULL,
		\"default_sortdir\" varchar(5) NOT NULL,
		\"album_sortby\" varchar(20) NOT NULL,
		\"album_sortdir\" varchar(5) NOT NULL,
		\"collection_sortby\" varchar(20) NOT NULL,
		\"collection_sortdir\" varchar(5) NOT NULL,
		\"allow_dl\" smallint(1) NOT NULL default '0',
		\"allow_comments\" smallint(1) NOT NULL default '1',
		\"allow_print\" smallint(1) NOT NULL default '1',
		\"truncate\" int(11) NOT NULL default '0',
		\"feed_num_entries\" int(15) NOT NULL default '15',
		\"feed_title\" text NOT NULL,
		\"feed_content\" tinyint NOT NULL default '1',
		\"use_mod_rewrite\" tinyint NOT NULL default '0',
		\"comments_notify\" tinyint NOT NULL default '1',
		\"comments_moderate\" tinyint NOT NULL default '0',
		\"theme_dir\" varchar(128) NOT NULL,
		\"thumb_nav_range\" int(11) NOT NULL default '0',
		\"allow_fullpic\" tinyint default '1',
		\"show_exif\" tinyint default '1',
		PRIMARY KEY (\"thumb_num\")"
		,"Engine=MyISAM");

		maybe_add_table(
		PLOGGER_TABLE_PREFIX.'thumbnail_config'
		,"\"id\" int(10) unsigned NOT NULL auto_increment,
		\"update_timestamp\" int(10) unsigned default NULL,
		\"max_size\" int(10) unsigned default NULL,
		\"disabled\" tinyint default '0',
		\"resize_option\" tinyint default '2',
		PRIMARY KEY (\"id\")"
		,"Engine=MyISAM");
	}  else {
		maybe_add_table(
		PLOGGER_TABLE_PREFIX.'collections'
		,"name text NOT NULL default '',
		description text NOT NULL default '',
		path text NOT NULL default '',
		id serial PRIMARY KEY,
		thumbnail_id int NOT NULL DEFAULT '0'"
		,"");

		maybe_add_table(
		PLOGGER_TABLE_PREFIX.'albums'
		,"name text NOT NULL default '',
		id serial PRIMARY KEY,
		description text NOT NULL default '',
		path text NOT NULL default '',
		parent_id int NOT NULL default '0',
		thumbnail_id int NOT NULL default '0'"
		,"");
		maybe_add_index(PLOGGER_TABLE_PREFIX.'albums', "alb_pid_idx", "parent_id");

		maybe_add_table(
		PLOGGER_TABLE_PREFIX.'pictures'
		,"path text NOT NULL default '',
		parent_album int NOT NULL default '0',
		parent_collection int NOT NULL default '0',
		caption text NOT NULL default '',
		description text NOT NULL default '',
		id serial PRIMARY KEY,
		date_modified timestamp NOT NULL,
		date_submitted timestamp NOT NULL,
		exif_date_taken timestamp,
		exif_camera text NOT NULL default '',
		exif_shutterspeed text NOT NULL default '',
		exif_focallength text NOT NULL default '',
		exif_flash text NOT NULL default '',
		exif_aperture text NOT NULL default '',
		exif_iso text NOT NULL default '',
		allow_comments smallint NOT NULL default '1'"
		,"");
		maybe_add_index(PLOGGER_TABLE_PREFIX.'pictures', "pic_pa_idx", "parent_album");
		maybe_add_index(PLOGGER_TABLE_PREFIX.'pictures', "pic_pc_idx", "parent_collection");

		maybe_add_table(
		PLOGGER_TABLE_PREFIX.'comments'
		,"id serial PRIMARY KEY,
		parent_id int NOT NULL default '0',
		author text NOT NULL default '',
		email text NOT NULL default '',
		url text NOT NULL default '',
		date timestamp NOT NULL,
		comment text NOT NULL default '',
		ip inet,
		approved smallint default '1'"
		,"");
		maybe_add_index(PLOGGER_TABLE_PREFIX.'comments', "com_pid_idx", "parent_id");
		maybe_add_index(PLOGGER_TABLE_PREFIX.'comments', "com_approved_idx", "approved");

		maybe_add_table(
		PLOGGER_TABLE_PREFIX.'config'
		,"gallery_name text NOT NULL default '',
		gallery_url text NOT NULL default '',
		cdn_url text NOT NULL default '',
		admin_username text NOT NULL default '',
		admin_email text NOT NULL default '',
		admin_password text NOT NULL default '',
		activation_key text NOT NULL default '',
		date_format text NOT NULL default '',
		compression int NOT NULL default '75',
		thumb_num int NOT NULL default '0' PRIMARY KEY,
		default_sortby text NOT NULL default '',
		default_sortdir text NOT NULL default '',
		album_sortby text NOT NULL default '',
		album_sortdir text NOT NULL default '',
		collection_sortby text NOT NULL default '',
		collection_sortdir text NOT NULL default '',
		allow_dl smallint NOT NULL default '0',
		allow_comments smallint NOT NULL default '1',
		allow_print smallint NOT NULL default '1',
		truncate int NOT NULL default '0',
		feed_num_entries int NOT NULL default '15',
		feed_title text NOT NULL default '',
		feed_content smallint NOT NULL default '1',
		use_mod_rewrite smallint NOT NULL default '0',
		comments_notify smallint NOT NULL default '1',
		comments_moderate smallint NOT NULL default '0',
		theme_dir text NOT NULL default '',
		thumb_nav_range int NOT NULL default '0',
		allow_fullpic smallint default '1',
		show_exif smallint default '1'"
		,"");

		maybe_add_table(
		PLOGGER_TABLE_PREFIX.'thumbnail_config'
		,"id serial PRIMARY KEY,
		update_timestamp int default NULL,
		max_size int default NULL,
		disabled smallint default '0',
		resize_option smallint default '2'"
		,"");
	}

}

function configure_plogger($form) {
	global $PLOGGER_DBH;
	// Use a random timestamp from the past to keep the existing thumbnails
	$long_ago = 1096396500;

	$thumbnail_sizes = array(
		THUMB_SMALL => 100,
		THUMB_LARGE => 500,
		THUMB_RSS => 400,
		THUMB_NAV => 60
	);

	foreach($thumbnail_sizes as $key => $size) {
		$resize = ($key == THUMB_SMALL || $key == THUMB_NAV) ? 3: 2;
		$sql = "INSERT INTO \"".PLOGGER_TABLE_PREFIX."thumbnail_config\" (\"id\", \"update_timestamp\", \"max_size\", \"resize_option\")
		VALUES('$key', '$long_ago', '$size', '$resize')";
		run_query($sql);
	}

	if ($_SERVER['HTTPS'] == "on") { 
		$srvproto = 'https://';
	} else {
		$srvproto = 'http://';
	}

	$config['gallery_url'] = $srvproto.$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']));
	// Remove plog-admin/ from the end, if present .. is there a better way to determine the full url?
	if (strpos($config['gallery_url'], 'plog-admin/')) {
		$config['gallery_url'] = substr($config['gallery_url'], 0, strpos($config['gallery_url'], 'plog-admin/'));
	}
	// Verify that gallery URL contains a trailing slash. if not, add one.
	if ($config['gallery_url'][strlen($config['gallery_url'])-1] != '/') {
		$config['gallery_url'] .= '/';
	}

	$config['admin_username'] = $form['admin_username'];
	$config['admin_password'] = $form['admin_password'];
	$config['admin_email'] = $form['admin_email'];
	$config['gallery_name'] = $form['gallery_name'];

	$config = array_map(array($PLOGGER_DBH, 'quote'), $config);

	$row_exist = run_query("SELECT * FROM \"".PLOGGER_TABLE_PREFIX."config\"");
	$row_exist_num = $row_exist->rowCount();

	if ($row_exist_num == 0) {
		$query = "INSERT INTO \"".PLOGGER_TABLE_PREFIX."config\"
			(\"theme_dir\",
			\"compression\",
			\"thumb_num\",
			\"admin_username\",
			\"admin_email\",
			\"admin_password\",
			\"date_format\",
			\"feed_title\",
			\"gallery_name\",
			\"gallery_url\",
			\"cdn_url\")
			VALUES
			('default',
			75,
			20,
			${config['admin_username']},
			${config['admin_email']},
			MD5(${config['admin_password']}),
			'n.j.Y',
			'Plogger Photo Feed',
			${config['gallery_name']},
			${config['gallery_url']},
			${config['gallery_url']})";
	} else {
		$query = "UPDATE \"".PLOGGER_TABLE_PREFIX."config\" SET
			\"theme_dir\" = 'default',
			\"compression\" = 75,
			\"thumb_num\" = 20,
			\"admin_username\" = ${config['admin_username']},
			\"admin_email\" = ${config['admin_email']},
			\"admin_password\" = MD5(${config['admin_password']}),
			\"date_format\" = 'n.j.Y',
			\"feed_title\" = 'Plogger Photo Feed',
			\"gallery_name\" = ${config['gallery_name']},
			\"gallery_url\" = ${config['gallery_url']}
			\"cdn_url\" = ${config['gallery_url']}";
	}
	run_query($query);

	// Create the FTP columns in the config table if safe_mode enabled/
	if (is_safe_mode() && isset($_SESSION['ftp_values'])) {
		configure_ftp($_SESSION['ftp_values']);
	}

	// Send an email with the username and password
	$from = str_replace('www.', '', $_SERVER['HTTP_HOST']);
	ini_set('sendmail_from', 'noreply@'.$from); // Set for Windows machines
	@mail(
		$config['admin_email'],
		plog_tr('[Plogger] Your new gallery'),
		plog_tr('You have successfully installed your new Plogger gallery.') . "\n\n" .sprintf(plog_tr('You can log in and manage it at %s'), $config['gallery_url'].'plog-admin/') . "\n\n" .plog_tr('Username').': '.$config['admin_username']. "\n" .plog_tr('Password').': '.$config['admin_password'],
		'From: Plogger <noreply@'.$from.'>'
	);
}

function configure_ftp($form) {
	global $PLOGGER_DBH;
	maybe_add_column(PLOGGER_TABLE_PREFIX.'config', 'ftp_host', "varchar(64) NOT NULL");
	maybe_add_column(PLOGGER_TABLE_PREFIX.'config', 'ftp_user', "varchar(64) NOT NULL");
	maybe_add_column(PLOGGER_TABLE_PREFIX.'config', 'ftp_pass', "varchar(64) NOT NULL");
	maybe_add_column(PLOGGER_TABLE_PREFIX.'config', 'ftp_path', "varchar(255) NOT NULL");
	$query = "UPDATE \"".PLOGGER_TABLE_PREFIX."config\" SET
		\"ftp_host\" = ".$PLOGGER_DBH->quote($form['ftp_host']).",
		\"ftp_user\" = ".$PLOGGER_DBH->quote($form['ftp_user']).",
		\"ftp_pass\" = ".$PLOGGER_DBH->quote($form['ftp_pass']).",
		\"ftp_path\" = ".$PLOGGER_DBH->quote($form['ftp_path'])."";
	run_query($query);
}

function fix_open_perms($dirs, $action = 'rename') {
	if (!empty($dirs)) {
		foreach ($dirs as $key => $dir) {
			if ($action == 'delete') {
				kill_dir(PLOGGER_DIR.'plog-content/'.$key);
			} else {
				@rename(PLOGGER_DIR.'plog-content/'.$key, PLOGGER_DIR.'plog-content/'.$key.'-old');
			}
			makeDirs(PLOGGER_DIR.'plog-content/'.$key);
		}
	}
}

function create_config_file($db_host, $db_user, $db_pass, $db_name, $db_type, $db_port) {
	$cfg_file = "<?php\n";
	$cfg_file .= "/* You can manually modify this file before installing (renaming this file to plog-config.php before\n";
	$cfg_file .= " * installation) or you can let Plogger generate the file automatically by running the installation script\n";
	$cfg_file .= " * (run plog-admin/_install.php in your browser).\n\n";
	$cfg_file .= " * If you want to change the database connection information, you may also edit this file manually\n";
	$cfg_file .= " * after Plogger has been installed. */\n\n";
	$cfg_file .= "/* Database hostname */\n";
	$cfg_file .= "define('PLOGGER_DB_HOST', '".$db_host."');\n\n";
	$cfg_file .= "/* Database username */\n";
	$cfg_file .= "define('PLOGGER_DB_USER', '".$db_user."');\n\n";
	$cfg_file .= "/* Database password */\n";
	$cfg_file .= "define('PLOGGER_DB_PW', '".addcslashes($db_pass, "\\'")."');\n\n"; // Escape certain password characters stored in single quotes (\) (')
	$cfg_file .= "/* Database Type\n";
	$cfg_file .= " * Currently supports 'mysql' and 'pgsql' */\n";
	$cfg_file .= "define('PLOGGER_DB_TYPE', '".$db_type."');\n\n";
	$cfg_file .= "/* Database Port (Ignored for MySQL, optional for PgSQL) */\n";
	$cfg_file .= "define('PLOGGER_DB_PORT', '".$db_port."');\n\n";
	$cfg_file .= "/* The name of the database for Plogger */\n";
	$cfg_file .= "define('PLOGGER_DB_NAME', '".$db_name."');\n\n";
	$cfg_file .= "/* Define the Plogger database table prefix. You can have multiple installations in one database if you give\n";
	$cfg_file .= " * each a unique prefix. Only numbers, letters, and underscores are permitted (i.e., plogger_). */\n";
	$cfg_file .= "define('PLOGGER_TABLE_PREFIX', 'plogger_');\n\n";
	$cfg_file .= "/* Define the Plogger directory permissions. Change permissions if you are having issues with images or\n";
	$cfg_file .= " * sub-directories being saved, moved, or deleted from the Plogger-created directories (i.e. Collections\n";
	$cfg_file .= " * or Albums) */\n";
	$cfg_file .= "define('PLOGGER_CHMOD_DIR', 0755);\n\n";
	$cfg_file .= "/* Define the Plogger file permissions. Change permissions if you are having issues with viewing,\n";
	$cfg_file .= " * deleting, or moving images within Plogger (i.e. Pictures) */\n";
	$cfg_file .= "define('PLOGGER_CHMOD_FILE', 0644);\n\n";
	$cfg_file .= "/* Is Plogger embedded in another program, like WordPress?\n";
	$cfg_file .= " * 1/0 (True/False) if set will overrule automatic check */\n";
	$cfg_file .= "define('PLOGGER_EMBEDDED', '');\n\n";
	$cfg_file .= "/* Define a directory path to save session variables if you are having trouble logging in or Plogger is\n";
	$cfg_file .= " * telling you that you have session.save_path issues and/or if your server php.ini setup has a\n";
	$cfg_file .= " * blank session.save_path php.ini variable */\n";
	$cfg_file .= "define('PLOGGER_SESSION_SAVE_PATH', '');\n\n";
	$cfg_file .= "/* Plogger localized language, defaults to English. Change this to localize Plogger.\n";
	$cfg_file .= " * A corresponding MO file for the chosen language must be installed in /plog-content/translations/.\n";
	$cfg_file .= " * For example, upload de.mo to /plog-content/translations/ and set PLOGGER_LOCALE to 'de' to\n";
	$cfg_file .= " * enable German language support.\n";
	$cfg_file .= " * Example language codes: da, de, et, fr, pl, ro, tr, en-CA (for Canadian English) */\n";
	$cfg_file .= "define('PLOGGER_LOCALE', '');\n\n";
	$cfg_file .= "/* Turn on debug mode if trying to troubleshoot issues.\n";
	$cfg_file .= " * 1/0 (True/False) if set will display debug messages at bottom of gallery and admin pages\n";
	$cfg_file .= " * Do not leave this running if gallery is functioning properly. */\n";
	$cfg_file .= "define('PLOGGER_DEBUG', '');\n\n";
	$cfg_file .= "?>";
	return $cfg_file;
}

?>
