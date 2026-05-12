const formulario = document.getElementById("main-form");

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

            alert("Formulário enviado com sucesso!");
            formulario.reset();

        } else {

            alert(resultado.mensagem);
            console.error(resultado);
        }

    } catch (erro) {

        console.error(erro);

        alert("Erro ao enviar formulário.");
    }

}); 