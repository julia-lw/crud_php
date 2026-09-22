<?php
//require_once "auth.php";
//require_once "config.php";

$nome = $endereco = $salario = $setor_id = "";
$nome_erro = $endereco_erro = $salario_erro = $foto_erro = $setor_erro = "";

// Busca a lista de setores para o campo <select>
$setores = [];
$res_produto = mysqli_query($link, "SELECT * FROM setores ORDER BY nome ASC");
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
    
    // Validação do Setor
    $input_setor = trim($_POST["setor_id"]);
    if (empty($input_setor)) {
        $setor_erro = "Por favor, selecione um setor.";
    } else {
        $setor_id = $input_setor;
    }

    // Validação do Endereço
    $input_endereco = trim($_POST["endereco"]);
    if (empty($input_endereco)) {
        $endereco_erro = "Por favor, insira um endereço.";     
    } else {
        $endereco = $input_endereco;
    }
    
    // Validação do Salário
    $input_salario = trim($_POST["salario"]);
    if (empty($input_salario) || !ctype_digit($input_salario)) {
        $salario_erro = "Por favor, insira um salário válido (número inteiro).";     
    } else {
        $salario = $input_salario;
    }

    // Upload da Foto
    $foto_nome = "default-avatar.png";
    if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] == 0) {
        $extensoes_permitidas = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png", "webp" => "image/webp");
        $filename = $_FILES["foto"]["name"];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if (!array_key_exists($ext, $extensoes_permitidas)) {
            $foto_erro = "Formato de imagem inválido.";
        } elseif ($_FILES["foto"]["size"] > 2 * 1024 * 1024) {
            $foto_erro = "Imagem excede o tamanho limite de 2MB.";
        } else {
            $foto_nome = uniqid() . "." . $ext;
            move_uploaded_file($_FILES["foto"]["tmp_name"], "uploads/" . $foto_nome);
        }
    }
    
    // Inserção no Banco
    if (empty($nome_erro) && empty($endereco_erro) && empty($salario_erro) && empty($foto_erro) && empty($setor_erro)) {
        $sql = "INSERT INTO funcionarios (nome, endereco, salario, foto, setor_id) VALUES (?, ?, ?, ?, ?)";
         
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssisi", $param_nome, $param_endereco, $param_salario, $param_foto, $param_setor_id);
            
            $param_nome = $nome;
            $param_endereco = $endereco;
            $param_salario = $salario;
            $param_foto = $foto_nome;
            $param_setor_id = $setor_id;
            
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto de Perfil</label>
                <input type="file" name="foto" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <?php if(!empty($foto_erro)): ?>
                    <span class="text-red-500 text-xs mt-1 block"><?php echo $foto_erro; ?></span>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                <input type="text" name="nome" value="<?php echo $nome; ?>" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 <?php echo (!empty($nome_erro)) ? 'border-red-500' : 'border-gray-300'; ?>">
                <?php if(!empty($nome_erro)): ?>
                    <span class="text-red-500 text-xs mt-1 block"><?php echo $nome_erro; ?></span>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Setor</label>
                <select name="setor_id" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 <?php echo (!empty($setor_erro)) ? 'border-red-500' : 'border-gray-300'; ?>">
                    <option value="">Selecione um Setor</option>
                    <?php foreach ($setores as $s): ?>
                        <option value="<?php echo $s['id']; ?>" <?php echo ($setor_id == $s['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($s['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if(!empty($setor_erro)): ?>
                    <span class="text-red-500 text-xs mt-1 block"><?php echo $setor_erro; ?></span>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Endereço</label>
                <textarea name="endereco" rows="3" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 <?php echo (!empty($endereco_erro)) ? 'border-red-500' : 'border-gray-300'; ?>"><?php echo $endereco; ?></textarea>
                <?php if(!empty($endereco_erro)): ?>
                    <span class="text-red-500 text-xs mt-1 block"><?php echo $endereco_erro; ?></span>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Salário</label>
                <input type="text" name="salario" value="<?php echo $salario; ?>" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 <?php echo (!empty($salario_erro)) ? 'border-red-500' : 'border-gray-300'; ?>">
                <?php if(!empty($salario_erro)): ?>
                    <span class="text-red-500 text-xs mt-1 block"><?php echo $salario_erro; ?></span>
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