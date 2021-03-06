<?php

$conn = new mysqli( 'localhost', 'root', '', 'vue_students' );

if ( $conn->connect_error ) {
  die( 'Error al conectarse a la base de datos' );
}

$res = array( 'error' => false );

$action = 'read';

if ( isset( $_GET['action'] ) ) {
  $action = $_GET['action'];
}

if ( $action == 'read' ) {
  $result = $conn->query( "SELECT * FROM students" );
  $students = array();

  while ( $row = $result->fetch_assoc() ) {
    array_push( $students, $row );
  }

  $res['students'] = $students;  
}

if ( $action == 'create' ) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $web = $_POST['web'];

  $result = $conn->query( "INSERT INTO students (name, email, web) VALUES ('$name', '$email', '$web')" );
  
  if ( $result ) {
    $res['message'] = 'Estudiante agregado con éxito.';
  } else {
    $res['error'] = true;
    $res['message'] = "Error al tratar de agregar estudiante.";
  }
}

if ( $action == 'update' ) {
  $id = $_POST['id'];
  $name = $_POST['name'];
  $email = $_POST['email'];
  $web = $_POST['web'];

  $result = $conn->query( "UPDATE students SET name = '$name', email = '$email', web = '$web' WHERE id = $id" );
  
  if ( $result ) {
    $res['message'] = 'Estudiante actualizado con éxito.';
  } else {
    $res['error'] = true;
    $res['message'] = "Error al tratar de actualizar estudiante.";
  }
}

if ( $action == 'delete' ) {
  $id = $_POST['id'];

  $result = $conn->query( "DELETE FROM students WHERE id = $id" );
  
  if ( $result ) {
    $res['message'] = 'Estudiante eliminado con éxito.';
  } else{
    $res['error'] = true;
    $res['message'] = "Error al tratar de eliminar estudiante.";
  }
}

$conn->close();

header( 'Content-type: application/json' );
echo json_encode($res);