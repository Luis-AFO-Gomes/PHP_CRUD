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
	require_once __DIR__ . '/config.php';
?> 

<html>
    <head>		
	    <title>Exemplo crUd em PHP: Alterar Password</title>

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="<?php echo $pathOnly; ?>/style/forms.css" type="text/css">

	<script type="text/javascript">
	    function verPreenche(campo){
//  função para verificar se um dado campo de preenchimento obrigatorio está preenchido
            with (campo){
                if (value==null||value==""){
//  verifica-se se o campo está vazio ou se tem uma string nula                    
//				    alert("vazio");
				    return false;
				}
			    else{
//				    alert("preenchido");
				    return true;
				}
		    }
	    }

		function validarForm(formulario){
            var txtOutput="";
                var valido=true;
                var txtStatus="";
                with (formulario){
//  Verificar preenchimento do campo OLD PASSWORD
//  Altera-se formato e alerta-se o utilizador se o campo não estiver preenchido
                    if (verPreenche(oldPass)==false){				
                        oldPass.focus();
                        document.getElementById('frmPass').style.color='red';
                        txtStatus=txtStatus + "Password antiga nao esta preenchida \n ";
                        valido=false;
                    } else {
					    document.getElementById('frmPass').style.color='blue';
				    }

//  Verificar preenchimento do campo Nova Password
//  Altera-se formato e alerta-se o utilizador se o campo não estiver preenchido
				    if (verPreenche(newPass)==false){		
                        newPass.focus();
                        document.getElementById('frmNewPass').style.color='red';
                        txtStatus=txtStatus + "Nova Password nao esta preenchida \n ";
                        valido=false;
                    } else {
                        document.getElementById('frmNewPass').style.color='blue';
                    }

//  Verificar preenchimento do campo Confirmar Password
//  Altera-se formato e alerta-se o utilizador se o campo não estiver preenchido ou se não for igual ao campo Nova Password
				    if (verPreenche(confPass)==false){		
                        confPass.focus();
                        document.getElementById('frmConfPass').style.color='red';
                        txtStatus=txtStatus + "Confirmar Password nao esta preenchida \n ";
                        valido=false;
                    } else if (newPass.value !== confPass.value){
                        confPass.focus();
                        document.getElementById('frmConfPass').style.color='red';
                        txtStatus=txtStatus + "Passwords nao coincidem \n ";
                        valido=false;
                    }else {
                        document.getElementById('frmConfPass').style.color='blue';
                    }


                }

                if (valido==false){
                    alert ("Verifique preenchimento do formulario \n "+txtStatus+" \n *Campos de Preenchimento obrigatorio","erro de preenchimento");
    			}
			return valido;
		}
	</script>
	</head>

	<body>
        <h1>Alterar a palavra passe de um utilizador na base de dados.</h1><br><br>
        <?php
            $action   = $_POST['action'] ?? 'pass';   // default
            $username = $_POST['username'] ?? null;

            if($username){
                printf("<br>Alterar a palavra passe para o utilizador: %s", htmlspecialchars($username, ENT_QUOTES, 'UTF-8'));  
            
                if ($action === 'pass') {
        ?>

<!-- O campo username é passado como um campo oculto do formulário para que possa ser utilizado na página de processamento do formulário 
  -- (neste caso, a própria página) para identificar o utilizador a alterar                    
  -->
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return validarForm(this);" method="post">

                        <input type="hidden" name="username" value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>">
                        <table align="center">
                            <tr id="frmPass">
                                <td align="right" width="150">Password antiga: </td>
                                <td align="left"><input type="password" name="oldPass" size="40"></td>
                                <td align="left" width="25">*</td>
                            </tr>
                            <tr id="frmNewPass" >
                                <td align="right">Nova Password: </td>
                                <td align="left"><input type="password" name="newPass" size="40"></td>
                                <td align="left" width="25">*</td>
                            </tr>
                            <tr id="frmConfPass" >
                                <td align="right">Confirmar Password: </td>
                                <td align="left"><input type="password" name="confPass" size="40"></td>
                                <td align="left" width="25">*</td>
                            </tr>
                            <tr>
                                <td id="status" colspan="3">* preenchimeto obrigatorio</td>
                            </tr>
                            <tr>
                                <td colspan="3" align="center">
                                    <button type="submit" name="action" value="save">Guardar</button>
                                </td>
                            </tr>
                        </table>
                    </form>
        <?php 
                } elseif ($action === 'save') {

                try {

                    $username = trim($_POST["username"] ?? "");
                    $oldPass  = trim($_POST["oldPass"] ?? "");
                    $newPass  = trim($_POST["newPass"] ?? "");

                    if ($username === "" || $oldPass === "" || $newPass === "") {
                        die("Erro: campos obrigatórios em falta.");
                    }

                    $sql = "SELECT password FROM utilizadores WHERE username = :nome";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':nome' => $username]);
// Só se vai ler 1 linha
                    $row = $stmt->fetch();

                    if ($row) {
                        if (password_verify($oldPass, $row['password'])) {
                            $passHash = password_hash($newPass, PASSWORD_DEFAULT);

//  Atualizar o utilizador na base de dados usando uma consulta SQL UPDATE
                            $sql_update = "UPDATE utilizadores SET password = :password WHERE username = :username";
                            $stmt = $pdo->prepare($sql_update);
                        
//  Associar os valores aos parâmetros e executar a instrução SQL
                            $stmt->execute([
                                ':password' => $passHash,
                                ':username' => $username
                            ]);
                            echo "<br>Password atualizada com sucesso.";
                        } else {
//  A password antiga fornecida não é válida para o utilizador identificado, 
//  por segurança a mensagem é genérica para não revelar qual o erro ocorrido (utilizador ou password errados)                            
                            echo "<br>Nome de utilizador ou palavra-passe inválidos.";
                            exit;
                        }
                    } else {
//  utilizador não existe
//  por segurança a mensagem é genérica para não revelar qual o erro ocorrido (utilizador ou password errados)   
                        echo "<br>Nome de utilizador ou palavra-passe inválidos.";
                    }
                } catch (PDOException $e) {
// falha na ligação à base de dados                                           
                    echo "Erro DB: " . $e->getMessage();
                }

                } else {
                    http_response_code(400); exit('Invalid action');
                }          
            } else {
                echo "<br>Erro: utilizador não identificado.";
                echo "<br><a href='$pathOnly/userList.php'>Voltar à lista de utilizadores</a>";
            } 
            ?>


    </body> 
</html>