<?php
session_start();

include('../protect.php'); // Inclui a função de proteção ao acesso da página
require_once('../conexao.php');
$conexao = novaConexao();

$registros = [];
$erro = false;

// Supondo que você queira o primeiro valor do array (ajuste conforme necessário)
$codPed = is_array($_SESSION['codPed']) ? $_SESSION['codPed'][0] : $_SESSION['codPed'];

try {
    $sql = "SELECT * FROM itens_pedido WHERE codPed = :codPed";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':codPed', $codPed, PDO::PARAM_INT); // Vincula o valor de codPed
    $stmt->execute();
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC); // Recupera todos os registros

    $sql_vTot = "SELECT SUM(valorTotal) AS total FROM itens_pedido WHERE codPed = :codPed";
    $stmt = $conexao->prepare($sql_vTot);
    $stmt->bindParam(':codPed', $codPed, PDO::PARAM_INT); // Vincula o valor de codPed
    $stmt->execute(); // Executa a consulta
    $vTot = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? null; // Obtém o total

    $sql_updateVTot = "UPDATE pagentg SET valorTotal = :vTot WHERE codPed = :codPed";
    $stmt = $conexao->prepare($sql_updateVTot);
    $stmt->bindValue(':vTot', $vTot);
    $stmt->bindParam(':codPed', $codPed, PDO::PARAM_INT); // Vincula o valor de codPed novamente
    $stmt->execute();
    
} catch (PDOException $e) {
    $erro = true; // Configura erro se houver uma exceção
    echo "Erro: " . $e->getMessage();
}

if (isset($_POST['delete'])) {
    $id = $_POST['cod_itensPed'];

    $sql = "DELETE FROM itens_pedido WHERE cod_itensPed = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->execute()) {
        echo "Linha excluída com sucesso!";
        // Redireciona para evitar reenviar o formulário
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "Erro ao excluir linha: ";
    }
}

if (isset($_POST['inserirItens'])) {
    $_SESSION['codPed'] = [
        $_POST['codPed']
    ];

    header("Location: inserirItens.php");
    exit;
}

if (isset($_POST['edit'])) {
    $_SESSION['cod_itensPed'] = [
        $_POST['cod_itensPed']
    ];
    $_SESSION['origem'] = ["itensPed.php"];
    header("Location: editItensPed.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css?v=1.2">
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
                            <a class="dropdown-item" href="./cadPed.php">Cadastro</a>
                            <a class="dropdown-item" href="#">Consulta</a>
                        </div>
                    </li> <!-- FECHA O DROPDOWN MENU-->

                    <li class="nav-item dropdown"> <!-- LINK BOOTSTRAP DORPDOWN MENU-->
                        <a class="nav-link dropdown-toggle cor_fonte" href="#" id="navbarDropdownMenuLink"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Agenda
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="../agenda/insAge.php">Inserir</a>
                            <a class="dropdown-item" href="../agenda/consAge.php">Consultar</a>
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

    <div class="container mb-5">
        <div class="row text-center justify-content-center">
            <div class="col-2">
                <a href="consPed.php" class="btn btn-outline-danger">
                    Voltar
                </a>
            </div>
            <div class="col-2">
                <form method="POST">
                    <input type="hidden" name="codPed" value="<?php echo $codPed ?>">
                    <button type="submit" name="inserirItens" class="btn btn-outline-primary">
                        Inserir Itens
                    </button>
                </form>
            </div>
        </div>
    </div>

    <h3 class="text-center mb-5">Pedidos Cadastrados</h3>
    </div>
    <?php if ($erro): ?>
        <div class="alert alert-danger" role="alert">
            Não foi possível carregar os dados.
        </div>
    <?php else: ?>
        <div class="container container-custom">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>
                            <div class="row justify-content-center text-center titleCons">ID</div>
                        </th>
                        <th>
                            <div class="row justify-content-center text-center titleCons">Referente ao Pedido</div>
                        </th>
                        <th>
                            <div class="row justify-content-center text-center titleCons">Produto</div>
                        </th>
                        <th>
                            <div class="row justify-content-center text-center titleCons">Medida</div>
                        </th>
                        <th>
                            <div class="row justify-content-center text-center titleCons">Quantidade</div>
                        </th>
                        <th>
                            <div class="row justify-content-center text-center titleCons">Valor Unitário</div>
                        </th>
                        <th>
                            <div class="row justify-content-center text-center titleCons">Valor Total</div>
                        </th>
                        <th>
                            <div class="row justify-content-center text-center titleCons">Operações</div>
                        </th>
                    </tr>
                </thead>
                <?php foreach ($registros as $registro): ?>
                    <tbody>
                        <tr>
                            <td>
                                <div class="row justify-content-center registro">
                                    <?php echo ($registro['cod_itensPed']); ?>
                                </div>
                            </td>
                            <td>
                                <div class="row justify-content-center registro">
                                    <?php echo ($registro['codPed']); ?>
                                </div>
                            </td>
                            <td>
                                <div class="row justify-content-center registro">
                                    <?php echo ($registro['codPro']); ?>
                                </div>
                            </td>
                            <td>
                                <div class="row justify-content-center registro">
                                    <?php echo ($registro['medida']); ?>
                                </div>
                            </td>
                            <td>
                                <div class="row justify-content-center registro">
                                    <?php echo ($registro['quantidade']); ?>
                                </div>
                            </td>
                            <td>
                                <div class="row justify-content-center registro">
                                    <?php echo ($registro['valorUnit']); ?>
                                </div>
                            </td>
                            <td>
                                <div class="row justify-content-center registro">
                                    <?php echo ($registro['valorTotal']); ?>
                                </div>
                            </td>
                            <td>
                                <div class="row text-center justify-content-center">
                                    <div class="col-6">
                                        <form method="POST">
                                            <input type="hidden" name="cod_itensPed"
                                                value="<?php echo $registro['cod_itensPed']; ?>">
                                            <button type="submit" name="delete" class="btn btn-outline-danger">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                                                    <path
                                                        d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col-6">
                                        <form method="POST">
                                            <input type="hidden" name="cod_itensPed"
                                                value="<?php echo $registro['cod_itensPed']; ?>">
                                            <button type="submit" name="edit" class="btn btn-outline-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                    <path
                                                        d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                    <path fill-rule="evenodd"
                                                        d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>