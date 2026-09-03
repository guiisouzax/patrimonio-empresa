<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
  header('Location: index.php');
  exit;
}

require_once 'Database.php';
$db = new Database();
$pdo = $db->getConnection();


$busca = $_GET['busca'] ?? '';
$setor_filtro = $_GET['setor'] ?? '';
$tipo_filtro = $_GET['tipo'] ?? '';
$status_filtro = $_GET['status'] ?? '';

$sql = "SELECT * FROM equipamentos WHERE 1=1";
$params = [];

$sucesso = $_GET['sucesso'] ?? '';

$mensagensSucesso = [
    '1' => 'Equipamento cadastrado com sucesso!',
    'editado' => 'Equipamento atualizado com sucesso!',
    'excluido' => 'Equipamento excluído com sucesso!',
];


if (!empty($busca)) {
    $sql .= " AND (patrimonio LIKE :busca1 OR descricao LIKE :busca2 OR responsavel LIKE :busca3 OR setor LIKE :busca4 OR tipo LIKE :busca5)";
    $params[':busca1'] = '%' . $busca . '%';
    $params[':busca2'] = '%' . $busca . '%';
    $params[':busca3'] = '%' . $busca . '%';
    $params[':busca4'] = '%' . $busca . '%';
    $params[':busca5'] = '%' . $busca . '%';
}

if (!empty($setor_filtro)) {
    $sql .= " AND setor = :setor";
    $params[':setor'] = $setor_filtro;
}

if (!empty($tipo_filtro)) {
    $sql .= " AND tipo = :tipo";
    $params[':tipo'] = $tipo_filtro;
}

if (!empty($status_filtro)) {
    $sql .= " AND status = :status";
    $params[':status'] = $status_filtro;
}

$sql .= " ORDER BY setor ASC, data_cadastro DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$equipamentos = $stmt->fetchAll();

$totalItens = $pdo->query("SELECT COUNT(*) FROM equipamentos")->fetchColumn();
$totalEmUso = $pdo->query("SELECT COUNT(*) FROM equipamentos WHERE status = 'em_uso'")->fetchColumn();
$totalDisponivel = $pdo->query("SELECT COUNT(*) FROM equipamentos WHERE status = 'disponivel'")->fetchColumn();
$totalManutencao = $pdo->query("SELECT COUNT(*) FROM equipamentos WHERE status = 'manutencao'")->fetchColumn();


?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventário de T.I</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased">

  <?php if (!empty($sucesso) && isset($mensagensSucesso[$sucesso])): ?>
<div id="alertaSucesso" class="fixed top-4 right-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg shadow-sm flex items-center gap-2 z-50">
    <i class="fa-solid fa-circle-check"></i>
    <?php echo $mensagensSucesso[$sucesso]; ?>
</div>
<?php endif; ?>
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-bold text-slate-900">Inventário de Equipamentos</h1>
        <p class="text-sm text-slate-500">Gerenciamento e controle patrimonial de T.I</p>
      </div>
      <div class="flex items-center gap-4">
    <span class="text-sm text-slate-600">
        Olá, <b><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></b>
    </span>
    <a href="logout.php" 
       class="flex items-center gap-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 hover:border-red-300 hover:text-red-600 hover:bg-red-50 px-3.5 py-2 rounded-lg shadow-sm transition-colors">
        <i class="fa-solid fa-right-from-bracket"></i>
        Sair
    </a>
</div>
      <button onclick="abrirModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2.5 rounded-lg text-sm shadow-sm transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Novo Equipamento
      </button>
    </div>

    <!-- Cards de Métricas (Zerados) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-sm">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total de Itens</p>
        <p class="text-2xl font-bold text-slate-900 mt-1"><?php echo $totalItens; ?></p>
      </div>
      <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-sm">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Em Uso</p>
        <p class="text-2xl font-bold text-emerald-600 mt-1"><?php echo $totalEmUso; ?></p>
      </div>
      <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-sm">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Disponíveis (Estoque)</p>
        <p class="text-2xl font-bold text-blue-600 mt-1"><?php echo $totalDisponivel; ?></p>
      </div>
      <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-sm">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Em Manutenção</p>
        <p class="text-2xl font-bold text-amber-600 mt-1"><?php echo $totalManutencao; ?></p>
      </div>
    </div>

    <form method="GET" action="inventario.php" class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm mb-6 flex flex-col md:flex-row gap-3 justify-between items-center">
  
  <div class="relative w-full md:w-96">
    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
    </div>
    <input type="text" id="campoBusca" name="busca" value="<?php echo htmlspecialchars($busca); ?>" placeholder="Buscar por patrimônio, modelo ou usuário..." 
           class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
  </div>

  <div class="flex flex-wrap gap-3 w-full md:w-auto">
    
    <select name="setor" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
      <option value="">Todos os Setores</option>
      <?php foreach (['financeiro','operacoes','expedicao','comercial','ti','rh','servidor'] as $opcao): ?>
        <option value="<?php echo $opcao; ?>" <?php echo $setor_filtro === $opcao ? 'selected' : ''; ?>><?php echo ucfirst($opcao); ?></option>
      <?php endforeach; ?>
    </select>

    <select name="tipo" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
      <option value="">Todos os Equipamentos</option>
      <?php foreach (['notebook','desktop','monitor','impressora','periferico','coletor'] as $opcao): ?>
        <option value="<?php echo $opcao; ?>" <?php echo $tipo_filtro === $opcao ? 'selected' : ''; ?>><?php echo ucfirst($opcao); ?></option>
      <?php endforeach; ?>
    </select>

    <select name="status" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
      <option value="">Todos os Status</option>
      <option value="em_uso" <?php echo $status_filtro === 'em_uso' ? 'selected' : ''; ?>>Em Uso</option>
      <option value="disponivel" <?php echo $status_filtro === 'disponivel' ? 'selected' : ''; ?>>Disponível</option>
      <option value="manutencao" <?php echo $status_filtro === 'manutencao' ? 'selected' : ''; ?>>Manutenção</option>
    </select>

  </div>
</form>
    <!-- Tabela -->
<div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-left text-sm text-slate-600">
      <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase font-semibold text-slate-500 tracking-wider">
        <tr>
          <th scope="col" class="px-6 py-3.5">Patrimônio</th>
          <th scope="col" class="px-6 py-3.5">Equipamento</th>
          <th scope="col" class="px-6 py-3.5">Descrição</th>
          <th scope="col" class="px-6 py-3.5">Setor</th>
          <th scope="col" class="px-6 py-3.5">Responsável</th>
          <th scope="col" class="px-6 py-3.5">Status</th>
          <th scope="col" class="px-6 py-3.5 text-right">Data</th>
          <th scope="col" class="px-6 py-3.5 text-right">Ações</th>
        </tr>
      </thead>
      <tbody id="corpoTabela" class="divide-y divide-slate-100">
        <?php if (count($equipamentos) === 0): ?>
          <tr>
            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
              <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
              <p class="font-medium text-slate-600">Nenhum equipamento cadastrado ainda.</p>
              <p class="text-xs text-slate-400 mt-1">Clique em "Novo Equipamento" para começar a registrar.</p>
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($equipamentos as $equipamento): ?>
          <tr>
            <td class="px-6 py-4 font-medium text-slate-900"><?php echo htmlspecialchars($equipamento['patrimonio']); ?></td>
            <td class="px-6 py-4"><?php echo htmlspecialchars(ucfirst($equipamento['tipo'])); ?></td>
            <td class="px-6 py-4 text-slate-500 max-w-xs truncate"><?php echo htmlspecialchars($equipamento['descricao'] ?? '-'); ?></td>
            <td class="px-6 py-4">
            <span class="px-2 py-1 rounded-full text-xs font-medium
            <?php 
              echo match($equipamento['setor']) {
                'financeiro' => 'bg-emerald-100 text-emerald-700',
                'operacoes' => 'bg-blue-100 text-blue-700',
                'expedicao' => 'bg-amber-100 text-amber-700',
                'comercial' => 'bg-purple-100 text-purple-700',
                'ti' => 'bg-indigo-100 text-indigo-700',
                'rh' => 'bg-pink-100 text-pink-700',
                'servidor' => 'bg-slate-200 text-slate-700',
              };
            ?>">
        <?php echo $equipamento['setor'] === 'ti' ? 'T.I' : ucfirst($equipamento['setor']); ?>
    </span>
</td>
            <td class="px-6 py-4"><?php echo htmlspecialchars($equipamento['responsavel'] ?? '-'); ?></td>
            <td class="px-6 py-4">
    <span class="px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
        <?php echo str_replace('_', ' ', ucfirst($equipamento['status'])); ?>
    </span>
</td>
            <td class="px-6 py-4 text-right"><?php echo date('d/m/Y', strtotime($equipamento['data_cadastro'])); ?></td>
            <td class="px-6 py-4 text-right">
            <button onclick="abrirModalEditar(<?php echo $equipamento['id']; ?>)" 
              class="text-slate-400 hover:text-indigo-600 transition-colors">
            <i class="fa-solid fa-pen"></i>
            </button>
            </td>
          </tr>
          <!-- Modal Editar Equipamento #<?php echo $equipamento['id']; ?> -->
<div id="modalEditar<?php echo $equipamento['id']; ?>" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-slate-900">Editar Equipamento</h2>
            <button onclick="fecharModalEditar(<?php echo $equipamento['id']; ?>)" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="editar_equipamento.php" class="space-y-3">
            <input type="hidden" name="id" value="<?php echo $equipamento['id']; ?>">

            <input type="text" name="patrimonio" required value="<?php echo htmlspecialchars($equipamento['patrimonio']); ?>"
                   class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">

            <select name="tipo" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                <?php foreach (['notebook','desktop','monitor','impressora','periferico','coletor'] as $opcao): ?>
                    <option value="<?php echo $opcao; ?>" <?php echo $equipamento['tipo'] === $opcao ? 'selected' : ''; ?>>
                        <?php echo ucfirst($opcao); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <textarea name="descricao" rows="2" placeholder="Descrição"
                      class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm"><?php echo htmlspecialchars($equipamento['descricao'] ?? ''); ?></textarea>

            <select name="setor" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                <?php foreach (['financeiro','operacoes','expedicao','comercial','ti','rh','servidor'] as $opcao): ?>
                    <option value="<?php echo $opcao; ?>" <?php echo $equipamento['setor'] === $opcao ? 'selected' : ''; ?>>
                        <?php echo ucfirst($opcao); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="text" name="responsavel" placeholder="Responsável" value="<?php echo htmlspecialchars($equipamento['responsavel'] ?? ''); ?>"
                   class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">

            <select name="status" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                <?php foreach (['disponivel','em_uso','manutencao'] as $opcao): ?>
                    <option value="<?php echo $opcao; ?>" <?php echo $equipamento['status'] === $opcao ? 'selected' : ''; ?>>
                        <?php echo str_replace('_',' ',ucfirst($opcao)); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="flex gap-2 pt-2">
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-lg text-sm">
                    Salvar Alterações
                </button>
            </div>
        </form>

        <form method="POST" action="excluir_equipamento.php" onsubmit="return confirm('Tem certeza que deseja excluir este equipamento? Essa ação não pode ser desfeita.');" class="mt-2">
            <input type="hidden" name="id" value="<?php echo $equipamento['id']; ?>">
            <button type="submit" class="w-full text-red-600 hover:bg-red-50 font-medium py-2.5 rounded-lg text-sm border border-red-200">
                <i class="fa-solid fa-trash"></i> Excluir Equipamento
            </button>
        </form>
    </div>
</div>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<!-- Modal Novo Equipamento -->
  <div id="modalNovoEquipamento" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
          <div class="flex justify-between items-center mb-4">
              <h2 class="text-lg font-bold text-slate-900">Novo Equipamento</h2>
              <button onclick="fecharModal()" class="text-slate-400 hover:text-slate-600">
                  <i class="fa-solid fa-xmark"></i>
              </button>
          </div>

          <form method="POST" action="cadastrar_equipamento.php" class="space-y-3">
              <input type="text" name="patrimonio" placeholder="Patrimônio (ex: TI-0004)" required
                    class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">

              <select name="tipo" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                  <option value="">Selecione o tipo</option>
                  <option value="notebook">Notebook</option>
                  <option value="desktop">Desktop</option>
                  <option value="monitor">Monitor</option>
                  <option value="impressora">Impressora</option>
                  <option value="periferico">Periférico</option>
                  <option value="coletor">Coletor</option>
              </select>
          
              <textarea name="descricao" placeholder="Descrição (modelo, configurações, etc.)" rows="2"
                class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm"></textarea>

              <select name="setor" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                  <option value="">Selecione o setor</option>
                  <option value="financeiro">Financeiro</option>
                  <option value="operacoes">Operações</option>
                  <option value="expedicao">Expedição</option>
                  <option value="comercial">Comercial</option>
                  <option value="ti">T.I</option>
                  <option value="rh">RH</option>
                  <option value="servidor">Servidor</option>
              </select>

              <input type="text" name="responsavel" placeholder="Responsável (opcional)"
                    class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">

              <select name="status" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                  <option value="disponivel">Disponível</option>
                  <option value="em_uso">Em Uso</option>
                  <option value="manutencao">Manutenção</option>
              </select>

              <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-lg text-sm mt-2">
                  Cadastrar
              </button>
          </form>
      </div>
  </div>

<script>
  function abrirModal() {
      const modal = document.getElementById('modalNovoEquipamento');
      modal.classList.remove('hidden');
      modal.classList.add('flex');
  }
  function fecharModal() {
      const modal = document.getElementById('modalNovoEquipamento');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
  }
  function abrirModalEditar(id) {
    const modal = document.getElementById('modalEditar' + id);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }
  function fecharModalEditar(id) {
    const modal = document.getElementById('modalEditar' + id);
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }
  

  let timeoutBusca;
  document.getElementById('campoBusca').addEventListener('input', function() {
      clearTimeout(timeoutBusca);
      const texto = this.value;

      timeoutBusca = setTimeout(() => {
          fetch('buscar_equipamentos.php?busca=' + encodeURIComponent(texto))
              .then(resposta => resposta.text())
              .then(html => {
                  document.getElementById('corpoTabela').innerHTML = html;
              });
      }, 300);
  });

  const alerta = document.getElementById('alertaSucesso');
  if (alerta) {
    setTimeout(() => {
      alerta.style.transition = 'opacity 0.5s';
      alerta.style.opacity = '0';
      setTimeout(() => alerta.remove(), 500);
    }, 3000);
  }
</script>
</body>
</html>