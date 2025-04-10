
-- Archivo en Español: Estructura completa de la base de datos con CUI y validaciones

CREATE TABLE departamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE municipios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    departamento_id INT,
    nombre VARCHAR(100) NOT NULL,
    FOREIGN KEY (departamento_id) REFERENCES departamentos(id)
);

CREATE TABLE sedes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion TEXT,
    municipio_id INT,
    telefono VARCHAR(20),
    FOREIGN KEY (municipio_id) REFERENCES municipios(id)
);

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50)
);

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    usuario VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    rol_id INT,
    departamento_id INT,
    municipio_id INT,
    sede_id INT,
    activo BOOLEAN DEFAULT TRUE,
    sincronizado BOOLEAN DEFAULT FALSE,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id),
    FOREIGN KEY (departamento_id) REFERENCES departamentos(id),
    FOREIGN KEY (municipio_id) REFERENCES municipios(id),
    FOREIGN KEY (sede_id) REFERENCES sedes(id)
);

CREATE TABLE especialidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100)
);

CREATE TABLE terapeutas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    especialidad_id INT,
    sede_id INT,
    cui CHAR(15) UNIQUE NOT NULL,
    CHECK (cui REGEXP '^[0-9]{4} [0-9]{5} [0-9]{4}$'),
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    sincronizado BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (especialidad_id) REFERENCES especialidades(id),
    FOREIGN KEY (sede_id) REFERENCES sedes(id)
);

CREATE TABLE firmas_digitales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    terapeuta_id INT,
    ruta_firma VARCHAR(255),
    firmada_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_firma VARCHAR(50),
    FOREIGN KEY (terapeuta_id) REFERENCES terapeutas(id)
);

CREATE TABLE bitacora_terapeutas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    terapeuta_id INT,
    accion TEXT,
    realizada_por INT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (terapeuta_id) REFERENCES terapeutas(id),
    FOREIGN KEY (realizada_por) REFERENCES usuarios(id)
);

CREATE TABLE pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100),
    cui CHAR(15) UNIQUE NOT NULL,
    fecha_nacimiento DATE,
    sexo ENUM('M','F','Otro'),
    direccion TEXT,
    telefono VARCHAR(20),
    correo VARCHAR(100),
    estudia BOOLEAN DEFAULT FALSE,
    nivel_educativo ENUM('preescolar', 'primaria', 'secundaria', 'bachillerato', 'universidad', 'otro'),
    responsable_id INT DEFAULT NULL,
    sede_id INT,
    activo BOOLEAN DEFAULT TRUE,
    sincronizado BOOLEAN DEFAULT FALSE,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (responsable_id) REFERENCES pacientes(id),
    FOREIGN KEY (sede_id) REFERENCES sedes(id)
);


CREATE TABLE contactos_pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT,
    nombre VARCHAR(100),
    parentesco VARCHAR(50),
    telefono VARCHAR(20),
    correo VARCHAR(100),
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id)
);

CREATE TABLE bitacora_pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT,
    accion TEXT,
    realizada_por INT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id),
    FOREIGN KEY (realizada_por) REFERENCES usuarios(id)
);

CREATE TABLE asignaciones_paciente_terapeuta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    terapeuta_id INT NOT NULL,
    asignado_por INT,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id),
    FOREIGN KEY (terapeuta_id) REFERENCES terapeutas(id),
    FOREIGN KEY (asignado_por) REFERENCES usuarios(id)
);

CREATE TABLE expedientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    sincronizado BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id)
);

CREATE TABLE reportes_visitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expediente_id INT,
    terapeuta_id INT,
    fecha DATE,
    observaciones TEXT,
    siguiente_cita DATE,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    sincronizado BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (expediente_id) REFERENCES expedientes(id),
    FOREIGN KEY (terapeuta_id) REFERENCES terapeutas(id)
);

CREATE TABLE archivos_expediente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reporte_id INT,
    ruta_archivo VARCHAR(255),
    categoria ENUM('examen', 'foto_tratamiento', 'receta_medica', 'otro') DEFAULT 'otro',
    descripcion TEXT,
    subido_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reporte_id) REFERENCES reportes_visitas(id)
);

CREATE TABLE citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT,
    terapeuta_id INT,
    sede_id INT,
    fecha DATETIME,
    motivo TEXT,
    estado ENUM('pendiente', 'realizada', 'cancelada', 'reprogramada') DEFAULT 'pendiente',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id),
    FOREIGN KEY (terapeuta_id) REFERENCES terapeutas(id),
    FOREIGN KEY (sede_id) REFERENCES sedes(id)
);



CREATE TABLE tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expira_en TIMESTAMP NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
