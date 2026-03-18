<?php
    session_start();
    $pathOnly = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	require_once __DIR__ . '/config.php';

    if(isset($_SESSION['user']) && isset($_SESSION['pcode'] ) && isset($_SESSION['profile'] )) {
        echo 'Utilizador ' . $_SESSION['user'] . ' com perfil ' . $_SESSION['profile'] . ' autenticado.<br>';
        echo '<br>';
        if($_SESSION['pcode'] != 'ADM') {
            echo 'Só administradores podem eliminar utilizadores... ';
            echo '<script type="text/javascript">';
            echo 't=setTimeout("window.location=\'http://'.$_SERVER['HTTP_HOST'].$pathOnly.'/userList.php\'",2000)';
            echo '</script>';		
            exit();
         } 
    } else {
//  Redirect automático (sem tempo de espera) para a página de login se o utilizador não estiver autenticado
//        echo 'Utilizador não autenticado.<br>';	
        echo '<script type="text/javascript">';
        echo 't=setTimeout("window.location=\'http://'.$_SERVER['HTTP_HOST'].$pathOnly.'/index.php\'",0)';
        echo '</script>';		
        exit();
    }
?> 
<!------------------------------------------------------------------------------------
  -- Acesso a Bases de Dados (MySQL) com PHP                                        --  
  -- Exemplo de ligação a uma base de dados MySQL usando PDO (PHP Data Objects)     --
  --                                                                                --
  -- Requer acesso a SGBD MySQL com os seguintes elementos:                         --
  --   - base de dados epge25_sim                                                   --  
  --   - tabela utilizadores(                                                       --         
  --       username VARCHAR(30) NOT NULL PK,                                        --  
  --       password VARCHAR(255) NOT NULL,                                          --  
  --       user VARCHAR(50) NOT NULL IX,                                            --  
  --       email VARCHAR(640) NOT NULL AK                                           --  
  ------------------------------------------------------------------------------------>
<html>
    <head>
	    <title>Exemplo cruD em PHP: Eliminar Utilizador</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="<?php echo $pathOnly; ?>/style/forms.css" type="text/css">
        <script language="javascript" type="text/javascript" src="<?php echo $pathOnly; ?>/scripts/funcional.js">
        </script>
	</head>

	<body>
        <h1>Eliminar um utilizador na base de dados.</h1><br><br>
        <?php
            $action   = $_POST['action'] ?? 'delete';   // default
            $username = $_POST['username'] ?? null;

            if($username){
                printf("<br>Eliminar o utilizador %s", htmlspecialchars($username, ENT_QUOTES, 'UTF-8'));  
            
                if ($action === 'delete') {
// Processar a eliminação do utilizador da base de dados
                    echo "Dados recebidos para eliminar o utilizador: <br>";
                    echo "Username: " . htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') . "<br>";

                    try {
                        $username = trim($_POST["username"] ?? "");

                        if ($username === "") {
                            die("Erro: username não indicado.");
                        }

//  Eliminar o utilizador na base de dados usando uma consulta SQL DELETE
                        $sql_delete = "DELETE FROM utilizadores WHERE username = :username";
                        $stmt = $pdo->prepare($sql_delete);
                        
//  Associar os valores aos parâmetros e executar a instrução SQL
                        $stmt->execute([
                            ':username' => $username
                        ]);
                        echo "Utilizador eliminado com sucesso.";
                        echo "<br><a href='$pathOnly/userList.php'>Voltar à Lista</a>";

                    } catch (PDOException $e) {
                        echo "Erro DB: " . $e->getMessage();
                    }
                } else {
                    http_response_code(400); exit('Invalid action');
                }          
            } else {
                echo "Erro: utilizador não identificado.";
                echo "<br><a href='$pathOnly/userList.php'>Voltar à lista de utilizadores</a>";
            } 
            ?>


    </body> 
</html>