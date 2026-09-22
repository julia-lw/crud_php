<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit();
}

require_once "config.php";

$email = $senha = "";
$login_erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $senha = trim($_POST["senha"]);

    if (empty($email) || empty($senha)) {
        $login_erro = "Por favor, preencha o e-mail e a senha.";
    } else {
        $sql = "SELECT id, nome, email, senha FROM administradores WHERE email = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = $email;
            
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                
                // Utilização correta da função para Prepared Statements
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id, $nome, $email, $hashed_senha);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($senha, $hashed_senha)) {
                            // Senha correta: Inicia nova sessão
                            session_regenerate_id();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["admin_id"] = $id;
                            $_SESSION["admin_nome"] = $nome;
                            $_SESSION["admin_email"] = $email;
                            
                            header("location: index.php");
                            exit();
                        } else {
                            $login_erro = "E-mail ou senha inválidos.";
                        }
                    }
                } else {
                    $login_erro = "E-mail ou senha inválidos.";
                }
            } else {
                $login_erro = "Ops! Algo deu errado. Tente novamente mais tarde.";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Painel Administrativo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
        <!-- Cabeçalho -->
        <div class="bg-slate-900 p-8 text-center text-white">
            <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg shadow-blue-500/30">
                <i class="fa fa-user-shield text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Painel do Gestor</h1>
            <p class="text-slate-400 text-sm mt-1">Insira suas credenciais para acessar</p>
        </div>

        <!-- Formulário -->
        <div class="p-8">
            <?php if (!empty($login_erro)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg text-sm mb-6 flex items-center gap-3">
                    <i class="fa fa-circle-exclamation text-lg"></i>
                    <span><?php echo $login_erro; ?></span>
                </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-5">
                <div>
                    <label class="block text-xs uppercase tracking-wider text-slate-500 font-bold mb-2">E-mail</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fa fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required placeholder="admin@empresa.com" class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wider text-slate-500 font-bold mb-2">Senha</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fa fa-lock"></i>
                        </span>
                        <input type="password" name="senha" required placeholder="••••••••" class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-sm">
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition duration-200 flex items-center justify-center gap-2">
                    <span>Entrar no Sistema</span>
                    <i class="fa fa-arrow-right text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</body>
</html>