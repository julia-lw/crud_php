<?php
require_once "auth.php";
require_once "config.php";

$nome =  "";
$nome_erro = "";

// Busca a lista de setores para o campo <select>
$setores = [];
$res_setores = mysqli_query($link, "SELECT * FROM pedido ORDER BY nome ASC");
if ($res_setores) {
    while ($s = mysqli_fetch_assoc($res_setores)) {
        $setores[] = $s;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validação do Nome
    $input_nome = trim($_POST["nome"]);
    if (empty($input_nome)) {
        $nome_erro = "Por favor, insira um nome.";
    } else {
        $nome = $input_nome;
    }
    
    
    // Inserção no Banco
    if (empty($nome_erro) && empty($endereco_erro) && empty($salario_erro) && empty($foto_erro) && empty($setor_erro)) {
        $sql = "INSERT INTO setores (nome) VALUES (?)";
         
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_nome);
            
            $param_nome = $nome;
           
            
            if (mysqli_stmt_execute($stmt)) {
                header("location: index.php");
                exit();
            } else {
                echo "Ops! Algo deu errado. Tente novamente.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Registro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">
    <div class="max-w-md mx-auto my-10 p-6 bg-white rounded-xl shadow-sm border border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800 mb-1">Cadastrar Funcionário</h2>
        <p class="text-sm text-gray-500 mb-6">Preencha os campos abaixo para salvar o funcionário.</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" class="space-y-4">
          

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome do setor</label>
                <input type="text" name="nome" value="<?php echo $nome; ?>" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 <?php echo (!empty($nome_erro)) ? 'border-red-500' : 'border-gray-300'; ?>">
                <?php if(!empty($nome_erro)): ?>
                    <span class="text-red-500 text-xs mt-1 block"><?php echo $nome_erro; ?></span>
                <?php endif; ?>
            </div>

            
          

            

            <div class="pt-4 flex items-center gap-3">
                <input type="submit" value="Salvar" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg transition cursor-pointer">
                <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-5 py-2 rounded-lg transition">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>