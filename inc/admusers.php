<?php

if (!defined('UID') || UID != 1) exit();

require('inc/header.php');
?>

<div id="breadcrumb">
    <a href="index.php">Home</a> &#187;
    <a href="?act=admin">Admin</a> &#187;
    <a href="?act=admusers">Admin Users</a>
</div>

<?php
echo '<div id="users">'."\n";
echo '<strong>Users</strong>';
echo '<table cellspacing="2" cellpadding="2">';
$sql = "SELECT * FROM users ORDER BY lower(username)";
foreach($db->query($sql)->fetchAll() as $row) {
    if ($row->uid != 1) {
    echo '<tr><td>'.$row->username.'</td>';
    echo '<td>'.$row->ip.'</td>';
    echo '<td><a href="?act=deluser&uid='.$row->uid.'"><em>Delete</em></a></td>';
    if ($row->ip != "UNKNOWN")
    echo '<td><a href="?act=banuser&uid='.$row->uid.'"><em>Ban</em></a></td></tr>';
    else echo '<td></td></tr>';
    }
}
echo '</table><br>';
echo '<strong>Banned Users</strong>';
echo '<table cellspacing="2" cellpadding="2">';
$sql = "SELECT * FROM banned ORDER BY lower(username)";
foreach($db->query($sql)->fetchAll() as $row) {
    echo '<tr><td>'.$row->username.'</td>';
    echo '<td>'.$row->ip.'</td>';
    echo '<td><a href="?act=unban&bid='.$row->bid.'"><em>UnBan</em></a></td>';
    echo '</tr>';
}
echo '</table>';
$db = null;
echo '</div>'."\n";

require('inc/footer.php');
exit();
