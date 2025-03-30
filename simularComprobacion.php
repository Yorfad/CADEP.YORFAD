<?php


$usernameDefaultTherapist = 'admin';
$passwordDefaultTherapist = 123;
$usernameDefaultReceptionist = 'admin2';
$passwordDefaultReceptionist = 1234;
$username = $_POST['username'];
$password = $_POST['password'];

if($username == $usernameDefaultReceptionist && $password == $passwordDefaultReceptionist){
    // Si el usuario y la contraseña son correctos, redirigir a la página de inicio
    header('Location: /vistarecepcionista.php');
    exit;
}

elseif($username == $usernameDefaultTherapist && $password == $passwordDefaultTherapist){
    // Si el usuario y la contraseña son correctos, redirigir a la página de inicio
    header('Location: /vistaTerapeuta.php');
    exit;
}	

else {
    // Si el usuario o la contraseña son incorrectos, redirigir a la página de error
    header('Location: /error.php');
    exit;
}

echo $username;
echo $password;
echo 'funciono';

?>