let calendar = null; // ⬅️ Declaración global
let currentArea = document.getElementById('areaSelector').value;


document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    let currentArea = 'psicologia';
  
    // Cargar y filtrar las citas
    const cargarCitas = async (area) => {
      const res = await fetch(`get_citas.php?area=${encodeURIComponent(area)}`);
      const citas = await res.json();
    
      // Agrupar por fecha
      const resumen = {};
      for (const cita of citas) {
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



  const mostrarCitasDelDia = async (fecha, area) => {
    const res = await fetch(`get_citas.php?area=${encodeURIComponent(area)}`);
    const citas = await res.json();
    

    const eventos = citas
    .filter(c => c.area.toLowerCase() === area.toLowerCase() && c.fecha === fecha)
      .map(cita => {
        const start = `${cita.fecha}T${cita.hora_inicio}`;
        const end = `${cita.fecha}T${cita.hora_fin}`;
        
        
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
    console.log("Eventos a agregar en timeGridDay:");
    console.table(eventos);
    calendar.addEventSource(eventos);
    console.log(eventos)
    console.log("Citas recibidas:", citas);

    
    

    calendar.setOption('slotMinTime', '08:00:00');
    calendar.setOption('slotMaxTime', '13:30:00');

    calendar.setOption('eventClick', function (info) {
      const props = info.event.extendedProps;
      const horaInicio = new Date(info.event.start).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      const horaFin = new Date(info.event.end).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    
      document.getElementById('detalleContenido').innerHTML = `
        <p><strong>Paciente:</strong> ${info.event.title}</p>
        <p><strong>Horario:</strong> ${horaInicio} - ${horaFin}</p>
        <p><strong>ID Paciente:</strong> ${props.pacienteId}</p>
      `;
    
      // Cargar estado actual
      document.getElementById('estadoCita').value = props.estado;
    
      // Guardar ID de cita activa
      document.getElementById('estadoCita').dataset.citaId = info.event.id;
    
      document.getElementById('modalDetalleCita').style.display = 'block';
    });
    
    

    
  };
  async function actualizarEstadoCita() {
    const estado = document.getElementById('estadoCita').value;
    const citaId = document.getElementById('estadoCita').dataset.citaId;
    const fechaActual = calendar.getDate().toISOString().split('T')[0];

  
    const res = await fetch('actualizar_estado_cita.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ id: citaId, estado })
    });
  
    const result = await res.json();
    if (result.success) {
      alert('Estado actualizado correctamente');
      cerrarModalDetalle();

      if (!calendar) {
        console.error('Calendario no está definido');
      }
      
      calendar.removeAllEvents();
      mostrarCitasDelDia(fechaActual, currentArea);

    } else {
      alert('Error al actualizar el estado');
    }
  }
  

  let pacientes = []; // Aquí se almacenan temporalmente los pacientes

  function cerrarModalDetalle() {
    document.getElementById('modalDetalleCita').style.display = 'none';
  }

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
    sede_id_paciente: document.getElementById('sede_id_paciente').value
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


function abrirModalCita() {
  document.getElementById('modalCita').style.display = 'block';

  // Espera unos milisegundos para asegurarse que el DOM haya "pintado" el modal
  setTimeout(() => {
    cargarOpciones();
  }, 50); // puedes usar 100ms si aún no funciona
}


function cerrarModalCita() {
  document.getElementById('modalCita').style.display = 'none';
}

async function cargarOpciones() {
  const [pacientes, terapeutas, sedes] = await Promise.all([
    fetch('get_pacientes.php').then(res => res.json()),
    fetch('get_terapeutas.php').then(res => res.json()),
    fetch('get_sedes.php').then(res => res.json())
  ]);

  llenarSelect('paciente_id', pacientes, 'id', 'nombre_completo');
  llenarSelect('terapeuta_id', terapeutas, 'id', 'nombre');
  llenarSelect('sede_id_cita', sedes, 'id', 'nombre');


}

function llenarSelect(id, data, valueKey, labelKey) {
  const select = document.getElementById(id);
  select.innerHTML = data.map(d => `<option value="${d[valueKey]}">${d[labelKey]}</option>`).join('');
}


function guardarCita() {
  const datos = {
    paciente_id: document.getElementById('paciente_id').value,
    terapeuta_id: document.getElementById('terapeuta_id').value,
    sede_id_cita: document.getElementById('sede_id_cita').value,
    fecha: document.getElementById('fecha').value,
    motivo: document.getElementById('motivo').value
  };

  const fecha = document.getElementById('fecha').value;
  const hora = document.getElementById('hora').value;
  datos.fecha = `${fecha} ${hora}:00`;

  fetch('agendar_cita.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(datos)
  })
  .then(res => res.json())
  .then(response => {
    if (response.success) {
      alert('Cita agendada correctamente');
      cerrarModalCita();
    } else {
      alert('Error al agendar: ' + response.message);
    }
  })
  .catch(err => {
    console.error(err);
    alert('Hubo un error al intentar guardar la cita.');
  });

  if (response.success) {
    alert('Cita agendada correctamente');
    cerrarModalCita();
  
    // 🔄 Actualizar el calendario
    calendar.refetchEvents(); // si usas event source
  }
  
}


//*  parte de la conexion a bd no simulada

async function login(event) {
  event.preventDefault(); // Evita que se recargue la página

  const usuario = document.getElementById("username-log-in").value;
  const contrasena = document.getElementById("password-log-in").value;

  try {
      const res = await fetch("http://localhost/api/auth/login", {
          method: "POST",
          headers: {
              "Content-Type": "application/json"
          },
          body: JSON.stringify({ usuario, contrasena })
      });

      const data = await res.json();

      if (!res.ok) {
          alert(data.mensaje || "Credenciales incorrectas");
          return;
      }

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