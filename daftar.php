<?php
session_start();
require_once './config/config.php';
require_once 'lib/Mail.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{

  //get data from input form  
	    $data_to_store = filter_input_array(INPUT_POST);
	    $db = getDbInstance();
	   	// validation email and user name
	    $query = "SELECT * FROM users WHERE username = '".$data_to_store['username']."'";
		$username = $db->query($query);

		$query = "SELECT * FROM users WHERE email = '".$data_to_store['email']."'";
		$email = $db->query($query);

		if (!empty($username) || !empty($email)) {
			$_SESSION['failure'] = "Username atau email tidak tersedia";
		    header('location: index.php');
	    	exit();
		}

		$str = 'abcdefghijklmnopqrstuvwxyz1234567890!@$%^*()';
		$shuffled = str_shuffle($str).$data_to_store['username'];


		$data_to_store['role'] = 'users';
		$data_to_store['active'] = '1';
		$data_to_store['active_key'] = $shuffled;

		$data_to_store['password'] = md5($data_to_store['password']);
	    $last_id = $db->insert ('users', $data_to_store);
	    if($last_id)
	    {
	    	
	    	header('location: index.php');
	    	exit();
	    }  
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') 
{
  	//Get DB instance. function is defined in config.php
	$db = getDbInstance();
	// get data load
	$active_key=  filter_input(INPUT_GET, 'active_key');
	$data_to_update['active'] = '1';
	$db->where('active_key',$active_key);
    $stat = $db->update ('users', $data_to_update);

    // return status
    if($stat)
    {
        $_SESSION['success'] = "Akun anda berhasil di aktifkan , silah kan login !!";
    }
    else
    {
        $_SESSION['failure'] = "Gagal mengaktifkan akun , silahkan hubungi administrator !!";
    }

    header('location: login.php');
    exit();
}




?>