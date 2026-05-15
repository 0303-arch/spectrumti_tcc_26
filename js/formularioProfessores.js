const formulario = document.getElementById("main-form");
const formWrapper = document.querySelector(".form-wrapper");

// VERIFICA STATUS AO ABRIR PÁGINA
document.addEventListener("DOMContentLoaded", async () => {

    try {

        const resposta = await fetch("../backend/verificarStatusProfessor.php");

        const dados = await resposta.json();

        console.log(dados);

        if (dados.possuiFormulario) {

            renderizarTelaPorStatus(dados.status);

        }

    } catch (erro) {

        console.error(erro);

    }

});

// ENVIO FORMULÁRIO
if (formulario) {

    formulario.addEventListener("submit", async (event) => {

        event.preventDefault();

        const formData = new FormData(formulario);

        try {

            const resposta = await fetch("../backend/enviarFormulario.php", {
                method: "POST",
                body: formData
            });

            const resultado = await resposta.json();

            console.log(resultado);

            if (resultado.sucesso) {

                renderizarTelaPorStatus("aguardando");

            } else {

                alert(resultado.mensagem);

            }

        } catch (erro) {

            console.error(erro);

            alert("Erro ao enviar formulário.");

        }

    });

}

// CONTROLE VISUAL
function renderizarTelaPorStatus(status) {

    if (!formWrapper) return;

    formWrapper.innerHTML = "";

    const containerMsg = document.createElement("div");

    containerMsg.className = "status-container-feedback";

    switch(status) {

        case "aguardando":

            containerMsg.innerHTML = `
                <div class="status-box waiting">
                    <h2>Inscrição Recebida!</h2>

                    <p>
                        Muito obrigado pela disposição de ser um(a)
                        professor(a) na SpectrumTI.
                    </p>

                    <p>
                        Aguarde a avaliação do time administrativo.
                    </p>
                </div>
            `;

            break;

        case "recusado":

            containerMsg.innerHTML = `
                <div class="status-box rejected">

                    <h2>Inscrição Encerrada</h2>

                    <p>
                        Parece que você ainda não possui alguns
                        requisitos necessários para lecionar na SpectrumTI.
                    </p>

                    <button
                        onclick="window.location.reload();"
                        class="btn-submit"
                    >
                        Tentar novamente
                    </button>

                </div>
            `;

            break;

        case "aprovado":

            window.location.href = "perfil_professor.html";

            return;

        case "aprovado_com_limitacoes":

            containerMsg.innerHTML = `
                <div class="status-box partial">

                    <h2>Pré-aprovado</h2>

                    <p>
                        Você foi aprovado, porém ainda possui algumas
                        limitações identificadas pelo time avaliador.
                    </p>

                    <p>
                        Em breve entraremos em contato.
                    </p>

                </div>
            `;

            break;

        default:

            window.location.reload();

            return;

    }

    formWrapper.appendChild(containerMsg);

}