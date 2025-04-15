<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="/js/config.js"></script>
    <script src="/js/login.js" defer></script>
</head>
<body>
    <!-- Contenedor del formulario flotante -->
    <div class="dark-background" id="dark-background">
        <div class="contenedor-formulario">
            <!-- Formulario de inicio de sesión -->
            <form class="form" onsubmit="return login(event)">
                <h1 class="tittle--Big">Iniciar sesión</h1>
                <input placeholder="username" name="username" type="text" class="username" id="username-log-in">
                <input placeholder="password" name="password" type="password" class="password" id="password-log-in">
                <a href="#" class="link">¿Olvidó su contraseña?</a>
                <input type="submit" value="Enviar datos" class="btn btn__form" id="btn-log-in">
            </form>
    </div>
</div>


<script>

</script>



</body>
</html>

