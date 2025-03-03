<?php

include('../protect.php'); // Inclui a função de proteção ao acesso da página

require_once('../conexao.php');

$conexao = novaConexao();





$query = "SELECT * FROM funcionarios";

$resultado = $conexao->query($query);

$funcionarios = [];

while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {

    // Adiciona cada (registro) ao array $funcionarios como um array associativo

    $funcionarios[] = $row;

}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {  // Verifica se o formulário foi enviado

    try {

        // Verificar se todos os campos obrigatórios estão preenchidos

        if (

            isset(

                $_POST['resp'],

                $_POST['titulo'],

                $_POST['dataRegistro'],

                $_POST['dataPrazo'],

                $_POST['informacao']

            )

        ) {



            // Converter datas para o formato YYYY-MM-DD

            $dataRegistro = date('Y-m-d', strtotime($_POST['dataRegistro']));

            $dataPrazo = date('Y-m-d', strtotime($_POST['dataPrazo']));



            $codFunc = $_POST['resp'];

            $sql_resp = "SELECT nome FROM funcionarios WHERE cod_func = :codFunc";

            $stmt = $conexao->prepare($sql_resp);

            $stmt->bindValue(':codFunc', $codFunc);

            $stmt->execute();

            $resp = $stmt->fetch(PDO::FETCH_ASSOC)['nome'];



            // Preparar a SQL

            $sql = "INSERT INTO agenda (cod_func, responsavel, titulo, dataRegistro, dataPrazo, informacao) VALUES (:a_cf, :a_rs, :a_t, :a_dR, :a_dP, :a_I)";

            $stmt = $conexao->prepare($sql);



            // Associar os valores aos placeholders

            $stmt->bindValue(':a_cf', $_POST['resp']);

            $stmt->bindValue(':a_rs', $resp);

            $stmt->bindValue(':a_t', $_POST['titulo']);

            $stmt->bindValue(':a_dR', $dataRegistro);

            $stmt->bindValue(':a_dP', $dataPrazo);

            $stmt->bindValue(':a_I', $_POST['informacao']);



            // Executar a SQL

            $stmt->execute();



            header("Location: ./consAge.php");

        }

    } catch (PDOException $e) {

        echo "Erro: " . $e->getMessage();

    }

}



?>



<!DOCTYPE html>

<html lang="pt-BR">



<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inserir na Agenda</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../style.css">

</head>



<body>



    <div class="container-fluid cabecalho"> <!-- CABECALHO -->

        <nav class="navbar navbar-light navbar-expand-md" style="background-color: #FFFF;">

            <a class="nav justify-content-start m-2" href="../admInicial.php">

                <img src="../img/back.png">

            </a>



            <button class="navbar-toggler hamburguer" data-bs-toggle="collapse" data-bs-target="#navegacao">

                <span class="navbar-toggler-icon"></span>

            </button>



            <div class="collapse navbar-collapse justify-content-center" id="navegacao">



                <ul class="nav nav-pills justify-content-center listas"> <!-- LISTAS DO MENU CABECALHO-->





                    <li class="nav-item dropdown"> <!-- LINK BOOTSTRAP DORPDOWN MENU-->

                        <a class="nav-link dropdown-toggle cor_fonte" href="#" id="navbarDropdownMenuLink"

                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                            Pedidos

                        </a>

                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">

                            <a class="dropdown-item" href="../pedidos/cadPed.php">Cadastro</a>

                            <a class="dropdown-item" href="../pedidos/consPed.php">Consulta</a>

                        </div>

                    </li> <!-- FECHA O DROPDOWN MENU-->



                    <li class="nav-item dropdown"> <!-- LINK BOOTSTRAP DORPDOWN MENU-->

                        <a class="nav-link dropdown-toggle cor_fonte" href="#" id="navbarDropdownMenuLink"

                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                            Agenda

                        </a>

                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">

                            <a class="dropdown-item" href="#">Inserir</a>

                            <a class="dropdown-item" href="consAge.php">Consultar</a>

                        </div>

                    </li> <!-- FECHA O DROPDOWN MENU-->



                    <li class="nav-item dropdown"> <!-- LINK BOOTSTRAP DORPDOWN MENU-->

                        <a class="nav-link dropdown-toggle cor_fonte" href="#" id="navbarDropdownMenuLink"

                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                            Produtos

                        </a>

                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">

                            <a class="dropdown-item" href="../produto/cadProd.php">Cadastro</a>

                            <a class="dropdown-item" href="../produto/editProd.php">Edição</a>

                            <a class="dropdown-item" href="../produto/categoria.php">Categoria</a>

                        </div>

                    </li> <!-- FECHA O DROPDOWN MENU-->



                    <li class="nav-item dropdown"> <!-- LINK BOOTSTRAP DORPDOWN MENU-->

                        <a class="nav-link dropdown-toggle cor_fonte" href="#" id="navbarDropdownMenuLink"

                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                            Funcionários

                        </a>

                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">

                            <a class="dropdown-item" href="../funcionarios/cadFunc.php">Cadastro</a>

                            <a class="dropdown-item" href="../funcionarios/listaFunc.php">Listar</a>

                        </div>

                    </li> <!-- FECHA O DROPDOWN MENU-->



                </ul> <!-- FECHA LISTAS MENU CABECALHO -->

            </div>

            <a href="../logout.php" class="nav-link justify-content-end" style="color: red;">

                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor"

                    class="bi bi-box-arrow-right" viewBox="0 0 16 16">

                    <path fill-rule="evenodd"

                        d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z" />

                    <path fill-rule="evenodd"

                        d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z" />

                </svg>

            </a>

        </nav> <!-- FECHA CABECALHO -->

    </div> <!-- FECHA CONTAINER DO CABECALHO -->





    <div class="container container-custom">

        <h3 class="text-center mb-4">Inserir na Agenda</h3>

        <form method="POST">

            <div class="row row-custom">

                <div class="form-group mb-3">

                    <label class="form-label">Responsável:</label>

                    <select class="form-select" name="resp">

                        <option selected disabled>Selecione um funcionário</option>

                        <?php foreach ($funcionarios as $funcionario): ?>

                            <option value="<?php echo htmlspecialchars($funcionario['cod_func']); ?>">

                                <?php echo htmlspecialchars($funcionario['nome']); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="form-group mb-3">

                    <label class="form-label">Título da Agenda:</label>

                    <input type="text" class="form-control" name="titulo" placeholder="Título da Agenda">

                </div>



                <div class="row">

                    <div class="col-6">

                        <div class="form-group mb-3">

                            <label class="form-label">Data de registro:</label>

                            <input type="date" class="form-control data" name="dataRegistro" readonly>

                        </div>

                    </div>



                    <div class="col-6">

                        <div class="form-group mb-3">

                            <label class="form-label">Data de Prazo:</label>

                            <input type="date" class="form-control data" name="dataPrazo">

                        </div>

                    </div>

                </div>



                <div class="form-group mb-3">

                    <label class="form-label">Informações:</label>

                    <textarea class="form-control" name="informacao"

                        placeholder="Digite as informações sobre o registro..." rows="5"></textarea>

                </div>



            </div>



            <!-- Botões centralizados abaixo das colunas -->

            <div class="row mt-4 btn-group-custom">

                <button type="button" class="btn btn-outline-danger btn-personalizado"
                    onclick="window.location.href='./consAge.php';">Cancelar</button>

                <button type="submit" class="btn btn-success btn-personalizado">Confirmar</button>

            </div>

    </div>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <script>

        var hoje = new Date();



        var dia = String(hoje.getDate()).padStart(2, '0');

        var mes = String(hoje.getMonth() + 1).padStart(2, '0'); // Janeiro é 0!

        var ano = hoje.getFullYear();



        // Monta a string no formato aceito pelo input de data

        var dataAtual = ano + '-' + mes + '-' + dia;



        // Seleciona todos os inputs com a classe 'data'

        var inputsData = document.querySelectorAll('.data');



        // Aplica o valor e o mínimo em todos os campos de data

        inputsData.forEach(function(input) {

            input.value = dataAtual; // Predefine a data atual

            input.setAttribute('min', dataAtual); // Define o valor mínimo

        });

    </script>

</body>



</html>