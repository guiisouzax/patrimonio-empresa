<?php 
session_start();

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    exit;
}

require_once 'Database.php';
$db = new Database();
$pdo = $db->getConnection();

$busca = $_GET['busca'] ?? '';

$sql = "SELECT * FROM equipamentos WHERE 1=1";
$params = [];

if (!empty($busca)) {
    $sql .= " AND (patrimonio LIKE :busca1 OR descricao LIKE :busca2 OR responsavel LIKE :busca3 OR setor LIKE :busca4 OR tipo LIKE :busca5)";
    $params[':busca1'] = '%' . $busca . '%';
    $params[':busca2'] = '%' . $busca . '%';
    $params[':busca3'] = '%' . $busca . '%';
    $params[':busca4'] = '%' . $busca . '%';
    $params[':busca5'] = '%' . $busca . '%';
}


$sql .= " ORDER BY setor ASC, data_cadastro DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$equipamentos = $stmt->fetchAll();

if (count($equipamentos) === 0) {
    echo '<tr><td colspan="8" class="px-6 py-12 text-center text-slate-400">Nenhum equipamento encontrado.</td></tr>';
    exit;
}

foreach ($equipamentos as $equipamento):
?>
<tr>
    <td class="px-6 py-4 font-medium text-slate-900"><?php echo htmlspecialchars($equipamento['patrimonio']); ?></td>
    <td class="px-6 py-4"><?php echo htmlspecialchars(ucfirst($equipamento['tipo'])); ?></td>
    <td class="px-6 py-4 text-slate-500 max-w-xs truncate"><?php echo htmlspecialchars($equipamento['descricao'] ?? '-'); ?></td>
    <td class="px-6 py-4"><?php echo htmlspecialchars(ucfirst($equipamento['setor'])); ?></td>
    <td class="px-6 py-4"><?php echo htmlspecialchars($equipamento['responsavel'] ?? '-'); ?></td>
    <td class="px-6 py-4">
        <span class="px-2 py-1 rounded-full text-xs font-medium
            <?php 
            echo match($equipamento['status']) {
                'em_uso' => 'bg-emerald-100 text-emerald-700',
                'disponivel' => 'bg-blue-100 text-blue-700',
                'manutencao' => 'bg-amber-100 text-amber-700',
            };
            ?>">
            <?php echo str_replace('_', ' ', ucfirst($equipamento['status'])); ?>
        </span>
    </td>
    <td class="px-6 py-4 text-right"><?php echo date('d/m/Y', strtotime($equipamento['data_cadastro'])); ?></td>
    <td class="px-6 py-4 text-right">
        <button onclick="abrirModalEditar(<?php echo $equipamento['id']; ?>)" class="text-slate-400 hover:text-indigo-600 transition-colors">
            <i class="fa-solid fa-pen"></i>
        </button>
    </td>
</tr>
<?php endforeach; ?>