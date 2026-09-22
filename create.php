<?php
require_once "auth.php";
require_once "config.php";

$nome = $descricao = $preco = "";
$nome_erro = $preco_erro = $foto_erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validação do Nome
    $input_nome = trim($_POST["nome"]);
    if (empty($input_nome)) {
        $nome_erro = "Por favor, insira o nome do produto.";
    } else {
        $nome = $input_nome;
    }
    
    // Descrição (opcional)
    $descricao = trim($_POST["descricao"]);

    // Validação do Preço
    $input_preco = trim($_POST["preco"]);
    if (empty($input_preco)) {
        $preco_erro = "Por favor, insira o preço do produto.";     
    } else {
        $preco = str_replace(',', '.', $input_preco); // Converte vírgula para ponto
    }

    // Upload da Foto
    $foto_nome = "default-product.png";
    if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] == 0) {
        $extensoes_permitidas = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png", "webp" => "image/webp");
        $filename = $_FILES["foto"]["name"];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!array_key_exists($ext, $extensoes_permitidas)) {
            $foto_erro = "Formato de imagem inválido.";
        } elseif ($_FILES["foto"]["size"] > 2 * 1024 * 1024) {
            $foto_erro = "A imagem excede o tamanho limite de 2MB.";
        } else {
            $foto_nome = uniqid() . "." . $ext;
            if (!is_dir("uploads")) {
                mkdir("uploads", 0777, true);
            }
            move_uploaded_file($_FILES["foto"]["tmp_name"], "uploads/" . $foto_nome);
        }
    }
    
    // Inserção no Banco
    if (empty($nome_erro) && empty($preco_erro) && empty($foto_erro)) {
        $sql = "INSERT INTO produto (nome, descricao, preco, foto) VALUES (?, ?, ?, ?)";
         
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssds", $param_nome, $param_descricao, $param_preco, $param_foto);
            
            $param_nome = $nome;
            $param_descricao = $descricao;
            $param_preco = $preco;
            $param_foto = $foto_nome;
            
            if (mysqli_stmt_execute($stmt)) {
                header("location: index.php");
                exit();
            } else {
                echo "Ops! Algo deu errado ao salvar o produto.";
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
    <title>Cadastrar Produto</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">
    <div class="max-w-md mx-auto my-10 p-6 bg-white rounded-xl shadow-sm border border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800 mb-1">Cadastrar Novo Produto</h2>
        <p class="text-sm text-gray-500 mb-6">Preencha os campos abaixo para adicionar um produto ao estoque.</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto do Produto</label>
                <input type="file" name="foto" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <?php if(!empty($foto_erro)): ?>
                    <span class="text-red-500 text-xs mt-1 block"><?php echo $foto_erro; ?></span>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto</label>
                <input type="text" name="nome" value="<?php echo htmlspecialchars($nome); ?>" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 <?php echo (!empty($nome_erro)) ? 'border-red-500' : 'border-gray-300'; ?>">
                <?php if(!empty($nome_erro)): ?>
                    <span class="text-red-500 text-xs mt-1 block"><?php echo $nome_erro; ?></span>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                <textarea name="descricao" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($descricao); ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Preço (R$)</label>
                <input type="text" name="preco" value="<?php echo htmlspecialchars($preco); ?>" placeholder="0.00" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 <?php echo (!empty($preco_erro)) ? 'border-red-500' : 'border-gray-300'; ?>">
                <?php if(!empty($preco_erro)): ?>
                    <span class="text-red-500 text-xs mt-1 block"><?php echo $preco_erro; ?></span>
                <?php endif; ?>
            </div>

            <div class="pt-4 flex items-center gap-3">
                <input type="submit" value="Salvar Produto" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-5 py-2 rounded-lg transition cursor-pointer">
                <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-5 py-2 rounded-lg transition">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>