<?php 
$servidor = 'swanshine.cpkoaos0ad68.us-east-2.rds.amazonaws.com';
$usuario = 'admin';
$senha = 'gLAHqWkvUoaxwBnm9wKD';  
$banco_de_dados = 'swanshine'; 

$conn = mysqli_connect($servidor, $usuario, $senha, $banco_de_dados); 

if (!$conn) {
    die('Erro ao conectar ao banco de dados: ' . mysqli_connect_error()); 
}
?>
