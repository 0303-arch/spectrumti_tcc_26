<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

/*
SIMULAÇÃO TEMPORÁRIA
Depois virá do login
*/
$usuario_id = 1;

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "spectrum"
);

if ($conn->connect_error) {

    echo json_encode([
        "erro" => "Erro conexão"
    ]);

    exit;
}

$conn->set_charset("utf8mb4");

$sql = "
SELECT status_adm
FROM voluntarios
WHERE usuario_id = '$usuario_id'
ORDER BY id DESC
LIMIT 1
";

$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {

    $dados = $resultado->fetch_assoc();

    echo json_encode([
        "possuiFormulario" => true,
        "status" => $dados["status_adm"]
    ]);

} else {

    echo json_encode([
        "possuiFormulario" => false
    ]);
}

$conn->close();

?>