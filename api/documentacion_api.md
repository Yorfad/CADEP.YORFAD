
# 📂 Documentación Final - API del Sistema Clínico

---

## 📊 Diagrama de Base de Datos (UML)

Archivo: `uml_db_diagram.png`

Incluye:
- usuarios, roles, tokens
- pacientes, contactos
- terapeutas, especialidades
- sedes, municipios, departamentos
- citas, reportes clínicos, recetas, archivos, firmas
- bitácora

---

## 🔐 Requisitos del Servidor

- Servidor Apache con mod_rewrite habilitado
- PHP 8.0+
- MariaDB 10+
- Composer instalado (para Dompdf)
- Extensiones PHP necesarias: `pdo`, `mbstring`, `gd`
- Permisos de escritura en la carpeta `/uploads`

---

## 📁 Estructura de Carpetas (API con enrutador central)

```
/api
├── index.php               <-- Enrutador central
├── .htaccess               <-- Redirecciona todo a index.php
├── /auth
│   ├── login.php
│   ├── logout.php
│   └── validate.php
├── /config
│   └── database.php
├── /core
│   ├── Response.php
│   └── Utils.php (opcional)
├── /uploads
│   ├── reportes
│   └── firmas
├── /endpoints
│   ├── /usuarios
│   ├── /pacientes
│   ├── /terapeutas
│   ├── /especialidades
│   ├── /sedes
│   ├── /departamentos
│   ├── /municipios
│   ├── /citas
│   ├── /reportes
│   ├── /reportes_archivos
│   ├── /recetas
│   ├── /firmas_terapeutas
│   ├── /bitacora
│   └── /expediente
```

---

## 📡 Endpoints por Módulo (con ejemplos)

### 🔑 Autenticación
| Método | Endpoint          | Descripción                  |
|--------|-------------------|------------------------------|
| POST   | /auth/login.php   | Inicia sesión y retorna token (incluye sede) |
| POST   | /auth/logout.php  | Cierra sesión y elimina token |

### 👥 Usuarios
| Método | Endpoint                     | Acción                        |
|--------|------------------------------|--------------------------------|
| POST   | /usuarios/post.php           | Crear usuario                  |
| GET    | /usuarios/get.php            | Listar usuarios activos        |
| GET    | /usuarios/get_inactivos.php  | Listar usuarios inactivos      |
| PUT    | /usuarios/put.php            | Actualizar usuario             |
| DELETE | /usuarios/delete.php         | Desactivar usuario             |
| PUT    | /usuarios/activar.php        | Activar usuario                |
| PUT    | /usuarios/cambiar_contrasena.php | Cambiar contraseña         |

### 👨‍⚕️ Terapeutas / Pacientes / Especialidades / Sedes / Municipios / Departamentos
- Todos estos módulos siguen la misma estructura que `/usuarios`
- `pacientes/get.php` y `terapeutas/get.php` filtran automáticamente por sede

### 📋 Reportes clínicos
| Método | Endpoint                     | Acción                           |
|--------|------------------------------|----------------------------------|
| POST   | /reportes/post.php           | Crear reporte                    |
| GET    | /reportes/get.php            | Listar reportes activos (filtrado por sede) |
| GET    | /reportes/get_by_paciente.php| Listar por paciente              |
| PUT    | /reportes/put.php            | Actualizar reporte               |
| DELETE | /reportes/delete.php         | Desactivar reporte               |
| PUT    | /reportes/activar.php        | Reactivar reporte                |

### 📁 Archivos en reportes
| Método | Endpoint                           | Acción                        |
|--------|------------------------------------|-------------------------------|
| POST   | /reportes_archivos/upload.php      | Subir archivo                 |
| GET    | /reportes_archivos/get_by_reporte.php | Obtener archivos por reporte |
| DELETE | /reportes_archivos/delete.php      | Eliminar archivo              |

### 📅 Citas
| Método | Endpoint                      | Acción                         |
|--------|-------------------------------|--------------------------------|
| POST   | /citas/post.php               | Crear cita                     |
| GET    | /citas/get.php                | Listar citas activas (filtrado por sede) |
| GET    | /citas/get_by_paciente.php    | Por paciente                   |
| GET    | /citas/get_by_terapeuta.php   | Por terapeuta                  |
| PUT    | /citas/put.php                | Actualizar cita                |
| DELETE | /citas/delete.php             | Cancelar cita                  |
| PUT    | /citas/activar.php            | Reactivar cita                 |

### 🧾 Recetas médicas
| Método | Endpoint                      | Acción                        |
|--------|-------------------------------|-------------------------------|
| POST   | /recetas/post.php             | Crear receta                  |
| GET    | /recetas/get.php              | Listar recetas activas (filtrado por sede) |
| GET    | /recetas/get_by_paciente.php  | Listar por paciente           |
| PUT    | /recetas/put.php              | Editar receta                 |
| DELETE | /recetas/delete.php           | Desactivar receta             |
| PUT    | /recetas/activar.php          | Activar receta                |
| GET    | /recetas/export_pdf.php?id=1  | Exportar receta como PDF con firma |

### 🖋️ Firmas de terapeutas
| Método | Endpoint                          | Acción                        |
|--------|-----------------------------------|-------------------------------|
| POST   | /firmas_terapeutas/upload.php     | Subir/actualizar firma       |
| GET    | /firmas_terapeutas/get.php        | Obtener firma                |
| DELETE | /firmas_terapeutas/delete.php     | Eliminar firma               |

### 🧾 Expediente clínico
| Método | Endpoint                              | Acción                        |
|--------|---------------------------------------|-------------------------------|
| GET    | /expediente/get_completo.php?id=1     | Ver resumen completo          |
| GET    | /expediente/export_pdf.php?id=1       | Exportar expediente como PDF  |

### 📓 Bitácora de cambios
| Método | Endpoint                          | Acción                        |
|--------|-----------------------------------|-------------------------------|
| POST   | /bitacora/post.php                | Registrar acción              |
| GET    | /bitacora/get.php                 | Ver todas                     |
| GET    | /bitacora/get_by_modulo.php       | Filtrar por módulo            |
| GET    | /bitacora/get_by_usuario.php      | Filtrar por usuario           |

---


