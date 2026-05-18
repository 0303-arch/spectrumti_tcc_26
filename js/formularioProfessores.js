const formulario = document.getElementById("main-form");
const formWrapper = document.querySelector(".form-wrapper");

/*
========================================
VERIFICA STATUS AO ABRIR
========================================
*/

document.addEventListener("DOMContentLoaded", async () => {

    await verificarStatusProfessor();

});

/*
========================================
VERIFICA STATUS
========================================
*/

async function verificarStatusProfessor() {

    try {

        const resposta = await fetch(
            "../backend/verificarStatusProfessor.php",
            {
                method: "GET",
                credentials: "same-origin"
            }
        );

        const texto = await resposta.text();

        console.log("RESPOSTA STATUS:");
        console.log(texto);

        let dados;

        try {

            dados = JSON.parse(texto);

        } catch (erroJson) {

            console.error("JSON inválido:");
            console.error(erroJson);

            mostrarErroBackend(texto);

            return;
        }

        console.log(dados);

        /*
        ================================
        NÃO LOGADO
        ================================
        */

        if (dados.sucesso === false) {

            alert(dados.mensagem || "Erro autenticação");

            return;
        }

        /*
        ================================
        POSSUI FORMULÁRIO
        ================================
        */

        if (dados.possuiFormulario) {

            renderizarTelaPorStatus(dados.status);

        }

    } catch (erro) {

        console.error("Erro fetch status:");
        console.error(erro);

        alert("Erro ao verificar status.");

    }

}

/*
========================================
ENVIO FORMULÁRIO
========================================
*/

if (formulario) {

    formulario.addEventListener("submit", async (event) => {

        event.preventDefault();

        const formData = new FormData(formulario);

        try {

            const resposta = await fetch(
                "../backend/enviarFormulario.php",
                {
                    method: "POST",
                    body: formData,
                    credentials: "same-origin"
                }
            );

            /*
            ================================
            TEXTO BRUTO
            ================================
            */

            const texto = await resposta.text();

            console.log("RESPOSTA FORM:");
            console.log(texto);

            /*
            ================================
            CONVERTE JSON
            ================================
            */

            let resultado;

            try {

                resultado = JSON.parse(texto);

            } catch (erroJson) {

                console.error("JSON inválido:");
                console.error(erroJson);

                mostrarErroBackend(texto);

                return;
            }

            console.log(resultado);

            /*
            ================================
            SUCESSO
            ================================
            */

            if (resultado.sucesso) {

                renderizarTelaPorStatus("aguardando");

            } else {

                alert(resultado.mensagem || "Erro no envio");

                console.error(resultado);

            }

        } catch (erro) {

            console.error("Erro fetch:");
            console.error(erro);

            alert("Erro ao enviar formulário.");

        }

    });

}

/*
========================================
TELAS POR STATUS
========================================
*/

function renderizarTelaPorStatus(status) {

    if (!formWrapper) return;

    formWrapper.innerHTML = "";

    const containerMsg = document.createElement("div");

    containerMsg.className = "status-container-feedback";

    switch (status) {

        case "aguardando":

            containerMsg.innerHTML = `
                <div class="status-box waiting">

                    <h2>Inscrição Recebida</h2>

                    <p>
                        Sua candidatura foi enviada.
                    </p>

                    <p>
                        Aguarde avaliação da administração.
                    </p>

                </div>
            `;

            break;

        case "recusado":

            containerMsg.innerHTML = `
                <div class="status-box rejected">

                    <h2>Inscrição Encerrada</h2>

                    <p>
                        Sua candidatura não foi aprovada.
                    </p>

                    <button
                        class="btn-submit"
                        onclick="window.location.reload();"
                    >
                        Tentar novamente
                    </button>

                </div>
            `;

            break;

        case "aprovado":

            window.location.href = "perfil_professor.php";

            return;

        case "aprovado_com_limitacoes":

            containerMsg.innerHTML = `
                <div class="status-box partial">

                    <h2>Pré-aprovado</h2>

                    <p>
                        Você foi aprovado com limitações.
                    </p>

                    <p>
                        Aguarde contato da administração.
                    </p>

                </div>
            `;

            break;

        default:

            containerMsg.innerHTML = `
                <div class="status-box rejected">

                    <h2>Status desconhecido</h2>

                    <p>
                        Recarregue a página.
                    </p>

                </div>
            `;

    }

    formWrapper.appendChild(containerMsg);

}

/*
========================================
MOSTRA ERRO PHP
========================================
*/

function mostrarErroBackend(texto) {

    console.error("BACKEND RETORNOU:");
    console.error(texto);

    alert(
        "O backend retornou erro PHP.\n\n" +
        "Abra F12 > Network > Response."
    );

}