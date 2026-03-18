<?php
    session_start();

    $pathOnly = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

	if(isset($_POST['out'])) { 
		session_destroy();		
//		header("Location: http://".$_SERVER['HTTP_HOST'].$pathOnly."/index.php");
		header("location: ".$_SERVER['PHP_SELF']);
	}

	require_once __DIR__ . '/config.php';
?> 
<!------------------------------------------------------------------------------------
  -- Acesso a Bases de Dados (MySQL) com PHP                                        --  
  -- Exemplo de ligação a uma base de dados MySQL usando PDO (PHP Data Objects)     --
  --                                                                                --
  -- Requer acesso a SGBD MySQL com os seguintes elementos:                         --
  --   - base de dados php_crud                                                   --  
  --   - tabela utilizadores(                                                       --         
  --       username VARCHAR(30) NOT NULL PK,                                        --  
  --       password VARCHAR(255) NOT NULL,                                          --  
  --       user VARCHAR(50) NOT NULL IX,                                            --  
  --       email VARCHAR(640) NOT NULL AK                                           --  
  ------------------------------------------------------------------------------------>
<head>
	<title>Acesso a Bases de Dados (MySQL) com PHP</title>
</head>    
    <?php

	// descomentar linha abaixo para verificar o DSN de ligação em output para ecrã
	// echo 'dsn: '.$dsn;
	// echo '<br />';

	// também se pode usar console log do browser para ver o DSN
	// é mais prático porque não interfere com a página apresentada no browser
	// fazer F12 para abrir as ferramentas de desenvolvimento do browser
	echo "<script>";
	echo "console.log('(Javacsript) dsn: ".$dsn."')";
	echo "</script>"; 
	// Neste exemplo, a escrita em consola é feita em JavaScript por se tratar de uma funcionalidade do browser (client side)

	// O PHP também tem a possibilidade de fazer log de erros:
	// error_log('(em PHP)dsn: '.$dsn);
	// Este log é escrito no ficheiro de log do servidor web (ex: Apache), não sendo visível no browser



	try {
//		$pdo = new PDO($dsn, $user, $pass, $options);
		$query = $pdo->query('SHOW VARIABLES LIKE "version"');
		$row = $query->fetch();

		echo 'MySQL version: ' . $row['Value'] . '<br />';
		echo 'Ligação a servidor e Base de Dados estabelecida com sucesso.<br>';

		// A ligação foi bem sucedida, agora pode executar consultas
		$sql_query = 'SELECT username,user,email FROM utilizadores';
		$stmt = $pdo->query($sql_query);

		echo "<table border='1'>";
		echo '<tr>';
		echo '<td colspan="3">';				
		echo 'Total de linhas: ' . $stmt->rowCount();
		echo '</td>';
		echo '<tr >';
		echo '<td> Username </td>';
		echo '<td> Utilizador </td>';
		echo '<td> email </td>';
		echo '</tr >';

		while ($row = $stmt->fetch()) {
			echo '<tr>';
			echo '<td>' . $row['username'] . '</td>';
			echo '<td>' . $row['user'] . '</td>';
			echo '<td>' . $row['email'] . '</td>';
			echo '</tr>';
		}
		echo '</table>';

		$pdo = null; // Fecha a ligação
					// Não é obrigatório, o PHP fecha a ligação automaticamente no final do script
					// mas é uma boa prática fechar a ligação para poupar recursos e evitar conflitos 
					// no caso de scripts mais longos e complexos ou com mais ligações abertas

		// Tratamento de erros no acesso à tabela
	} catch (PDOException $e) {
		echo "Erro: " . $e->getMessage();
	}

	if(isset($_SESSION['user']) && isset($_SESSION['profile'])) {
		echo 'Utilizador ' . $_SESSION['user'] . ' com perfil ' . $_SESSION['profile'] . ' autenticado.<br>';
		echo '<form method="post" action="'.htmlspecialchars($_SERVER['PHP_SELF']).'" style="display:inline;">
				<button type="submit" name="out" value="Log out" style="background:none;border:none;color:blue;text-decoration:underline;cursor:pointer;padding:0;">
					Sair
				</button>
		</form>';
		echo '<br>';
	} else {	
		echo 'Utilizador não autenticado. ';
		echo '<a href="http://'.$_SERVER['HTTP_HOST'].$pathOnly.'/login.php">fazer login</a>';
	}
?>
