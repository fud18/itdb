<?php 

/* Cory Funk 2018, cafunk_at_ scatcat.fhsu.edu */
$dbfile="../data/itdb.db"; /* sqlite db file */
$cururl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//open db
$dbh = new PDO("sqlite:$dbfile");
$id=$_GET['id'];
$sql=$dbh->query("DELETE FROM vlans WHERE id=$id");

$url = '../index.php?action=listvlans'; // this can be set based on whatever
// no redirect
header( "Location: $url" );
?>