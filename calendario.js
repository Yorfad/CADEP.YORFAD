document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    let calendar;
    let currentArea = 'psicologia';
  
    // Cargar y filtrar las citas
    const cargarCitas = async (area) => {
      const res = await fetch('citas.json');
      const citas = await res.json();
  
      // Agrupar por fecha
      const resumen = {};
      for (const cita of citas) {
        if (cita.area !== area) continue;
        if (!resumen[cita.fecha]) resumen[cita.fecha] = 0;
        resumen[cita.fecha]++;
      }
  
      // Devolver eventos resumen por día
      return Object.entries(resumen).map(([fecha, total]) => ({
        title: `${total} citas`,
        start: fecha,
        allDay: true
      }));
    };
  
    const mostrarCitasDelDia = async (fecha, area) => {
      const res = await fetch('citas.json');
      const citas = await res.json();
      
  
      const eventos = citas
        .filter(c => c.area === area && c.fecha === fecha)
        .map(cita => {
            const start = `${cita.fecha}T${cita.hora_inicio.length === 5 ? cita.hora_inicio + ':00' : cita.hora_inicio}`;
            const end = `${cita.fecha}T${cita.hora_fin.length === 5 ? cita.hora_fin + ':00' : cita.hora_fin}`;
          
            console.log(`Cita: ${cita.id} | start: ${start} | end: ${end}`);
            return {
              id: cita.id,
              title: cita.paciente.nombre,
              start,
              end,
              extendedProps: {
                pacienteId: cita.paciente.id
              }
            };
          });
  
      calendar.changeView('timeGridDay', fecha);
      calendar.removeAllEvents();
      calendar.addEventSource(eventos);
      console.log(eventos)
  
      calendar.setOption('slotMinTime', '08:00:00');
      calendar.setOption('slotMaxTime', '13:30:00');
  
      calendar.setOption('eventClick', function (info) {
        const citaId = info.event.id;
        window.location.href = `detalle-cita.html?id=${citaId}&rol=terapeuta`;
      });
    };

    
  
    const renderCalendar = async (area) => {
      const eventosResumen = await cargarCitas(area);
  
      if (calendar) calendar.destroy();
  
      calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        events: eventosResumen,
        dateClick: function (info) {
          mostrarCitasDelDia(info.dateStr, area);
        },

        slotMinTime: '08:00:00',
        slotMaxTime: '13:00:00',
        slotDuration: '00:30:00',         // 🟢 fuerza bloques de 30 minutos
        slotLabelInterval: '00:30',       // 🟢 asegura que las etiquetas sean cada 30 minutos
        allDaySlot: false,                // 🟢 evita mostrar "all-day"
        nowIndicator: true,               // opcional: muestra línea del tiempo actual
        expandRows: true,                 // 🟢 fuerza que se vea el alto real
        height: 'auto', 
      });
  
      calendar.render();
    };
  
    document.getElementById('areaSelector').addEventListener('change', (e) => {
      currentArea = e.target.value;
      renderCalendar(currentArea);
    });
  
    renderCalendar(currentArea);


  });
  

  let pacientes = []; // Aquí se almacenan temporalmente los pacientes



function modal ()  {
    document.getElementById('modalPaciente').style.display = 'block';
}

function cerrarModal() {
  document.getElementById('modalPaciente').style.display = 'none';
}

function guardarPaciente() {
  const datos = {
    nombre_completo: document.getElementById('nombre_completo').value,
    cui: document.getElementById('cui').value,
    fecha_nacimiento: document.getElementById('fecha_nacimiento').value,
    sexo: document.getElementById('sexo').value,
    direccion: document.getElementById('direccion').value,
    telefono: document.getElementById('telefono').value,
    correo: document.getElementById('correo').value,
    estudia: document.getElementById('estudia').checked ? 1 : 0,
    nivel_educativo: document.getElementById('nivel_educativo').value,
    sede_id: document.getElementById('sede_id').value
  };

  console.log("Enviando:", datos);


  fetch('registrar_paciente.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(datos)
  })
  .then(res => res.json())
  .then(response => {
    if (response.success) {
      alert('Paciente registrado correctamente');
      cerrarModal();
    } else {
      alert('Error: ' + response.message);
    }
  })
  .catch(err => {
    console.error('Error:', err);
    alert('Hubo un problema al registrar el paciente.');
  });
}

