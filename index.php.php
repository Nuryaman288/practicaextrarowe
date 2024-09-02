<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Biblioteca</title>
    <style>
        .formulario {
            margin-bottom: 20px;
        }
        .formulario label {
            display: block;
            margin: 5px 0;
        }
        .formulario input[type="text"],
        .formulario input[type="number"] {
            width: 200px;
            padding: 5px;
            margin-bottom: 10px;
        }
        .formulario input[type="submit"] {
            padding: 8px 12px;
            background-color: #ff69b4;
            border: none;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }
        .formulario input[type="submit"]:hover {
            background-color: #ff1493;
        }
        table {
            width: 80%;
            max-width: 600px;
            border-collapse: collapse;
            border: 1px solid #ff69b4;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #ff69b4;
            color: white;
        }
        td {
            background-color: #ffe4e1;
        }
    </style>
</head>
<body>

<h2>Agregar Libro</h2>
<form action="nurita.php" method="post" class="formulario">
    <label for="titulo">Título:</label>
    <input type="text" id="titulo" name="titulo" required><br>
    <label for="autor">Autor:</label>
    <input type="text" id="autor" name="autor" required><br>
    <label for="año_publicacion">Año de Publicación:</label>
    <input type="number" id="año_publicacion" name="año_publicacion" required><br>
    <label for="genero">Género:</label>
    <input type="text" id="genero" name="genero" required><br>
    <input type="submit" name="agregar" value="Agregar Libro">
</form>

<h2>Eliminar Libro</h2>
<form action="nurita.php" method="post" class="formulario">
    <label for="id">ID del Libro a Eliminar:</label>
    <input type="number" id="id" name="id" required><br>
    <input type="submit" name="eliminar" value="Eliminar Libro">
</form>

<h2>Editar Libro</h2>
<form action="nurita.php" method="post" class="formulario">
    <label for="id">ID del Libro a Editar:</label>
    <input type="number" id="id" name="id" required><br>
    <label for="titulo">Nuevo Título:</label>
    <input type="text" id="titulo" name="titulo"><br>
    <label for="autor">Nuevo Autor:</label>
    <input type="text" id="autor" name="autor"><br>
    <label for="año_publicacion">Nuevo Año de Publicación:</label>
    <input type="number" id="año_publicacion" name="año_publicacion"><br>
    <label for="genero">Nuevo Género:</label>
    <input type="text" id="genero" name="genero"><br>
    <input type="submit" name="editar" value="Editar Libro">
</form>

<h2>Buscar Libro por ID</h2>
<form action="nurita.php" method="post" class="formulario">
    <label for="id_buscar">ID del Libro a Buscar:</label>
    <input type="number" id="id_buscar" name="id_buscar" required><br>
    <input type="submit" name="buscar" value="Buscar Libro">
</form>

<?php
$server = "localhost";
$user = "root";
$pass = "";
$db = "biblioteca";

// Crear la conexión
$conexion = new mysqli($server, $user, $pass, $db);

// Verificar la conexión
if ($conexion->connect_errno) {
    die("Conexión fallida: " . $conexion->connect_error);
} else {
    echo "Conectado<br>";
}

// Agregar un libro
if (isset($_POST['agregar'])) {
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $año_publicacion = $_POST['año_publicacion'];
    $genero = $_POST['genero'];

    $sql = "INSERT INTO libros (titulo, autor, año_publicacion, genero) VALUES ('$titulo', '$autor', '$año_publicacion', '$genero')";

    if ($conexion->query($sql) === TRUE) {
        echo "Nuevo libro agregado con éxito<br>";
    } else {
        echo "Error: " . $sql . "<br>" . $conexion->error;
    }
}

// Eliminar un libro y ajustar AUTO_INCREMENT
if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];

    $sql = "DELETE FROM libros WHERE id='$id'";

    if ($conexion->query($sql) === TRUE) {
        echo "Libro eliminado con éxito<br>";

        $resultado = $conexion->query("SELECT MAX(id) as max_id FROM libros");
        $fila = $resultado->fetch_assoc();
        $nuevo_auto_increment = $fila['max_id'] + 1;

        $conexion->query("ALTER TABLE libros AUTO_INCREMENT = $nuevo_auto_increment");
    } else {
        echo "Error: " . $sql . "<br>" . $conexion->error;
    }
}

// Editar un libro
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $año_publicacion = $_POST['año_publicacion'];
    $genero = $_POST['genero'];

    $updates = [];

    if (!empty($titulo)) {
        $updates[] = "titulo='$titulo'";
    }
    if (!empty($autor)) {
        $updates[] = "autor='$autor'";
    }
    if (!empty($año_publicacion)) {
        $updates[] = "año_publicacion='$año_publicacion'";
    }
    if (!empty($genero)) {
        $updates[] = "genero='$genero'";
    }

    if (!empty($updates)) {
        $sql = "UPDATE libros SET " . implode(", ", $updates) . " WHERE id='$id'";

        if ($conexion->query($sql) === TRUE) {
            echo "Libro actualizado con éxito<br>";
        } else {
            echo "Error: " . $sql . "<br>" . $conexion->error;
        }
    } else {
        echo "No se realizaron cambios.<br>";
    }
}

// Buscar un libro por ID
if (isset($_POST['buscar'])) {
    $id_buscar = $_POST['id_buscar'];

    $sql = "SELECT * FROM libros WHERE id='$id_buscar'";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0) {
        echo "<h2>Resultado de la Búsqueda</h2>";
        echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Año de Publicación</th>
                    <th>Género</th>
                </tr>";
        while($fila = $resultado->fetch_assoc()) {
            echo "<tr>
                    <td>" . $fila["id"] . "</td>
                    <td>" . $fila["titulo"] . "</td>
                    <td>" . $fila["autor"] . "</td>
                    <td>" . (isset($fila["año_publicacion"]) ? $fila["año_publicacion"] : "No disponible") . "</td>
                    <td>" . $fila["genero"] . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "No se encontraron resultados para el ID: $id_buscar.";
    }
}

// Mostrar todos los libros
$sql = "SELECT * FROM libros";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    echo "<h2>Lista de Libros</h2>";
    echo "<table>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Año de Publicación</th>
                <th>Género</th>
            </tr>";
    while($fila = $resultado->fetch_assoc()) {
        echo "<tr>
                <td>" . $fila["id"] . "</td>
                <td>" . $fila["titulo"] . "</td>
                <td>" . $fila["autor"] . "</td>
                <td>" . (isset($fila["año_publicacion"]) ? $fila["año_publicacion"] : "No disponible") . "</td>
                <td>" . $fila["genero"] . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No hay libros en la biblioteca.";
}

$conexion->close();
?>

</body>
</html>

