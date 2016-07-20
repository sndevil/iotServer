<?php
session_start();
include_once('config.php');
$db=dbConnect();
$file=file_get_contents("php://input");
//$filesignup = '{"reqtype":"signup", "name":"ali2","password":"123", "reqtime":"12356"}';
//$fileupload = '{"reqtype":"upload", "uid":"1" , "password":"123","temperature":"30","humidity":"20","pulse":"32", "reqtime":"12356"}';
//$filedownload = '{"reqtype":"download", "uid":"1" , "password":"123", "reqtime":"12356"}';
$input=json_decode($file);

try
{
//var_dump($input->reqtype);
if ($input->reqtype=='signup'){
    //signup user
    //$query = "SELECT * FROM users WHERE Name='".$input->name."'";
    //$result = $db->query($query);
    $count = 0;//(int) $result->fetchColumn();

    if($count == 0){
		$uid = getuid();
		$sql = "INSERT INTO Users (UID, Name, Access, LastOnline, Password) VALUES (".$uid.",'".$input->name."', 'Admin', '".time()."','".md5($input->password)."')";
	 	try
		{
    		   $db->exec($sql);
    		}
    	 	catch (PDOException $e)
    	 	{
    		    echo "There was an error: ".$e->getMessage();
    		    $uid = -1;
    		}
                if ($uid > -1)
                {
                    unset($_SESSION['user']);
                    $_SESSION['user'] = $uid;
                }
    		$output=array("reqtype"=>"signup","uid"=>$uid,"reqtime"=>time());
	}
	else{
		$output=array("reqtype"=>"signup","uid"=>"-1","reqtime"=>time());
	}
        echo(json_encode($output));	
} 
else if ($input->reqtype=='upload')
{
//{"reqtype":"upload", "uid":"1" , "password":"123","temperature":"30","humidity":"20","pulse":"32", "reqtime":"12356"}
        $query = "SELECT * FROM Users WHERE UID='".$input->uid."'";
        $que = $db->query($query);
        $result = $que->fetch(PDO::FETCH_ASSOC);
        $success = 'false';
        if ($result['Password'] == md5($input->password))
        {
            $success = 'true';
            $_SESSION['user'] = $input->uid;
            $sql = "INSERT INTO Data (UID, Time, Temp, Humidity, Pulse) VALUES ('".$input->uid."','".time()."','".$input->temperature."', '".$input->humidity."','".$input->pulse."')";
            //echo("query: ".$sql);
	 	try
		{
    		   $db->exec($sql);
    		}
    	 	catch (PDOException $e)
    	 	{
    		    echo "There was an error: ".$e->getMessage();
    		    $uid = -1;
    		}
        }
        $output=array("reqtype"=>"upload","successful"=>$success,"reqtime"=>time());
        echo(json_encode($output));	
}
else if ($input->reqtype=='download')
{
        $success = 'false';
        if ($_SESSION['user'] == $input->uid)
        {
            $success = 'true';
            $sql = "SELECT * FROM Data WHERE UID = '".$input->uid."' limit 0, 30";
            $ar = array();
            foreach($db->query($sql) as $row)
            {
                array_push($ar,$row);
             }
            //$que = $db->query($sql);
            //$result = $que->fetch(PDO::FETCH_ASSOC);
            //print_r($que);
        }
        $output=array("reqtype"=>"download","table"=>$ar,"reqtime"=>time());
        echo(json_encode($output));	
        
}
}
catch
{
}



function getuid()
{
    global $db;
    $query = "SELECT COUNT(*) FROM Users";
    $result = $db->query($query);
    $count = (int) $result->rowCount();
    return $count;
}
?>