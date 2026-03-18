<?php
    session_start();

    $pathOnly = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

	if(isset($_POST['out'])) { 
		session_destroy();		
		header("Location: http://".$_SERVER['HTTP_HOST'].$pathOnly."/index.php");
	}
	if(isset($_SESSION['user']) && isset($_SESSION['pcode'] ) && isset($_SESSION['profile'] )) {
		echo 'Utilizador ' . $_SESSION['user'] . ' com perfil ' . $_SESSION['profile'] . ' autenticado.<br>';
		echo '<form method="post" action="'.htmlspecialchars($_SERVER['PHP_SELF']).'" style="display:inline;">
				<button type="submit" name="out" value="Log out" style="background:none;border:none;color:blue;text-decoration:underline;cursor:pointer;padding:0;">
					Sair
				</button>
		</form>';
		echo '<br>';
	} else {	
		echo 'Utilizador não autenticado.<br>';
		echo 'Aguarde, dentro de alguns segundos será reencaminhado para a página de login...';
		echo '<script type="text/javascript">';
		echo 't=setTimeout("window.location=\'http://'.$_SERVER['HTTP_HOST'].$pathOnly.'/index.php\'",0)';
		echo '</script>';		
		exit();
	}

	require_once __DIR__ . '/config.php';
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
	    <title>Exemplo cRud em PHP: Listar Utilizadores</title>
        
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" href="style/style.css" type="text/css">

		<script language="javascript" type="text/javascript" src="funcional.js">
		</script>
	</head> 
	<body>
	<?php

		try {
			// A ligação foi bem sucedida, agora pode executar consultas
			$sql_query = 'SELECT u.username,u.user,u.email, p.designation  FROM utilizadores u JOIN profile p on u.profile = p.code';
// Se o utilizador autenticado não for administrador, só pode ver os seus próprios dados
// Só administradores podem ver e editar os dados de outros utilizadores			
			if($_SESSION['pcode'] != 'ADM') {
				$sql_query .= ' WHERE u.username = :username';
			}
			$sql_query .= ' ORDER BY u.user;';

			$stmt = $pdo->prepare($sql_query);
			if($_SESSION['pcode'] != 'ADM') {
				$stmt->execute([':username' => $_SESSION['user']]);
			} else {
				$stmt->execute();
			}

			
			echo "<table border='1'>";
			echo '<tr>';
			echo '<td colspan="6">';				
			echo 'Total de linhas: ' . $stmt->rowCount();
			echo '</td>';
//	Só administradores podem adicionar novos utilizadores						
			if($_SESSION['pcode'] != 'ADM') {
				echo '<td width="25">&nbsp;</td>';
			} else {	
				echo '<td width="25"><a href="userAdd.php"><img width="25" src="images/newUser.webp" alt="Adicionar Utilizador"></a></td>';
			}
			echo '</tr>';
			echo '<tr >';
			echo '<td> Username </td>';
			echo '<td> Utilizador </td>';
			echo '<td> email </td>';
			echo '<td> perfil </td>';
			echo '<td colspan="3"> &nbsp; </td>';
			echo '</tr >';

			while ($row = $stmt->fetch()) {
				echo '<tr>';
				echo '<td>' . $row['username'] . '</td>';
				echo '<td>' . $row['user'] . '</td>';
				echo '<td>' . $row['email'] . '</td>';
				echo '<td>' . $row['designation'] . '</td>';
	//  Chamada a páginas de edição e eliminação usando o método GET, passando o username como parâmetro na query string            
	//          echo '<td><a href="userEdit.php?username='.$row['username'].'"><img width="20" src="images\edit.webp" alt="Editar"></a></td>';
	//          echo '<td><a href="userDelete.php?username='.$row['username'].'"><img width="20" src="images\delete.webp" alt="Eliminar"></a></td>';
	//  Chamada a páginas de edição e eliminação usando o método POST, passando o username como parâmetro num campo oculto do formulário            
				echo '<td width="25">
						<form method="post" action="userEdit.php" style="display:inline;">
							<input type="hidden" name="username" value="'.htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8').'">
							<button type="submit" name="action" value="edit" style="border:0;background:transparent;padding:0;cursor:pointer;">
							<img width="20" src="images/edit.webp" alt="Editar">
							</button>
						</form>
						</td>';

				echo '<td width="25">
						<form method="post" action="userPass.php" style="display:inline;" onsubmit="return confirm(\'Alterar a password de '.htmlspecialchars($row['user'], ENT_QUOTES, 'UTF-8').'?\');">
							<input type="hidden" name="username" value="'.htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8').'">
							<button type="submit" name="action" value="pass" style="border:0;background:transparent;padding:0;cursor:pointer;">
							<img width="20" src="images/password.webp" alt="Alterar Password">
							</button>
						</form>
						</td>';
//	Um utilizador não administrador não pode eliminar outros utilizadores, mesmo que seja o próprio						
				if($_SESSION['pcode'] != 'ADM') {
					echo '<td width="25">&nbsp;</td>';
				} else {	
					echo '<td width="25">
							<form method="post" action="userDel.php" style="display:inline;" onsubmit="return confirm(\'Eliminar utilizador '.htmlspecialchars($row['user'], ENT_QUOTES, 'UTF-8').'? \');">
								<input type="hidden" name="username" value="'.htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8').'">
								<button type="submit" name="action" value="delete" style="border:0;background:transparent;padding:0;cursor:pointer;">
								<img width="20" src="images/delete.webp" alt="Eliminar">
								</button>
							</form>
							</td>';
					echo '</tr>';
				}
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