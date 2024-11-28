<?php
// conexión a bd
$servidor="127.0.0.1:3306";
$bd="animal_therapy";
$usuario="root";
$pass="root";
$con=mysqli_connect($servidor,$usuario,$pass,$bd); 
if(!$con){
    die("Error al conectar: " . mysqli_connect_error());
}
?>
