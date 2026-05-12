# Users CRUD - PHP Application

Aplicación web de gestión de usuarios (Create, Read, Update, Delete) desarrollada en PHP puro con MySQL.

---

## Base de Datos

### Nombre de la Base de Datos
```
users_crud_php
```

### Estructura de la Tabla `users`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | INT (11) | Clave primaria, auto-incrementable |
| `name` | VARCHAR (100) | Nombre del usuario |
| `lastname` | VARCHAR (100) | Apellidos del usuario |
| `username` | VARCHAR (50) | Nombre de usuario (único) |
| `password` | VARCHAR (255) | Contraseña hasheada (bcrypt) |
| `email` | VARCHAR (100) | Correo electrónico |
| `created_at` | TIMESTAMP | Fecha de creación/actualización |

---

## Flujo de la Aplicación

### 1. Conexión (`conection.php`)
Establece la conexión con MySQL:
- **Host:** localhost
- **Usuario:** root
- **Contraseña:** (vacía)
- **Base de datos:** users_crud_php
- **Charset:** utf8mb4

```php
function connection() {
    // Retorna objeto de conexión mysqli o null si falla
}
```

---

### 2. Página Principal (`index.php`)

**Funcionalidad:**
- Muestra formulario para crear nuevos usuarios
- Lista todos los usuarios registrados en una tabla
- Muestra mensajes de error o éxito mediante sesiones

**Flujo:**
1. Inicia sesión (`session_start()`)
2. Recupera mensajes de sesión (error/success)
3. Se conecta a la base de datos
4. Ejecuta: `SELECT id, name, lastname, username, email FROM users ORDER BY id DESC`
5. Renderiza el formulario y la tabla con los datos

**Elementos visuales:**
- Formulario de creación (izquierda)
- Tabla de usuarios (derecha)
- Botones para editar y eliminar cada fila

---

### 3. Crear Usuario (`insert_user.php`)

**Funcionalidad:**
- Procesa el formulario de creación de usuarios
- Valida datos, verificausername único, hashea contraseña

**Flujo:**
1. Verifica que el método sea POST
2. Obtiene y valida los campos:
   - Nombre, apellido, username, password, email
3. Valida formato de email con `filter_var()`
4. Verifica que el username no exista ya (consulta preparada)
5. Hashea la contraseña con `password_hash()` (bcrypt)
6. Inserta en la base de datos usando prepared statements
7. Guarda mensaje en sesión y redirecciona a `index.php`

**Seguridad:**
- Prepared statements contra SQL injection
- Validación de email
- Contraseña hasheada (nunca se guarda en texto plano)

---

### 4. Editar Usuario (`edit_user.php`)

**Funcionalidad:**
- Procesa la actualización de datos de un usuario

**Flujo:**
1. Verifica método POST
2. Obtiene ID del usuario a editar
3. Valida que el ID sea numérico
4. Verifica que el nuevo username no esté en uso por otro usuario
5. Si se proporciona nueva contraseña, la hashea; si no, mantiene la actual
6. Actualiza el registro en la base de datos
7. Guarda mensaje en sesión y redirecciona a `index.php`

**⚠️ Nota:** El enlace en `index.php` apunta a `update_user.php` pero este archivo no existe. Debería apuntar a `edit_user.php`.

---

### 5. Eliminar Usuario (`delete_user.php`)

**Funcionalidad:**
- Elimina un usuario de la base de datos por su ID

**Flujo:**
1. Verifica método GET (recibe ID por URL)
2. Valida que el ID sea numérico
3. Ejecuta: `DELETE FROM users WHERE id = ?`
4. Verifica que se eliminó al menos una fila
5. Guarda mensaje en sesión y redirecciona a `index.php`

**Seguridad:**
- Prepared statement contra SQL injection
- Validación del ID

---

## Diagrama del Flujo

```
┌─────────────────────────────────────────────────────────────┐
│                         index.php                            │
│                    (Página Principal)                       │
└─────────────────────────┬───────────────────────────────────┘
                          │
          ┌───────────────┼───────────────┐
          │               │               │
          ▼               ▼               ▼
   ┌──────────────┐ ┌──────────────┐ ┌──────────────┐
   │  Formulario │ │   Lista de   │ │   Botones     │
   │   Crear     │ │   Usuarios   │ │   Acciones    │
   └──────┬──────┘ └──────────────┘ └──────┬───────┘
          │                                │
          ▼                                ▼
┌─────────────────┐              ┌─────────────────┐
│ insert_user.php │              │ edit/delete    │
│   (CREATE)      │              │  (UPDATE/      │
└─────────────────┘              │   DELETE)       │
                                └─────────────────┘
```

---

## Requisitos para Ejecutar

1. **XAMPP** instalado con:
   - Apache
   - MySQL

2. **Base de datos creada:**
   ```sql
   CREATE DATABASE users_crud_php;

   CREATE TABLE users (
       id INT(11) PRIMARY KEY AUTO_INCREMENT,
       name VARCHAR(100) NOT NULL,
       lastname VARCHAR(100) NOT NULL,
       username VARCHAR(50) NOT NULL UNIQUE,
       password VARCHAR(255) NOT NULL,
       email VARCHAR(100) NOT NULL,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   ```

3. **Archivos** en `htdocs/pratica_db1/`

4. **Acceso:** `http://localhost/pratica_db1/`

---

## Tecnologías Utilizadas

- **Backend:** PHP 7+
- **Base de datos:** MySQL (mysqli)
- **Frontend:** HTML5, CSS3
- **Seguridad:** Prepared statements, password hashing (bcrypt), HTML escaping

---

## Autor

Leoshi123