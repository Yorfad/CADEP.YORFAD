<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

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
    //*  parte de la conexion a bd no simulada

async function login(event) {
  event.preventDefault(); // Evita que se recargue la página

  const usuario = document.getElementById("username-log-in").value;
  const password = document.getElementById("password-log-in").value;

  try {
      const res = await fetch("http://localhost:3000/api/auth/login.php", {
          method: "POST",
          headers: {
              "Content-Type": "application/json"
          },
          body: JSON.stringify({ usuario, password })
      });

      const data = await res.json();

      if (!res.ok) {
          alert(data.mensaje || "Credenciales incorrectas");
          return;
      }

      alert ("Bienvenido " + data);

      // Guardar token en localStorage para futuras peticiones
      localStorage.setItem("token", data.token);
      localStorage.setItem("usuario", JSON.stringify(data.usuario));

      // Redireccionar según el rol
      if (data.usuario.rol === "recepcionista") {
          window.location.href = "/vistarecepcionista.php";
      } else if (data.usuario.rol === "terapista" || data.usuario.rol === "terapeuta") {
          window.location.href = "/vistaTerapeuta.php";
      } else {
          alert("Rol no reconocido: " + data.usuario.rol);
      }

  } catch (error) {
      console.error("Error de conexión:", error);
      alert("Error al intentar iniciar sesión.");
  }

  return false;
}
</script>



</body>
</html>

