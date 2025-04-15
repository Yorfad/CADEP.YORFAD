async function login(event) {
    event.preventDefault(); // Evita que se recargue la página
  
    const usuario = document.getElementById("username-log-in").value;
    const password = document.getElementById("password-log-in").value;
  
    try {
        const res = await fetch(`${API_BASE_URL}/auth/login.php`, {
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