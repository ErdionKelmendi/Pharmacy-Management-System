<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: auth/login.php'); exit; }
if ($_SESSION['role'] !== 'admin') { header('Location: index.php'); exit; }
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Customer.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id=(int)($_POST['id']??0);
    if ($id>0) {
        $c=new Customer(); $cust=$c->getById($id);
        if ($cust) { $c->delete($id); $_SESSION['flash']=['type'=>'success','msg'=>"Customer '{$cust['name']}' deleted."]; }
        else { $_SESSION['flash']=['type'=>'error','msg'=>'Customer not found.']; }
    }
}
header('Location: index.php'); exit;
