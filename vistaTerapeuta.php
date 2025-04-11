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
        <option value="psicologia">Psicologia</option>
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



</body>
</html>
