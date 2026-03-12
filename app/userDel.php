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
<?php
    $pathOnly = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
/*	$pathOnly é utilizado para obter o caminho da página actual sem o nome do ficheiro para ser utilizado 
	na construção do URL de redireccionamento para a página de login no caso de utilizador não identificado

	Num servidor web pode utilizar-se a variável 'PATH_INFO', que não está definida quando se utiliza LOCALHOST
	Pode-se testar esta variável utilizando
		echo $_SERVER['SERVER_NAME'].'<br>';
	Que não devolverá qualquer valor para servidor locais sem path definida
 */	
?> 
<html>
    <head>
	    <title>Exemplo cruD em PHP: Eliminar Utilizador</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="style/style.css" type="text/css">
        <script language="javascript" type="text/javascript" src="scripts/funcional.js">
        </script>
	</head>

	<body>
        <h1>Eliminar um utilizador na base de dados.</h1><br><br>
        <?php
            $action   = $_POST['action'] ?? 'delete';   // default
            $username = $_POST['username'] ?? null;

            if($username){
                printf("<br>Eliminar o utilizador ",$_POST['username']);  
            
                if ($action === 'delete') {
// Processar a eliminação do utilizador da base de dados
// (a implementação desta parte depende da estrutura da tabela e dos campos do formulário)
                    echo "Dados recebidos para eliminar o utilizador: <br>";
                    echo "Username: " . htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') . "<br>";

                    //  Ligação à base de dados, igual ao exemplo de listar utilizadores (index.php)
                    $host = 'php_crud-mysql-1';
                    $db   = 'php_crud';
                    $user = 'root';
                    $pass = 'my5@fEp@s5';
                    $charset = 'utf8mb4';

                    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
                    $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ];

                    try {
                        $pdo = new PDO($dsn, $user, $pass, $options);
//  Converter e 'limpar' os dados recebidos do formulário
//  A limpesa também podia ser feita no Javascript, antes do envio do formulário, mas é boa prática fazê-la no servidor
//  -- operador 'null  coalescing' (??) evita aviso/erro por variáveis indefinidas/vazias
//  -- Trim remove espaços em branco no início e no fim --
                        $username = trim($_POST["username"] ?? "");

                        if ($username === "") {
                            die("Erro: username não indicado.");
                        }

//  Atualizar o utilizador na base de dados usando uma consulta SQL UPDATE
                        $sql_delete = "DELETE FROM utilizadores WHERE username = :username";
                        $stmt = $pdo->prepare($sql_delete);
                        
//  Associar os valores aos parâmetros e executar a instrução SQL
                        $stmt->execute([
                            ':username' => $username
                        ]);
                        echo "Utilizador eliminado com sucesso.";

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