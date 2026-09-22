<?php
require_once "auth.php";
require_once "config.php";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel de Controle</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 font-sans">
    
    <!-- Barra Superior / Header de Autenticação -->
    <header class="bg-slate-900 text-white border-b border-slate-800">
        <div class="max-w-5xl mx-auto px-6 py-3 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-building text-blue-500"></i>
                <span class="font-bold text-sm tracking-wide">Compra de Produtos</span>
            </div>
            <div class="flex items-center gap-4 text-sm">
                <span class="text-slate-300">Olá, <strong class="text-white"><?php echo htmlspecialchars($_SESSION["admin_nome"]); ?></strong></span>
                <a href="logout.php" class="bg-slate-800 hover:bg-red-600 text-slate-300 hover:text-white px-3 py-1.5 rounded-md transition duration-150 flex items-center gap-1.5 text-xs font-semibold">
                    <i class="fa fa-right-from-bracket"></i> Sair
                </a>
            </div>
        </div>
    </header>

    <div class="max-w-5xl mx-auto my-10 p-6 bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">Detalhes dos Produtos</h2>
            <a href="create.php" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-4 py-2 rounded-lg transition inline-flex items-center gap-2 shadow-sm">
                <i class="fa fa-plus"></i> Cadastrar Novo Produto
            </a>
            <a href="create_setor.php" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg transition inline-flex items-center gap-2 shadow-sm">
                <i class="fa fa-plus"></i> Adicionar Novo Pedido
            </a>
          
        </div>

        <?php
        $sql = "SELECT f.*, s.nome AS setor_nome 
                FROM funcionarios f 
                INNER JOIN setores s ON f.setor_id = s.id 
                ORDER BY f.id DESC";

        if ($result = mysqli_query($link, $sql)) {
            if (mysqli_num_rows($result) > 0) {
                echo '<div class="overflow-x-auto rounded-lg border border-gray-200">';
                echo '<table class="w-full border-collapse text-left text-sm text-gray-600">';
                echo '<thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold">';
                echo '<tr>';
                echo '<th class="px-4 py-3 border-b">#</th>';
                echo '<th class="px-4 py-3 border-b">Nome</th>';
                echo '<th class="px-4 py-3 border-b">Setor</th>';
                echo '<th class="px-4 py-3 border-b">Endereço</th>';
                echo '<th class="px-4 py-3 border-b">Salário</th>';
                echo '<th class="px-4 py-3 border-b text-center">Ações</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody class="divide-y divide-gray-200">';
                
                while ($row = mysqli_fetch_array($result)) {
                    $foto_path = (!empty($row['foto']) && file_exists('uploads/' . $row['foto'])) ? 'uploads/' . $row['foto'] : 'https://via.placeholder.com/40';

                    echo '<tr class="hover:bg-gray-50 transition">';
                    echo '<td class="px-4 py-3 font-medium text-gray-900">' . $row['id'] . '</td>';
                    echo '<td class="px-4 py-3 flex items-center gap-3">';
                    echo '<img src="' . $foto_path . '" class="w-9 h-9 rounded-full object-cover border border-gray-200">';
                    echo htmlspecialchars($row['nome']);
                    echo '</td>';
                    echo '<td class="px-4 py-3"><span class="bg-blue-50 text-blue-700 font-medium px-2.5 py-1 rounded-md text-xs border border-blue-100">' . htmlspecialchars($row['setor_nome']) . '</span></td>';
                    echo '<td class="px-4 py-3">' . htmlspecialchars($row['endereco']) . '</td>';
                    echo '<td class="px-4 py-3">R$ ' . number_format($row['salario'], 2, ',', '.') . '</td>';
                    echo '<td class="px-4 py-3 text-center space-x-3">';
                    echo '<a href="read.php?id='. $row['id'] .'" class="text-blue-600 hover:text-blue-800"><i class="fa fa-eye"></i></a>';
                    echo '<a href="update.php?id='. $row['id'] .'" class="text-amber-600 hover:text-amber-800"><i class="fa fa-pencil"></i></a>';
                    echo '<a href="delete.php?id='. $row['id'] .'" class="text-red-600 hover:text-red-800"><i class="fa fa-trash"></i></a>';
                    echo '</td>';
                    echo '</tr>';
                }
                
                echo '</tbody>';
                echo '</table>';
                echo '</div>';
                mysqli_free_result($result);
            } else {
                echo '<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">Nenhum registro encontrado.</div>';
            }
        }
        mysqli_close($link);
        ?>
    </div>
</body>
</html>