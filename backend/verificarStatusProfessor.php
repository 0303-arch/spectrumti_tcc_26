<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

/*
SIMULAÇÃO TEMPORÁRIA
Depois trocar pelo login real
*/
$usuario_id = 1;

// CONEXÃO
$conn = new mysqli(
    "localhost",
    "root",
    "",
    "spectrum"
);

// ERRO
if ($conn->connect_error) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro conexão banco"
    ]);

    exit;
}

$conn->set_charset("utf8mb4");

// BUSCA STATUS REAL
$sql = "
SELECT
    status_adm
FROM voluntarios
WHERE usuario_id = '$usuario_id'
LIMIT 1
";

$resultado = $conn->query($sql);

// NÃO POSSUI FORMULÁRIO
if ($resultado->num_rows === 0) {

    echo json_encode([
        "possuiFormulario" => false
    ]);

    exit;
}

// PEGA DADOS
$dados = $resultado->fetch_assoc();

// DEBUG
echo json_encode([

    "possuiFormulario" => true,

    "status" => $dados["status_adm"]

]);

$conn->close();

?>