<?php
    try {
        // Set your plogger table prefix here
        $table_prefix = 'plogger_';
        
        // Configure a PDO object here
        // For more information see https://secure.php.net/manual/en/ref.pdo-mysql.connection.php
        $db = new PDO('mysql:host=localhost;dbname=plogger;charset=utf8', 'database_user', 'database_pass');
        
        // Turn errors into exceptions
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Default to mysql_fetch_assoc style returns for compatibility
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
       
        // Make MySQL more SQL compliant, using less of its own weird syntax
        $db->query("SET SESSION sql_mode = 'ANSI'");
        
        // Add column for show/hide exif to config table
        $db->query('ALTER TABLE ' . $table_prefix . "config ADD show_exif tinyint default '1'");

        // Add column for CDN URL to config table
        $db->query('ALTER TABLE ' . $table_prefix . "config ADD cdn_url varchar(255) utf8_general_ci");

        // Fix any existing exif data
        $pics = $db->query('SELECT id,exif_date_taken FROM ' . $table_prefix . 'pictures');
        $fixed_exif = array();
        while ($row = $pics->fetch()) {
            if (empty(trim($row['exif_date_taken']))) {
                $newdate = 'NULL';
            } else {
                $newdate = $db->quote(preg_replace('/:/', '-', $row['exif_date_taken'], 2));
            }        
            $fixed_exif[$row['id']] = $newdate;
        }
        $db->query('ALTER TABLE ' . $table_prefix . "pictures DROP exif_date_taken");
        $db->query('ALTER TABLE ' . $table_prefix . "pictures ADD exif_date_taken timestamp NULL");
        foreach($fixed_exif as $id => $date) {
           $db->query('UPDATE ' . $table_prefix . "pictures  SET exif_date_taken=" . $date ." WHERE id='" . $id . "'");
        }
        echo "DB Schema Updated Successfully.";
    } catch (PDOException $ex) {
        echo "A database error occurred. <br />Error details: " . $ex->getMessage();
        exit();
    }
?>
