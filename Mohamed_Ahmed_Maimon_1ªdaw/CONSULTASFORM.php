<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario para gestionar artículos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Formulario para artículos</h1>
    <form method="post">
        <label>Insertar artículo</label><br>
        <input type="hidden" name="tipo_consulta" value="insert">
        Descripción: <input type="text" name="descripcion"><br>
        Stock: <input type="number" name="stock"><br>
        PVP: <input type="number" step="0.01" name="pvp"><br>
        Precio de coste: <input type="number" step="0.01" name="precioCoste"><br>
        Stock mínimo: <input type="number" name="stockMinimo"><br>
        Proveedor: <input type="text" name="proveedor"><br>
        <input type="submit" value="Insertar artículo">
    </form>

    <form method="post">
        <label>Consultar artículos</label><br>
        <input id="Consultar artículos" type="hidden" name="tipo_consulta" value="select">
        <textarea name="consulta_select" rows="2" cols="50" placeholder="Escribe tu consulta SELECT aquí."></textarea><br>
        <input type="submit" value="Realizar consulta SELECT">
    </form>

    <form method="post">
        <label>Actualizar artículo</label><br>
        <input  type="hidden" name="tipo_consulta" value="update">
        <textarea name="consulta_update" rows="2" cols="50" placeholder="Escribe tu consulta UPDATE aquí."></textarea><br>
        <input type="submit" value="Realizar consulta UPDATE">
    </form>

    <form method="post">
        <label>Eliminar artículo</label><br>
        <input type="hidden" name="tipo_consulta" value="delete">
        <textarea name="consulta_delete" rows="2" cols="50" placeholder="Escribe tu consulta DELETE aquí."></textarea><br>
        <input type="submit" value="Realizar consulta DELETE">
    </form>
    <?php
function conectar($host, $usuario, $contraseña) {
    $conexion = mysqli_connect($host, $usuario, $contraseña);
    if ($conexion) {
        echo "Conexión con éxito <br>"; 
        echo 'Información sobre el servidor: ', mysqli_get_host_info($conexion), '<br>'; 
        echo 'Versión del servidor: ', mysqli_get_server_info($conexion), '<br>';
    } else {
        echo "Conexión realizada incorrectamente <br>";
        echo mysqli_connect_errno(), ":", mysqli_connect_error();
    }
    return $conexion;
}

function conectar_BD($conexion, $nombre_BD) {
    $ok = mysqli_select_db($conexion, $nombre_BD);
    if ($ok) {
        echo "Conexión realizada con éxito a la base de datos <br>";
    } else {
        exit('No se pudo conectar a la base de datos<br>');
    }
    return $ok;
}

function consultas($conexion, $consulta) {
    $resultado = mysqli_query($conexion, $consulta);
    return $resultado;
}

function desconectar($conexion) {
    if ($conexion) {
        $ok = mysqli_close($conexion);
        if ($ok) {
            echo "Desconexión con éxito <br />";
        } else {
            echo "Error al desconectar <br />";
        }
    } else {
        echo "Conexión no abierta <br />";
    }
}

function mostrar($conexion, $resultado) {
    // Primero, verificar si el resultado es FALSE, lo que indica un error en la consulta
    if ($resultado === false) {
        echo "Error en la consulta SQL: " . mysqli_error($conexion) . "<br>";
    } else {
        // Verificar si el resultado es de tipo mysqli_result
        if ($resultado instanceof mysqli_result) {
            if (mysqli_num_rows($resultado) > 0) {
                while ($fila = mysqli_fetch_assoc($resultado)) {
                    foreach ($fila as $clave => $valor) {
                        echo $clave . ": " . $valor . " | ";
                    }
                    echo "<br>";
                }
            } else {
                echo "No se encontraron resultados.<br>";
            }
        } else {
            // Si el resultado es TRUE y no un mysqli_result, significa que la operación fue exitosa pero no es de tipo SELECT
            echo "Operación realizada correctamente, pero no hay datos para mostrar.<br>";
        }
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conexion = conectar('localhost', 'root', '');
    if ($conexion) {
        conectar_BD($conexion, 'almacen');

        $tipo_consulta = $_POST['tipo_consulta'];
        switch ($tipo_consulta) {
            case 'insert':
                $descripcion = $_POST['descripcion'];
                $stock = $_POST['stock'];
                $pvp = $_POST['pvp'];
                $precioCoste = $_POST['precioCoste'];
                $stockMinimo = $_POST['stockMinimo'];
                $proveedor = $_POST['proveedor'];
                $consulta = "INSERT INTO articulo (descripcion, stock, pvp, precioCoste, stockMinimo, proveedor) VALUES ('$descripcion', $stock, $pvp, $precioCoste, $stockMinimo, '$proveedor')";
                $resultado = consultas($conexion, $consulta);
                echo "Insertado con exito un nuevo artículo: <br>";
                mostrar($conexion,consultas($conexion, "SELECT * FROM articulo WHERE descripcion = '$descripcion'"));
                break;
            case 'select':
                $consulta = $_POST['consulta_select'];
                $resultado = consultas($conexion, $consulta);
                echo "Resultado de la consulta SELECT: <br>";
                mostrar($conexion,$resultado);
                break;
            case 'update':
                $consulta = $_POST['consulta_update'];
                $resultado = consultas($conexion, $consulta);
                echo "Resultado de la consulta UPDATE: <br>";
                mostrar($conexion,$resultado);
                break;
            case 'delete':
                $consulta = $_POST['consulta_delete'];
                $resultado = consultas($conexion, $consulta);
                echo "Resultado de la consulta DELETE: <br>";
                mostrar($conexion, $resultado);
                break;
        }
        desconectar($conexion);
    }
}
?>
</body>
</html>