<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Hardware T.I</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="https://admin.raizestecnologia.com.br/favicon/icone.png">
</head>
<script>
    function toggleSenha() {
        const input = document.getElementById('senha');
        const icone = document.querySelector('.eye-icon');
        
        if (input.type === 'password'){
            input.type = 'text';
            icone.classList.remove('fa-eye');
            icone.classList.add('fa-eye-slash');
        }else{
            input.type = 'password';
            icone.classList.remove('fa-eye-slash');
            icone.classList.add('fa-eye');
        }

    }
</script>
<body>
    <div class="page-wrapper">
        <img src="assets/img/raizes.png" alt="Raizes" class="logo">
    <div class="login-container">
        <h2>Inventário T.I</h2><br><br>
        
      <form method="POST" action="autenticar.php">
        <div class="input-group">
            <div class="input-icon">
                <i class="fa-regular fa-envelope"></i>
                <input type="email" name="email" id="email" placeholder="E-mail" required>
            </div>
        </div>

        <div class="input-group">
                <div class="input-icon">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="senha" id="senha" placeholder="Senha" required>
                    <i class="fa-regular fa-eye eye-icon" onclick="toggleSenha()"></i>
                </div>
         </div>
            <button class="btn"><b>Entrar</b></button>
      </form>
    </div>
</div>
</body>
</html>