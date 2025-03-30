<?php

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recepción - Calendario</title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/es.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
</head>
<body>
    <h1>Calendario de Terapias</h1>
    
    <!-- Filtro para cambiar entre áreas -->
    <label for="areaSelect">Selecciona un área:</label>
    <select id="areaSelect">
        <option value="todas">Todas</option>
        <option value="fisio">Fisioterapia</option>
        <option value="ocupacional">Terapia Ocupacional</option>
        <option value="lenguaje">Terapia de Lenguaje</option>
    </select>

    <div id="calendar"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let calendarEl = document.getElementById('calendar');
            let calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'es',
                initialView: 'timeGridWeek', 
                events: function(fetchInfo, successCallback, failureCallback) {
                    $.ajax({
                        url: 'index.php?controller=calendar&action=getEvents',
                        method: 'GET',
                        dataType: 'json',
                        data: { area: $("#areaSelect").val() },
                        success: function(response) {
                            successCallback(response);
                        },
                        error: function() {
                            failureCallback();
                        }
                    });
                },
                eventClick: function(info) {
                    alert("Detalles de la cita:\n" + info.event.title + "\nHorario: " + info.event.start);
                }
            });

            calendar.render();

            $("#areaSelect").change(function() {
                calendar.refetchEvents(); // Recargar eventos cuando se cambia el área
            });
        });
    </script>

    <button onclick="window.location.href='index.php?controller=patients&action=new'">Registrar Paciente Nuevo</button>
    <button onclick="window.location.href='index.php?controller=appointments&action=create'">Crear Cita</button>
</body>
</html>
