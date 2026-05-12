<?php

header("Content-Type: application/json; charset=UTF-8");

// conexão
$host = "localhost";
$usuario = "gabrielkafferDS";
$senha = "gabrielkafferDS123@";
$banco = "spectrum";

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro conexão banco"
    ]);

    exit;
}

$conn->set_charset("utf8mb4");

// dados
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

// SQL
$sql = "
INSERT INTO voluntarios (
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
    observacoes_adicionais
)
VALUES (
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
    '$observacoes_adicionais'
) 
";

if ($conn->query($sql) === TRUE) {

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Dados salvos"
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