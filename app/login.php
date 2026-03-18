<?php
    session_start();
    $pathOnly = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

    if(isset($_SESSION['user'] ) && isset($_SESSION['profile'] )) {
		echo 'Sessão iniciada com utilizador '.$_SESSION['user'].' ('.$_SESSION['profile'].')<br>';
		echo '<br>';
		echo 'Aguarde, dentro de alguns segundos será reencaminhado para a página de inicial...';
		echo '<script type="text/javascript">';
		echo 't=setTimeout("window.location=\'http://'.$_SERVER['HTTP_HOST'].$pathOnly.'/index.php\'",5000)';
		echo '</script>';		
	}
?> 
<html>
	<head>
	<title>Login em PHP com base de dados de utilizador</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<!--------------------------------------------------------------------------------------------
  -- Para melhor organização do código, que irá crescer em tamanho e complexidade           --
  -- passmos a utilizar formatação e funções client-side (javascript) em ficheiros externos --
  --                                                                                        --
  -- No caso do Javascript, é preciso ter em conta que o código tem diferenças entre estar  --
  -- embebido na página ou em ficheiro externo. Diferenças principais:                      --
  --    1. No ficheiro externo não é possível utilizar código PHP, pelo que todas as        --
  --       variáveis e textos devem ser escritos directamente em JS                         --
  --    2. O Javascript tem limitações no acesso a objetos HTML da página, nomeadamente na  --
  --       manipulação da estrutura de objectos                                             --
  --       por exemplo:                                                                     --  
  --        - não é possível usar document.write() para escrever na página                  --
  --        - não é possível usar innerHTML para alterar a estrutura de objectos            --
  --        - não se pode usar comando de atributos HTML:                                   --
  --            # WITH para referenciar estruturas de objectos                              --
  --            # eval() para executar código JS escrito em strings                         --
  --            # onload para executar código JS quando a página é carregada                --
  --            # onsubmit para validar um formulário antes de ser submetido                --
  --            # onkeypress para validar a introdução de dados                             --
  --            # entre outros...                                                           --     
  --       Para contornar estas limitações, é necessário utilizar funções de manipulação de --
  --       objectos do JS, como getElementById(), addEventListener(), createElement(), etc. --
  --       Estas funções permitem criar e manipular a estrutura de objectos da página de    --
  --       forma dinâmica, mas exigem uma abordagem diferente da escrita do código JS.      --
  -------------------------------------------------------------------------------------------->		
		<link rel="stylesheet" href="style/style.css" type="text/css">
	
		<script language="javascript" type="text/javascript" src="scripts/funcional.js">
		</script>
	</head>

	<body>
<!--       A variável de sessão com nome "<?php echo $sessName; ?>" foi criada.<br> -->
        <h1>Login - inserir credenciais válidas.</h1><br><br>
        <?php

	        require_once __DIR__ . '/config.php';

            if(isset($_POST["nome"]) && isset($_POST["pass"])){
                printf("<br>o utilizador %s está a fazer login para ",$_POST['nome']);

                try {
//                    $pdo = new PDO($dsn, $user, $pass, $options);

                    // value coming from POST (example)
                    $username = $_POST["nome"] ?? "";
                    $password = $_POST["pass"] ?? "";

                    $sql = "SELECT password,user,email,p.designation, p.code  FROM utilizadores u join profile p on u.profile = p.code  WHERE username = :nome";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':nome' => $username]);
                    // If you expect 1 row:
                    $row = $stmt->fetch();

                    if ($row) {
#############################################################################################################
#   SEGURANÇA e CRIPTOGRAFIA                                                                                #
#   Utilizador existe, verificar password                                                                   #  
#   Como a password foi guardada na base de dados está encriptada com password_hash(),                      #
#   utiliza-se a função complementar password_verify() para verificar a password sem a desencriptar         #
#   Esta função cria uma nova hash para password fornecida e compara com o hash guardado na base de dados   #
#   Por segurança, nunca se deve utilizar texto livre para comparar passwords                               #
#                                                                                                           #
#   A criptografia de passwords é um tema complexo e em evolução constante.                                 #
#   As funções de criptografia do PHP - password_hash() e password_verify() - são regularmente atualizadas  #
#   para se adaptarem às melhores práticas de segurança.                                                    #
#   Os algoritmos de criptografia utilizados podem ser alterados com o tempo, levando a calculos diferentes #
#   da hash para a mesma password.                                                                          #
#   Por este motivo, é importante manter o PHP actualizado e utilizar sempre estas funções nativas para     #
#   gerir criptografia.                                                                                     #
#   Mais informações: https://www.php.net/manual/en/function.password-hash.php                              #
#############################################################################################################   

                        if (password_verify($password, $row['password'])) {
                            echo "<br>Utilizador <strong>" . htmlspecialchars($row['user']) . "</strong> identificado com sucesso<br>";
                            echo "Perfil: " . htmlspecialchars($row['designation']) . "<br>";
                            echo "Email: " . htmlspecialchars($row['email']) . "<br>";
//  Guardar dados do utilizador na sessão para podermos integar este login com os exemplos anteriores                            
                            $_SESSION['user'] = $username;
                            $_SESSION['pcode'] = $row['code'];
                            $_SESSION['profile'] = $row['designation'];
                            echo "<br><a href='$pathOnly/userList.php'>Ir para a lista de utilizadores</a>";
                        } else {
//  Utilizador existe, mas a password está errada                            
                            echo "<br>Nome de utilizador ou palavra-passe inválidos.";
                            exit;
                        }
                    } else {
//  Utilizador não existe
//  Por segurança, a mensagem é igual à do caso de password errada para não se revelar qual o dos dois erros ocorreu                        
                        echo "<br>Nome de utilizador ou palavra-passe inválidos.";
                    }

                } catch (PDOException $e) {
                    echo "DB error: " . $e->getMessage();
                }

            } else {
            ?>

                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return validarForm(this);" method="post">
                    <table align="center">
                        <tr id="frmNome">
                            <td align="right" width="75">Nome: </td>
                            <td align="left"><input type="text" name="nome" size="30" <?php 
                                if(isset($_SESSION['log'])){
                                        echo "value='".$_SESSION['log']."'";
                                    }
                                ?>></td>
                            <td align="left" width="25">*</td>
                        </tr>
                        <tr id="frmPass" >
                            <td align="right">Password: </td>
                            <td align="left"><input type="password" name="pass" size="30"></td>
                            <td align="left" width="25">*</td>
                        </tr>
                        <tr>
                            <td id="status" colspan="3">* preenchimeto obrigatorio</td>
                        </tr>
                        <tr>
                            <td colspan="3" align="center"><input type="submit" value="Continuar »"></td>
                        </tr>
                    </table>
                </form>
            <?php 
            } 
            ?>
    </body> 
</html>