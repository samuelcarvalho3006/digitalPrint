<?php
// Inicia a sessão para acessar os dados
session_start();
include('../protect.php');
require_once('../conexao.php');
$conexao = novaConexao();

$codPed = is_array($_SESSION['codPed']) ? $_SESSION['codPed'][0] : $_SESSION['codPed'];

try {

    $sql_pedido = "SELECT * FROM pedidos WHERE codPed = :codPed";
    $stmt = $conexao->prepare($sql_pedido);
    $stmt->bindParam(':codPed', $codPed, PDO::PARAM_INT); // Vincula o valor de codPed
    $stmt->execute();
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC); // Recupera apenas o primeiro registro

    //-----------------------------------------------------------------------------------------------------------------------------------------    

    $sql_itensPedido = "SELECT * FROM itens_pedido WHERE codPed = :codPed";
    $stmt = $conexao->prepare($sql_itensPedido);
    $stmt->bindParam(':codPed', $codPed, PDO::PARAM_INT); // Vincula o valor de codPed
    $stmt->execute();
    $itensPedido = $stmt->fetchAll(PDO::FETCH_ASSOC); // Recupera todos os registros

    //-----------------------------------------------------------------------------------------------------------------------------------------

    $sql_pagEntg = "SELECT * FROM pagentg WHERE codPed = :codPed";
    $stmt = $conexao->prepare($sql_pagEntg);
    $stmt->bindParam(':codPed', $codPed, PDO::PARAM_INT); // Vincula o valor de codPed
    $stmt->execute();
    $pagEntg = $stmt->fetch(PDO::FETCH_ASSOC); // Recupera apenas o primeiro registro

} catch (PDOException $e) {
    $erro = true; // Configura erro se houver uma exceção
    echo "Erro: " . $e->getMessage();
}


//---------------------------------------------------------------------------------------------------------------

if (isset($_POST['enviar'])) {
    try {
        $sql_enviarPed = "UPDATE pedidos SET nomeCli = :nome, tipoPessoa = :tipoPess, contato = :ctt, dataPrev = :dtPrev, status = 'pendente' WHERE codPed = :codPed";
        $stmt = $conexao->prepare($sql_enviarPed);
        $stmt->bindParam(':codPed', $codPed, PDO::PARAM_INT); // Vincula o valor de codPed
        $stmt->bindValue(':nome', $_POST['cliente']);
        $stmt->bindValue(':tipoPess', $_POST['pessoa']);
        $stmt->bindValue(':ctt', $_POST['contato']);
        $stmt->bindValue(':dtPrev', $_POST['dataPrev']);
        $stmt->execute();

        // Atualiza a tabela pagentg
        $sql_enviarPag = "UPDATE pagentg SET entrega = :entr, logradouro = :logr, numero = :num, bairro = :bair, cidade = :cid, estado = :est, cep = :cep WHERE codPed = :codPed";
        $stmt = $conexao->prepare($sql_enviarPag);
        $stmt->bindValue(':entr', isset($_POST['entrega']) ? $_POST['entrega'] : '');
        $stmt->bindValue(':logr', isset($_POST['logradouro']) ? $_POST['logradouro'] : '');
        $stmt->bindValue(':num', isset($_POST['numero']) ? $_POST['numero'] : '');
        $stmt->bindValue(':bair', isset($_POST['bairro']) ? $_POST['bairro'] : '');
        $stmt->bindValue(':cid', isset($_POST['cidade']) ? $_POST['cidade'] : '');
        $stmt->bindValue(':est', isset($_POST['estado']) ? $_POST['estado'] : '');
        $stmt->bindValue(':cep', isset($_POST['cep']) ? $_POST['cep'] : '');
        $stmt->bindParam(':codPed', $codPed, PDO::PARAM_INT); // Vincula o valor de codPed novamente
        $stmt->execute();

        if ($_POST['entrega'] == 'retirada'){
            $sql = "UPDATE pagentg SET logradouro = :logr, numero = :num, bairro = :bair, cidade = :cid, estado = :est, cep = :cep WHERE codPed = :codPed";
            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':logr', " ");
        $stmt->bindValue(':num', " ");
        $stmt->bindValue(':bair', " ");
        $stmt->bindValue(':cid', " ");
        $stmt->bindValue(':est', " ");
        $stmt->bindValue(':cep', " ");
        $stmt->bindParam(':codPed', $codPed, PDO::PARAM_INT); // Vincula o valor de codPed novamente
        $stmt->execute();
        }

        if ($_SESSION['origem']){
            header('location: '. $_SESSION['origem'][0]);
        } else {
        header('location: consPed.php');
        }
    } catch (PDOException $e) {
        $erro = true; // Configura erro se houver
        echo "Erro: " . $e->getMessage();
    }
}
if (isset($_POST['cancelar'])) {

    if($_SESSION['origem']){
    header("Location: " . $_SESSION['origem'][0]);
    } else{
        echo ("erro ao retornar à página");
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edição de Dados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css?v=1.6">
</head>

<body>
    <div class="container-fluid cabecalho"> <!-- CABECALHO -->
        <nav class="navbar navbar-light navbar-expand-md" style="background-color: #FFFF;">
            <a class="navbar-brand m-2" href="../admInicial.php">
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
                            <a class="dropdown-item" href="#">Cadastro</a>
                            <a class="dropdown-item" href="./consPed.php">Consulta</a>
                        </div>
                    </li> <!-- FECHA O DROPDOWN MENU-->

                    <li class="nav-item dropdown"> <!-- LINK BOOTSTRAP DORPDOWN MENU-->
                        <a class="nav-link dropdown-toggle cor_fonte" href="#" id="navbarDropdownMenuLink"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Agenda
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="../agenda/insAge.php">Inserir</a>
                            <a class="dropdown-item" href="../agenda/consAge.php">Consultar </a>
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
    
        <h1 class="text-center mb-5">Edição de Dados do Pedido</h1>

        <form method="POST">

            <div class="row row-custom justify-content-center text-center">
                <div class="row">
                    <div class="col-4">
                        <div class="form-group mb-3">
                            <p><strong>Responsável:</strong><br>
                                <input type="text" name="responsavel" class="form-control"
                                    value="<?php echo htmlspecialchars($pedido['cod_func']); ?>" readonly>
                            </p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group mb-3">
                            <p><strong>Data do pedido:</strong><br>
                                <input type="date" class="form-control data" name="datPedido"
                                    value="<?php echo htmlspecialchars($pedido['dataPed']); ?>" readonly>
                            </p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group mb-3 text-center">
                            <p><strong>Data prevista:</strong><br>
                                <input type="date" class="form-control data" name="dataPrev"
                                    value="<?php echo htmlspecialchars($pedido['dataPrev']); ?>">
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row mb-5">
                    <div class="col-4">
                        <div class="form-group mb-3">
                            <p><strong>Nome do cliente:</strong><br>
                                <input type="text" name="cliente" class="form-control"
                                    value="<?php echo htmlspecialchars($pedido['nomeCli']); ?>">
                            </p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group mb-3">
                            <p><strong>Tipo de Pessoa:</strong><br>
                                <input type="radio" id="pessoaFis" name="pessoa" class="form-check-input" value="Física"
                                    <?php echo ($pedido['tipoPessoa'] == 'Física') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="pessoaFis">Física</label>

                                <input type="radio" id="pessoaJur" name="pessoa" class="form-check-input ms-3"
                                    value="Jurídica" <?php echo ($pedido['tipoPessoa'] == 'Jurídica') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="pessoaJur">Jurídica</label>
                            </p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group mb-3">
                            <p><strong>Contato:</strong><br>
                                <input type="text" name="contato" class="form-control"
                                    value="<?php echo htmlspecialchars($pedido['contato']); ?>">
                            </p>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row mt-5">
                    <div class="col-3">
                        <div class="form-group mb-3">
                            <p><strong>Entrega:</strong><br>
                                <input type="radio" id="retirada" name="entrega" value="retirada"
                                    class="form-check-input" <?php echo ($pagEntg['entrega'] == 'retirada') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="retirada">Retirada</label>

                                <input type="radio" id="entrega" name="entrega" value="entrega"
                                    class="form-check-input ms-3" <?php echo ($pagEntg['entrega'] == 'entrega') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="entrega">Entrega</label>
                            </p>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group mb-3">
                            <p><strong>Logradouro:</strong><br>
                                <input type="text" name="logradouro" class="form-control"
                                    value="<?php echo htmlspecialchars($pagEntg['logradouro']); ?>">
                            </p>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group mb-3">
                            <p><strong>Número:</strong><br>
                                <input type="text" name="numero" class="form-control"
                                    value="<?php echo htmlspecialchars($pagEntg['numero']); ?>">
                            </p>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group mb-3">
                            <p><strong>Bairro:</strong><br>
                                <input type="text" name="bairro" class="form-control"
                                    value="<?php echo htmlspecialchars($pagEntg['bairro']); ?>">
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row m-3">
                    <div class="col-4">
                        <div class="form-group mb-3">
                            <p><strong>Cidade:</strong><br>
                                <input type="text" name="cidade" class="form-control"
                                    value="<?php echo htmlspecialchars($pagEntg['cidade']); ?>">
                            </p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group mb-3">
                            <p><strong>Estado:</strong><br>
                                <input type="text" name="estado" class="form-control"
                                    value="<?php echo htmlspecialchars($pagEntg['estado']); ?>">
                            </p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group mb-3">
                            <p><strong>Cep:</strong><br>
                                <input type="text" name="cep" class="form-control"
                                    value="<?php echo htmlspecialchars($pagEntg['cep']); ?>">
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4 btn-group-custom">

                <button type="submit" class="btn btn-outline-dark btn-personalizado" name="cancelar">Cancelar Alterações</button>

                <button type="submit" class="btn btn-success btn-personalizado" name="enviar">Finalizar
                    Alterações</button>

            </div>
        </form>
    </div>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>