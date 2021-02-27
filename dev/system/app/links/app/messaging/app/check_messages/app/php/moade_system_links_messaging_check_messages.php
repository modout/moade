<?php

/* 
 * BOOT
 * -- CHECK IF STATUS FILE EXISTS
 * -- IF TRUE
 * ---  CHECK IF STATUS SCHEMA FILE EXISTS
 * ---  IF TRUE
 * ----     GET CURRENT STATUS FROM STATUS SCHEMA
 * ---- ELSE 
 * ---      GET CURRENT STATUS FROM STATUS FILE
 * ---  IF CURRENT STATUS IS SCHEMA_EXISTS
 * ----     SEND MESSAGE TO DATA TO ASK FOR QUEUES MESSAGES
 * ----     CHANGE STATUS TO CHECK_MESSAGES IN STATUS SCHEMA
 * ---  ELSE IF CURRENT STATUS IS CREATE_SCHEMA
 * ----     GOTO CREATE_SCHEMA
 * ---  ELSE IF CURRENT STATUS IS CHECK_SCHEMA_STATUS
 * ----     CHECK IF QUEUE FILE HAS MESSAGE
 * ----     IF TRUE
 * -----        CHECK IF MESSAGE IS SCHEMA_CREATED
 * -----        IF TRUE
 *                  CREATE APP (EXPLODE BY MESSAGING) QUEUE, INBOX AND STATUS SCHEMA FILES
 * ------           CHANGE STATUS TO SCHEMA_EXISTS
 * ---  ELSE IF CURRENT STATUS IS CHECK_DATA_STATUS
 * ----     CHANGE STATUS TO CREATE_SCHEMA
 * ---  ELSE IF CURRENT STATUS IS CHECK_MESSAGE_STATUS
 * ----     CHANGE STATUS TO CHECK_MESSAGES
 * ---  ELSE IF CURRENT STATUS IS CHECK_MESSAGES
 * ----     GOTO CHECK_MESSAGES
 * ---  ELSE
 * ----     CHANGE STATUS TO CHECK_MESSAGES
 * --  
 * -- 
 * -- ELSE 
 * ---  CREATE STATUS FILE
 * ---  CREATE QUEUE FILE
 * ---  CREATE INBOX FILE
 * ---  CHANGE STATUS TO INIT IN STATUS FILE
 * 
 * INIT
 * -- CHECK IF SERVICE SCHEDULED IN CRON BY CHECKING CRON FILE
 * -- IF CRON FILE DOES NOT EXIST
 * ---  CREATE CRON FILE
 * --
 * -- CHANGE STATUS TO CREATE_SCHEMA IN STATUS FILE
 * -- GOTO CREATE SCHEMA  
 * 
 * CREATE SCHEMA
 * -- CHECK IF DATA SERVICE IS ACTIVE (RUNNING)
 * -- IF TRUE
 * ---  SEND MESSAGE TO DATA TO CREATE MESSAGE QUEUE, INBOX AND STATUS SCHEMA IF NOT EXIST - WRITE INTO DATA SCHEMA QUEUE
 * ---  CHANGE STATUS TO CHECK_SCHEMA_STATUS IN STATUS FILE
 * -- ELSE
 * ---  SEND MESSAGE TO SYSTEM TO START DATA SERVICE - WRITE INTO SYSTEM QUEUE FILE
 * ---  CHANGE STATUS TO CHECK_DATA_STATUS IN STATUS FILE
 * 
 * CHECK MESSAGES
 * -- CHECK IF DATA SERVICE IS ACTIVE (RUNNING)
 * -- IF TRUE
 * ---  SEND MESSAGE TO DATA TO MOVE QUEUED MESSAGES TO INBOX
 * -- ELSE
 * ---  SEND MESSAGE TO SYSTEM TO START DATA SERVICE - WRITE INTO SYSTEM FILE QUEUE
 * ---  CHANGE STATUS TO CHECK_MESSAGE_STATUS IN STATUS FILE  
 */



//GLOBAL VARIABLES
//VARIABLE
define('BLANK', "blank");
define('MYSQL', "mysql");
define('MONGO', "mongo");
define('LAPTOP', "laptop");
define('NOTEBOOK', "notebook");
define('DESKTOP', "desktop");
define('TABLET', "tablet");
define('PHONE', "phone");

//MOADE
define('DATA', "data");
define('SYSTEM', "system");
define('USER', "user");
define('SECURITY', "security");
define('APP', "app");

//INITIALISE
//ROOT_PATH
$root_dir = exec('pwd');
//APPNAME
$appname = basename(__FILE__, '.php');
$apptokens = explode('_', $appname);
//SYSNAME
$sysname = $apptokens[0] == 'user' ? $apptokens[0]."_".$apptokens[1]."_".$apptokens[2] : $apptokens[0];
//DB
$DB_CONFIG_FILE = "../../data/files/config/.db";
if (file_exists($DB_CONFIG_FILE)){
    $db = exec("cat $DB_CONFIG_FILE");
} else {
    //LOAD DEFAULT SYSNAME DB
    $root_tokens = explode($sysname, ROOT_PATH);
    $db = exec("cat ".$root_tokens[0]."/$sysname/data/files/config/.db");
    $db_file = fopen($DB_CONFIG_FILE, "w") or die("Unable to open file $DB_CONFIG_FILE !!!");
    fwrite($db_file, "$db");
    fclose($db_file);
}
//DBTOOL
$DBTOOL_CONFIG_FILE = "../../data/files/config/.dbtool";
if (file_exists($DBTOOL_CONFIG_FILE)){
    $dbtool = exec("cat $DBTOOL_CONFIG_FILE");
} else {
    //LOAD DEFAULT DBTOOL
    $dbtool = MYSQL;
    $dbtool_file = fopen($DBTOOL_CONFIG_FILE, "w") or die("Unable to open file $DBTOOL_CONFIG_FILE !!!");
    fwrite($dbtool_file, "$dbtool");
    fclose($dbtool_file);
}
//DBTOOL_ACCESS

switch(DBTOOL){
    case MYSQL:
        $root_tokens = explode(DATA, ROOT_PATH);
        $dbtool_access = $root_tokens[0]."/data/files/config/.mysql.cnf"; 
        break;
    
    case MONGO:
        
        break;
    
    default:
        $root_tokens = explode(DATA, ROOT_PATH);
        $dbtool_access = $root_tokens[0]."/data/files/config/.mysql.cnf";
        break;
}
define ('DBTOOL_ACCESS', $dbtool_access);

$DEVICE_CONFIG_FILE = "../../data/files/config/.device";
if (file_exists($DEVICE_CONFIG_FILE)){
    $device = exec("cat $DEVICE_CONFIG_FILE");
} else {
    //LOAD DEFAULT DBTOOL
    $device = LAPTOP;
    $device_file = fopen($DEVICE_CONFIG_FILE, "w") or die("Unable to open file $DEVICE_CONFIG_FILE !!!");
    fwrite($device_file, "$device");
    fclose($device_file);
}
//SYSTEM
define('SYSNAME', $sysname);
//DEVICE
define('DEVICE', $device);
//DB
define('DB', $db);
//DBTOOL
define('DBTOOL', $dbtool);
//PATH
define('ROOT_PATH', $root_dir);
//APP
define('APPNAME', $appname);
//FILES
define('STATUS_FILE', "../../data/files/code/.".APPNAME."_status_file");
define('QUEUE_FILE', "../../data/files/code/.".APPNAME."_queue_file");
define('INBOX_FILE', "../../data/files/code/.".APPNAME."_inbox_file");
define('STATUS_SCHEMA_FILE', "../../data/files/code/.".APPNAME."_status_schema");
define('QUEUE_SCHEMA_FILE', "../../data/files/code/.".APPNAME."_queue_schema");
define('INBOX_SCHEMA_FILE', "../../data/files/code/.".APPNAME."_inbox_schema");
define('CRON_FILE', "../../data/files/code/.".APPNAME.".cron");
define('LOG_FILE', "../../data/files/code/.".APPNAME.".log");
//LOG
define('LOG_LIMIT', 10);
//STATUSES
define('SCHEMA_EXISTS', "schema_exists");
define('CHECK_SCHEMA_MESSAGES', "check_schema_messages");
define('SCHEMA_CONNECTED', "schema_connected");
define('CREATE_SCHEMA', "create_schema");
define('CHECK_SCHEMA_STATUS', "check_schema_status");
define('CHECK_DATA_STATUS', "check_data_status");
define('CHECK_MESSAGE_STATUS', "check_message_status");
define('CHECK_MESSAGES', "check_messages");
define('INIT', "init");
//STATUS TYPE
define('FILE_STATUS', "file_status");
define('SCHEMA_STATUS', "schema_status");
//STATUS PATH
define('CURRENT_STATUS', "current_status");
//MESSAGE TYPE
define('FILE_MESSAGE', "file_message");
define('SCHEMA_MESSAGE', "schema_message");


//NOTE IF ADDITIONAL PARAMETERS REQUIRED 
/*if (!isset($_SERVER["HTTP_HOST"])) {
  parse_str($argv[1], $_GET);
  parse_str($argv[1], $_POST);
}

extract($_GET);
extract($_POST);*/

//CHECK IF STATUS FILE EXISTS
$messages = array ();
if (file_exists(STATUS_FILE)){
   //NOTE THAT WE CAN CHECK IF DATA SERVICE IS ACTIVE AND SCHEMA EXITS IN ORDER TO GET CURRENT STATUS FROM SCHEMA_STATUS
   if (file_exists(STATUS_SCHEMA_FILE)){
       $status = get_status(SCHEMA_STATUS);
   } else {  
       $status = get_status(FILE_STATUS);
   }
   add_log("CURRENT STATUS $status");
   switch($status){
       
       case SCHEMA_EXISTS:
            $messages [] = "-1!#!".APPNAME."!@!".DATA."!@!check!:!messages!-!".APPNAME."!:!queue!~!SEND";
            add_message_inbox($messages);
            change_status(SCHEMA_STATUS, CHECK_MESSAGES, CURRENT_STATUS);
            break;
       
       case CHECK_MESSAGES:
            if (file_exists(STATUS_SCHEMA_FILE)){
                 check_messages(SCHEMA_MESSAGE);
            } else {
                 check_messages(FILE_MESSAGE);
            }
            break;
       
       case CREATE_SCHEMA:
            create_schema();
            break;
       
       case CHECK_SCHEMA_STATUS:
            check_schema_status();
            break;
       
       case CHECK_DATA_STATUS:
           create_schema();
            break;
       
       case CHECK_MESSAGE_STATUS:
            if (file_exists(STATUS_SCHEMA_FILE)){
                 check_messages(SCHEMA_MESSAGE);
            } else {
                 check_messages(FILE_MESSAGE);
            }
            break;
       
       case INIT:
           init();
           break;
       
       
       default:
           add_log("STATUS $status UNKNOWN");
           break;
   }
} else {
   change_status(FILE_STATUS, INIT); 
   
   //send_message(FILE_MESSAGE, BLANK);
   //read_message(FILE_MESSAGE, BLANK);
} 

//INIT
function init(){
    add_cron();
    change_status(FILE_STATUS, CREATE_SCHEMA);
    
}

//CREATE SCHEMA
function create_schema(){
    $messages = array ();
    if (data_service_active()){
        $message[] = "-1#".APPNAME."!@!".DATA."!@!create!:!schema!-!".APPNAME."!:!queue!,!inbox!,!status!~!SEND";
        add_message_inbox($messages);
        //send_message(SCHEMA_MESSAGE, $message);
        change_status(FILE_STATUS, CHECK_SCHEMA_STATUS);
    } else {
        $message[] = "-1#".APPNAME."!@!".SYSTEM."!@!start!-!data_service!~!SEND";
        add_message_inbox($messages);
        //send_message(FILE_MESSAGE, $message);
        change_status(FILE_STATUS, CHECK_DATA_STATUS);
    }
}

//CHECK MESSAGES
function check_messages($type, $return=null){
    $messages = array ();
    switch ($type) {
        case FILE_MESSAGE:
            $queue_file = fopen(QUEUE_FILE, "r");
            if ($queue_file) {
                $messages = get_messages($queue_file);
                fclose($queue_file);
            } else {
                die("Unable to open file ".QUEUE_FILE." !!!");
            }
            
            if (!is_null($return)){
                return $messages;
            } else {
                //FOREACH MESSAGE WITH STATUS QUEUED
                //---- ADD MESSAGE TO INBOX FILE OR SCHEMA
                add_message_inbox($messages);
            }

            break;
        
        case SCHEMA_MESSAGE:
            $queue_schema = exec("cat ".QUEUE_SCHEMA_FILE);
            $inbox_schema = exec("cat ".INBOX_SCHEMA_FILE);
            $message = "-1!#!".APPNAME."!@!".DATA."!@!copy!:!select!-!$queue_schema!2!$inbox_schema!~!SEND";
            //$inbox_file = INBOX_FILE;
            $messages [] = $message;
            add_message_inbox($messages);
            //shell_exec("echo $message | cat >> ".INBOX_FILE);
            
            break;

        default:
            break;
    }
    
}

//ADD INBOX
function add_message_inbox($messages){
    foreach($messages as $mid=>$message){
        shell_exec("echo $message | cat >> ".INBOX_FILE);
    }
}

//GET MESSAGES
function get_messages($source){
    $messages = array ();
    $count = 1;
    while (($line = fgets($source)) !== false) {
        if (trim($line) == BLANK && $count == 1){
            return array ();
        } elseif (trim($line) == BLANK && $count > 1){
            //DO NOTHING
        } else {
            $messages[] = $line;
        }
        $count++;
    }
    return $messages;
    
}

//SCHEMA MYSQL GET MESSAGES
function schema_mysql_get_messages(){
    $messages = array ();
    $queue_schema = exec("cat ".QUEUE_SCHEMA_FILE);
    $cmd = "mysql --defaults-extra-file=".DBTOOL_ACCESS." -uroot -e \"use ".DB."; SELECT COUNT(message_data) FROM $queue_schema WHERE message_data LIKE '%QUEUED%'";
    $count = exec($cmd);
    if ($count > 0){
        $inbox_schema = exec("cat ".INBOX_SCHEMA_FILE);
        //SEND MESSAGE TO DATA TO COPY QUEUED MESSAGES TO INBOX SCHEMA 
        
        send_message(SCHEMA_MESSAGE, $message);
    }
}

//CHECK SCHEMA STATUS
function check_schema_status(){
    $messages = check_messages($type, 'return');
    if (!empty($messages)){
        $found = FALSE;
        foreach($messages as $mid=>$message){
            if (strpos($message,"SCHEMA_CREATED") !== FALSE){
                $message_tokens = explode("!-!", $message);
                $schema_tokens = explode("!~!", $message_tokens[1]);
                $schema_items = explode("!,!", $schema_tokens[0]);
                //CREATE QUEUE SCHEMA FILE
                $queue_schema = $schema_items[0];
                $queue_schema_file = fopen(QUEUE_SCHEMA_FILE, "w") or die("Unable to open file ".QUEUE_SCHEMA_FILE." !!!");
                fwrite($queue_schema_file, "$queue_schema");
                fclose($queue_schema_file);
                //CREATE INBOX SCHEMA FILE
                $inbox_schema = $schema_items[1];
                $inbox_schema_file = fopen(INBOX_SCHEMA_FILE, "w") or die("Unable to open file ".INBOX_SCHEMA_FILE." !!!");
                fwrite($inbox_schema_file, "$inbox_schema");
                fclose($inbox_schema_file);
                //CREATE STATUS SCHEMA FILE
                $status_schema = $schema_items[2];
                $status_schema_file = fopen(STATUS_SCHEMA_FILE, "w") or die("Unable to open file ".STATUS_SCHEMA_FILE." !!!");
                fwrite($status_schema_file, "$status_schema");
                fclose($status_schema_file);
                
                $found = TRUE;
                break;
            }
        }
        if ($found){
            change_status(FILE_STATUS, SCHEMA_EXISTS);
        }
    }
}

//READ MESSAGE
function read_message($type, $message){
   
    switch($type){
        
        case FILE_MESSAGE:
            $inbox_file = fopen(INBOX_FILE, "w") or die("Unable to open file ".INBOX_FILE." !!!");
            fwrite($inbox_file, "$message");
            fclose($inbox_file);
            break;
        
        case SCHEMA_MESSAGE:
            
            break;
        
        default:
            
            break;
    }
}

//SEND MESSAGE
function send_message($type, $message){

    $message_tokens = explode("!@!", $message);
    switch($type){
        
        case FILE_MESSAGE:
            switch ($message_tokens[0]){
                case BLANK:
                    $queue_file = fopen(QUEUE_FILE, "w") or die("Unable to open file ".QUEUE_FILE." !!!");
                    fwrite($queue_file, "$message");
                    fclose($queue_file);
                    break;
                
                case APPNAME:
                    push_message($message_tokens[1], "$message!~!QUEUED");
                    break;
                
                default:
                    $queue_file = fopen(QUEUE_FILE, "w") or die("Unable to open file ".QUEUE_FILE." !!!");
                    fwrite($queue_file, "$message");
                    fclose($queue_file);
                    break;
                            
            }
            break;
        
        case SCHEMA_MESSAGE:
       
            $send_message = "-1#".APPNAME."!@!".DATA."!@!send!::!$message";
            switch(DBTOOL){
            
                case MYSQL:                
                    schema_mysql_push_message($message_tokens[1], $message);
                    break;
                
                case MONGO:
                    
                    break;
                
                default:
                    
                    break;
            }    
            break;
        
        default:
            
            break;
        
    }
}

//SCHEMA PUSH MESSAGE
function schema_mysql_push_message($destination, $message){
    //GET QUEUE SCHEMA FOR THE DESTINATION
    //$queue_schema = exec("cat ".QUEUE_SCHEMA_FILE);//THIS IS THE QUEUE SCHEMA FOR APPNAME
    $queue_schema = "NONE";
    $cmd = "mysql --defaults-extra-file=".DBTOOL_ACCESS." -uroot -e \"use ".DB."; SELECT queue_data FROM ".SYSNAME."_schema_appdata_queue WHERE queue_data LIKE '%APPNAME=$destination%'";
    $queue_data_tokens = explode('|', exec($cmd));
    foreach($queue_data_tokens as $qdtid=>$queue_data_item){
        if (strpos($queue_data_item, "QUEUE_SCHEMA") !== FALSE){
            $queue_schema_item_tokens = explode("=", $queue_data_item);
            $queue_schema = $queue_schema_item_tokens[1];
            $cmd = "mysql --defaults-extra-file=".DBTOOL_ACCESS." -uroot -e \"use ".DB."; INSERT INTO $queue_schema VALUES (NULL, '$message!~!QUEUED', NULL)\"";
            shell_exec($cmd);
            break;
        }
    }
   
}

//FILE PUSH MESSAGE
function push_message($type, $message){
    $root_tokens = explode("$type", ROOT_PATH);
    switch ($type){
        
        case DATA:
            push_message(SYSTEM, $message);
            break;
        
        case SYSTEM:
            $SYSTEM_QUEUE_FILE = $root_tokens[0]."/data/files/code/.".SYSTEM."_queue";
            if (file_exists($SYSTEM_QUEUE_FILE)){
                shell_exec("echo $message | cat >> $SYSTEM_QUEUE_FILE");
            } else {
                shell_exec("echo $message | cat > $SYSTEM_QUEUE_FILE");
            }
            break;
        
        case SECURITY:
            push_message(SYSTEM, $message);
            break;
        
        case USER:
            push_message(SYSTEM, $message);
            break;
        
        case APP:
            push_message(SYSTEM, $message);
            break;

        default:
            push_message(SYSTEM, $message);
            break;
    }
}
//STATUS
function change_status($type, $status, $path=null){
    
    switch($type){
        
        case FILE_STATUS:
            $status_file = fopen(STATUS_FILE, "w") or die("Unable to open file ".STATUS_FILE." !!!");
            fwrite($status_file, "$status");
            fclose($status_file);
            break;
        
        case SCHEMA_STATUS:
            //CHECK IF STATUS EXISTS WITH GIVEN PATH
            //IF TRUE
            //--- UPDATE STATUS 
            //ELSE
            //--- INSERT NEW STATUS WITH GIVEN PATH
            $status_schema = exec("cat ".STATUS_SCHEMA_FILE);
            $cmd = "mysql --defaults-extra-file=".DBTOOL_ACCESS." -uroot -e \"use ".DB."; SELECT COUNT(status_data) FROM $status_schema WHERE status_path = '$path'";
            $count = exec($cmd);
            if ($count > 0){
                $cmd = "mysql --defaults-extra-file=".DBTOOL_ACCESS." -uroot -e \"use ".DB."; UPDATE $status_schema SET status_data = '$status' WHERE status_path = '$path')";
            } else {
                $cmd = "mysql --defaults-extra-file=".DBTOOL_ACCESS." -uroot -e \"use ".DB."; INSERT INTO $status_schema VALUES (NULL, '$path', '$status')";
            }
            exec($cmd);
            break;
        
        default:
            
            break;
    }
}

function get_status($type, $path=null){
    
    switch ($type){
        
        case FILE_STATUS:
            $status_file = fopen(STATUS_FILE, "r") or die("Unable to open file ".STATUS_FILE." !!!");
            $status = fgets($status_file);
            fclose($status_file);
            break;
        
        case SCHEMA_STATUS:
            $status_schema = exec("cat ".STATUS_SCHEMA_FILE);
            $cmd = "mysql --defaults-extra-file=".DBTOOL_ACCESS." -uroot -e \"use ".DB."; SELECT COUNT(status_data) FROM $status_schema WHERE status_path = '$path'";
            $count = exec($cmd);
            if ($count > 0){
                $cmd = "mysql --defaults-extra-file=".DBTOOL_ACCESS." -uroot -e \"use ".DB."; SELECT status_data FROM $status_schema WHERE status_path = '$path'";
                $status = exec($cmd);
            } else {
                $status = "NONE";
            }
            break;
        
        default:
            
            break;
    }
    return $status;
}

//CRON
function add_cron(){
    if (!file_exists(CRON_FILE)) {
            $cron_data = "* * * * * flock -n /tmp/".APPNAME.".lockfile php ".ROOT_PATH."/".APPNAME.".php  >> ".LOG_FILE."\n";
            $cron_data .= "\n";
            create_cron(CRON_FILE, $cron_data);
    }
}

function create_cron($file, $cron_data){

        //$current_dir = shell_exec('pwd');
	$CRON_FILE = fopen("$file", "w") or die("Unable to open file $file !!!");
	fwrite($CRON_FILE, $cron_data);
	fclose($CRON_FILE);
	
}

//LOG
function add_log($notice){

    	//CHECK LOG FILE SIZE
	manage_log('log_size');
		
	$time = time();
    	$time = date("Y-m-d H:i:s", $time);
	echo "$time: ".APPNAME." - $notice\n";
	//exit;
}

function manage_log($action){

	switch($action){

		case 'log_size':
			if (file_exists(LOG_FILE)) {
				$log_size = shell_exec("du -h ".LOG_FILE); 
				$log_size_tokens = explode(' ', $log_size);
				$log_size = trim($log_size_tokens[0]);
				if (strpos($log_size, 'M') !== FALSE){
					$log_size_tokens = explode('M', $log_size);
					if (intval($log_size_tokens[0]) > LOG_LIMIT){
						shell_exec("rm -f ".LOG_FILE);
					}
				}
			}
			break;

		default:
			break;
	}	
}

//DATA
function data_service_active(){
    $data_status = exec("service ".DBTOOL." status | awk '{ print $2 }' | grep active");
    if (strcmp(trim($data_status), "active") == 0){
       return TRUE; 
    } else {
        return FALSE;
    }
}