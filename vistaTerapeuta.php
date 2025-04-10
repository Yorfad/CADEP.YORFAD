<?php

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recepción - Calendario</title>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/es.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="calendario.js" defer></script>
    <link rel="stylesheet" type="text/css" href="sass/sass.css"></link>
</head>
<body>
    <h1 class="title">Calendario de Terapias</h1>
    
    <!-- Filtro para cambiar entre áreas -->

    <div class="group group-select">
    <label for="areaSelect">Selecciona un área:</label>

    <select class="select" id="areaSelector">
        <option value="psicologia">Psicología</option>
        <option value="fisioterapia">Fisioterapia</option>
        <option value="linguistica">Lingüística</option>
        <option value="nutricion">Nutrición</option>
        <option value="terapia_ocupacional">Terapia Ocupacional</option>
    </select>
    </div>


    <div id="calendar"></div>

<div class="group group-btn">
    <button class="btn btn-calendar" type="button" onclick="modal()">Registrar Paciente</button>
    <button class="btn btn-calendar" onclick="window.location.href='index.php?controller=appointments&action=create'">Crear Cita</button>
</div>

<!-- Modal Registrar Paciente -->
<div id="modalPaciente" style="display:none; z-index: 1000; position:fixed; top:0; left:0; right:0; bottom:0; background-color:rgba(0,0,0,0.6);">
<div style="background:#fff; width:500px; margin:100px auto; padding:20px; border-radius:10px;">
<h3>Registrar Paciente</h3>

<label>Nombre completo:</label>
<input type="text" id="nombre_completo" style="width:100%" required><br><br>

<label>CUI:</label>
<input type="text" id="cui" style="width:100%" required><br><br>

<label>Fecha de nacimiento:</label>
<input type="date" id="fecha_nacimiento" style="width:100%"><br><br>

<label>Sexo:</label>
<select id="sexo" style="width:100%">
  <option value="M">Masculino</option>
  <option value="F">Femenino</option>
  <option value="Otro">Otro</option>
</select><br><br>

<label>Dirección:</label>
<textarea id="direccion" style="width:100%"></textarea><br><br>

<label>Teléfono:</label>
<input type="text" id="telefono" style="width:100%"><br><br>

<label>Correo:</label>
<input type="email" id="correo" style="width:100%"><br><br>

<label>¿Estudia?</label>
<input type="checkbox" id="estudia"><br><br>

<label>Nivel educativo:</label>
<select id="nivel_educativo" style="width:100%">
  <option value="preescolar">Preescolar</option>
  <option value="primaria">Primaria</option>
  <option value="secundaria">Secundaria</option>
  <option value="bachillerato">Bachillerato</option>
  <option value="universidad">Universidad</option>
  <option value="otro">Otro</option>
</select><br><br>

<label>Sede ID:</label>
<input type="number" id="sede_id" style="width:100%" value="1"><br><br>

<button onclick="guardarPaciente()">Guardar</button>
<button onclick="cerrarModal()">Cancelar</button>
</div>
</div>


</body>
</html>
