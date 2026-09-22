<?php
require_once "auth.php";
require_once "config.php";

$nome = $endereco = $salario = $setor_id = "";
$nome_erro = $endereco_erro = $salario_erro = $foto_erro = $setor_erro = "";

// Busca a lista de setores para o campo <select>
$setores = [];
$res_setores = mysqli_query($link, "SELECT * FROM setores ORDER BY nome ASC");
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
</head>
<body>
    <div class="container">
        <h2>Cadastrar Funcionário</h2>
        <p>Preencha os campos abaixo para salvar o funcionário.</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Foto de Perfil</label>
                <input type="file" name="foto" accept="image/*">
                <?php if(!empty($foto_erro)): ?>
                    <span class="erro"><?php echo $foto_erro; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome" value="<?php echo $nome; ?>" class="<?php echo (!empty($nome_erro)) ? 'campo-erro' : ''; ?>">
                <?php if(!empty($nome_erro)): ?>
                    <span class="erro"><?php echo $nome_erro; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Setor</label>
                <select name="setor_id" class="<?php echo (!empty($setor_erro)) ? 'campo-erro' : ''; ?>">
                    <option value="">Selecione um Setor</option>
                    <?php foreach ($setores as $s): ?>
                        <option value="<?php echo $s['id']; ?>" <?php echo ($setor_id == $s['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($s['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if(!empty($setor_erro)): ?>
                    <span class="erro"><?php echo $setor_erro; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Endereço</label>
                <textarea name="endereco" rows="3" class="<?php echo (!empty($endereco_erro)) ? 'campo-erro' : ''; ?>"><?php echo $endereco; ?></textarea>
                <?php if(!empty($endereco_erro)): ?>
                    <span class="erro"><?php echo $endereco_erro; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Salário</label>
                <input type="text" name="salario" value="<?php echo $salario; ?>" class="<?php echo (!empty($salario_erro)) ? 'campo-erro' : ''; ?>">
                <?php if(!empty($salario_erro)): ?>
                    <span class="erro"><?php echo $salario_erro; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <input type="submit" value="Salvar">
                <a href="index.php">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>