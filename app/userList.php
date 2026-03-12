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
	    <title>Exemplo cRud em PHP: Listar Utilizadores</title>
        
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" href="style/style.css" type="text/css">

		<script language="javascript" type="text/javascript" src="funcional.js">
		</script>
	</head> 
	<body>
		<?php
		// Variáveis para ligação à base de dados
		// alterar conforme a configuração do ambiente
		// em ambiente de produção, estas variáveis não devem estar em ficheiro de código
		// Não é obrigatótio definir os parâmetros de ligação numa variável, podem ser inseridos directamente no DSN
		// mas é uma boa prática para facilitar a leitura e manutenção do código
		$host = 'php_crud-mysql-1';
		$db   = 'php_crud';
		$user = 'root';
		$pass = 'my5@fEp@s5';
		$charset = 'utf8mb4';

		// Data Source Name (DSN) - string de ligação à base de dados
		$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

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

		$options = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		];

		try {
			$pdo = new PDO($dsn, $user, $pass, $options);

			// A ligação foi bem sucedida, agora pode executar consultas
			$sql_query = 'SELECT username,user,email FROM utilizadores';
			$stmt = $pdo->query($sql_query);

			echo "<table border='1'>";
			echo '<tr>';
			echo '<td colspan="6">';				
			echo 'Total de linhas: ' . $stmt->rowCount();
			echo '</td>';
			echo '<tr >';
			echo '<td> Username </td>';
			echo '<td> Utilizador </td>';
			echo '<td> email </td>';
			echo '<td colspan="3"> &nbsp; </td>';
			echo '</tr >';

			while ($row = $stmt->fetch()) {
				echo '<tr>';
				echo '<td>' . $row['username'] . '</td>';
				echo '<td>' . $row['user'] . '</td>';
				echo '<td>' . $row['email'] . '</td>';
	//  Chamada a páginas de edição e eliminação usando o método GET, passando o username como parâmetro na query string            
	//          echo '<td><a href="userEdit.php?username='.$row['username'].'"><img width="20" src="images\edit.webp" alt="Editar"></a></td>';
	//          echo '<td><a href="userDelete.php?username='.$row['username'].'"><img width="20" src="images\delete.webp" alt="Eliminar"></a></td>';
	//  Chamada a páginas de edição e eliminação usando o método POST, passando o username como parâmetro num campo oculto do formulário            
				echo '<td>
						<form method="post" action="userEdit.php" style="display:inline;">
							<input type="hidden" name="username" value="'.htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8').'">
							<button type="submit" name="action" value="edit" style="border:0;background:transparent;padding:0;cursor:pointer;">
							<img width="20" src="images/edit.webp" alt="Editar">
							</button>
						</form>
						</td>';

				echo '<td>
						<form method="post" action="userPass.php" style="display:inline;" onsubmit="return confirm(\'Alterar a password de '.htmlspecialchars($row['user'], ENT_QUOTES, 'UTF-8').'?\');">
							<input type="hidden" name="username" value="'.htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8').'">
							<button type="submit" name="action" value="pass" style="border:0;background:transparent;padding:0;cursor:pointer;">
							<img width="20" src="images/password.webp" alt="Alterar Password">
							</button>
						</form>
						</td>';

				echo '<td>
						<form method="post" action="userDel.php" style="display:inline;" onsubmit="return confirm(\'Eliminar utilizador '.htmlspecialchars($row['user'], ENT_QUOTES, 'UTF-8').'? \');">
							<input type="hidden" name="username" value="'.htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8').'">
							<button type="submit" name="action" value="delete" style="border:0;background:transparent;padding:0;cursor:pointer;">
							<img width="20" src="images/delete.webp" alt="Eliminar">
							</button>
						</form>
						</td>';
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
	?>
	</body>  
</html>  