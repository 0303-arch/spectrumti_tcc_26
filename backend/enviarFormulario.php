<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

/*
SIMULAÇÃO TEMPORÁRIA
Depois você pegará do login real
*/
$usuario_id = 1;

// CONEXÃO
$conn = new mysqli(
    "localhost",
    "root",
    "",
    "spectrum"
);

// ERRO CONEXÃO
if ($conn->connect_error) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro conexão banco",
        "erro" => $conn->connect_error
    ]);

    exit;
}

$conn->set_charset("utf8mb4");

// DADOS
$nome = $conn->real_escape_string($_POST["nome_completo"] ?? "");
$email = $conn->real_escape_string($_POST["email"] ?? "");
$telefone = $conn->real_escape_string($_POST["telefone"] ?? "");
$idade = intval($_POST["idade"] ?? 0);

$situacao_ti = $conn->real_escape_string($_POST["situacao_ti"] ?? "");
$curso_detalhes = $conn->real_escape_string($_POST["curso_detalhes"] ?? "");

$conteudos_ensino = $conn->real_escape_string($_POST["conteudos_ensino"] ?? "");

$experiencia_previa = $conn->real_escape_string($_POST["experiencia_previa"] ?? "");

$disponibilidade_horario = $conn->real_escape_string($_POST["disponibilidade_horario"] ?? "");

$acesso_tecnologia = $conn->real_escape_string($_POST["acesso_tecnologia"] ?? "");

$motivacao_voluntariado = $conn->real_escape_string($_POST["motivacao_voluntariado"] ?? "");

$observacoes_adicionais = $conn->real_escape_string($_POST["observacoes_adicionais"] ?? "");

$status_adm = "aguardando";

// VERIFICA SE JÁ EXISTE INSCRIÇÃO
$sqlVerifica = "
SELECT id
FROM voluntarios
WHERE usuario_id = '$usuario_id'
LIMIT 1
";

$resultadoVerifica = $conn->query($sqlVerifica);

if ($resultadoVerifica->num_rows > 0) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Você já possui uma candidatura em avaliação."
    ]);

    exit;
}

// INSERT
$sql = "
INSERT INTO voluntarios (

    usuario_id,
    nome_completo,
    email,
    telefone,
    idade,
    situacao_ti,
    curso_detalhes,
    conteudos_ensino,
    experiencia_previa,
    disponibilidade_horario,
    acesso_tecnologia,
    motivacao_voluntariado,
    observacoes_adicionais,
    status_adm

)
VALUES (

    '$usuario_id',
    '$nome',
    '$email',
    '$telefone',
    '$idade',
    '$situacao_ti',
    '$curso_detalhes',
    '$conteudos_ensino',
    '$experiencia_previa',
    '$disponibilidade_horario',
    '$acesso_tecnologia',
    '$motivacao_voluntariado',
    '$observacoes_adicionais',
    '$status_adm'

)
";

// EXECUTA
if ($conn->query($sql) === TRUE) {

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Formulário enviado com sucesso"
    ]);

} else {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro SQL",
        "erro" => $conn->error
    ]);
}

$conn->close();

?>