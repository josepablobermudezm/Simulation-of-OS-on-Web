<?php

error_reporting();
ini_set("display_errors", 1);

require_once("conexion.php");

$servidor = "localhost";
$usuario = "root";
$pass = "";
$base_datos = "bd_funcionarios";
$conexion = new Conexion($servidor, $usuario, $pass, $base_datos);

if (isset($_GET['datobusqueda'])) {
    buscarProductos($conexion, $_GET['datobusqueda']);
}

if (isset($_POST['botonEliminar'])) {
    $ced = $_POST['cedula'];
    $conexion->consulta("SELECT cedula FROM tbl_funcionarios WHERE cedula = '$ced'");
    if ($conexion->extraer_registro()) {
        eliminarProductos($conexion, $_POST['cedula']);
        echo obtenerProductos($conexion);
    } else {
        echo "null";
    }
}

if (isset($_POST['btn_save'])) {
    $cedula = $_POST['txt_cedula'];
    $nombre = $_POST["txt_nombre"];
    $apellido1 = $_POST["txt_apellido1"];
    $apellido2 = $_POST["txt_apellido2"];
    $telefono = $_POST["txt_telefono"];
    $email = $_POST["txt_email"];
    $direccion = $_POST["txt_direccion"];
    $departamento = $_POST["txt_departamento"];
    $puesto = $_POST["txt_puesto"];
    $salario = $_POST["txt_salario"];
    $observaciones = $_POST["txt_observaciones"];
    $foto = $_POST["txt_foto"];
    $fecha = $_POST["txt_fecha"];

    metodo();

    insertarProductos($conexion, $cedula, $nombre, $apellido1, $apellido2, $telefono, $email, $direccion, $departamento, $puesto, $salario, $observaciones, $foto, $fecha);
    echo obtenerProductos($conexion);
}

function metodo()
{
    $postinfo = array();
    $postinfo[0] = str_replace("'", "\"", uploadFile());
}

function uploadFile()
{
    $img = "";

    if (isset($_FILES['txt_foto'])) {
        $target_dir = "img/";
        $target_file = $target_dir . basename($_FILES["txt_foto"]["name"]);
        move_uploaded_file($_FILES["txt_foto"]["tmp_name"], $target_file);

        $img = "<br> <img src='$target_file' alt='' title='' width='300'/>";
    }

    return $img;
}

function obtenerProductos($conexion)
{
    $conexion->consulta("SELECT * FROM tbl_funcionarios ORDER BY id DESC");
    $datos = "";
    //substr(5,undefined);
    while ($fila = $conexion->extraer_registro()) {
        //$imagenTemp = "img/" + $fila[12];
        //$imagen = str_replace("%C:fakepath%", "", $imagenTemp);

        $DireccionCompleta  = $fila[12];
        $Eliminar = array("C:fakepath");
        $CambiarPor   = array("");
        $DireccionSinError = str_replace($Eliminar, $CambiarPor, $DireccionCompleta);

        $imagen = "img/" . $DireccionSinError; 

        $datos .= "<tr><td onclick='getRow(this)'>$fila[1]</td><td onclick='getRow(this)'>$fila[2]</td><td onclick='getRow(this)'>$fila[3]</td><td onclick='getRow(this)'>$fila[4]</td><td onclick='getRow(this)'>$fila[5]</td><td onclick='getRow(this)'>$fila[6]</td><td onclick='getRow(this)'>$fila[7]</td><td onclick='getRow(this)'>$fila[8]</td><td onclick='getRow(this)'>$fila[9]</td><td onclick='getRow(this)'>$fila[10]</td><td onclick='getRow(this)'>$fila[11]</td><td onclick='getRow(this)'><img src='$imagen' border=3 height=25 width=25></img></td><td onclick='getRow(this)'>$fila[13]</td><td onclick='getRow(this)'><tr>";
    }

    return $datos;
}

function buscarProductos($conexion, $dato)
{
    $conexion->consulta("SELECT * FROM tbl_funcionarios WHERE cedula LIKE '%$dato%' OR nombre LIKE '%$dato%' OR apellido1 LIKE '%$dato%' OR apellido2 LIKE '%$dato%' ");
    $resultado = "";
    while ($fila = $conexion->extraer_registro()) {
        $resultado .= "<tr><td onclick='getRow(this)'>$fila[1]</td><td onclick='getRow(this)'>$fila[2]</td><td onclick='getRow(this)'>$fila[3]</td><td onclick='getRow(this)'>$fila[4]</td><td onclick='getRow(this)'>$fila[5]</td><td onclick='getRow(this)'>$fila[6]</td><td onclick='getRow(this)'>$fila[7]</td><td onclick='getRow(this)'>$fila[8]</td><td onclick='getRow(this)'>$fila[9]</td><td onclick='getRow(this)'>$fila[10]</td><td onclick='getRow(this)'>$fila[11]</td><td onclick='getRow(this)'>$fila[12]</td><td onclick='getRow(this)'>$fila[13]</td><td onclick='getRow(this)'><tr>";
    }
    echo $resultado; //imprimimos los datos
}

function insertarProductos($conexion, $cedula, $nombre, $apellido1, $apellido2, $telefono, $email, $direccion, $departamento, $puesto, $salario, $observaciones, $foto, $fecha)
{
    //INSERTAR - ACTUALIZAR - Comprobamos que la cedula existe buscándolo primero
    if (
        $cedula != "" && $nombre != "" && $apellido1 != "" && $apellido2 != "" && $telefono != "" && $email != "" &&
        $direccion != "" && $departamento != "" && $puesto != "" && $salario != "" && $fecha != ""
    ) {
        $conexion->consulta("SELECT cedula FROM tbl_funcionarios WHERE cedula = '$cedula' ");

        if ($conexion->extraer_registro()) { //SI EXISTE EL CODIGO LO ACTUALIZA
            $actualizar = "UPDATE tbl_funcionarios SET cedula='$cedula', nombre='$nombre', apellido1='$apellido1', apellido2='$apellido2', telefono='$telefono'
                , email='$email', direccion='$direccion', departamento='$departamento', puesto='$puesto', salario = $salario, observaciones='$observaciones', foto='$foto', fecha='$fecha' WHERE cedula = '$cedula' ";
            $conexion->consulta($actualizar);
            return "Registro actualizado exitosamente.";
        } else { //SI NO EXISTE EL CODIGO LO INSERTA
            $insertar = "INSERT INTO tbl_funcionarios (cedula,nombre,apellido1,apellido2,telefono,email,direccion,departamento,puesto,salario,observaciones,
                foto,fecha) VALUES('$cedula','$nombre','$apellido1','$apellido2','$telefono','$email','$direccion','$departamento','$puesto',$salario,'$observaciones',
                '$foto','$fecha')";
            $conexion->consulta($insertar);
            return "Registro insertado exitosamente.";
        }
    } else {
        return "Debe de insertar todos los datos";
    }
}

function eliminarProductos($conexion, $cedula)
{
    $conexion->consulta("SELECT cedula FROM tbl_funcionarios WHERE cedula = '$cedula' ");
    if ($conexion->extraer_registro()) {
        $actualizar = "DELETE FROM tbl_funcionarios WHERE cedula = '$cedula' ";
        $conexion->consulta($actualizar);
        return "Registro actualizado exitosamente.";
    }
}
