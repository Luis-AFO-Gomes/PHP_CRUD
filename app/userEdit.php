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
	    <title>Exemplo crUd em PHP: Editar Utilizador</title>
        
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
	
		function validarMail(endereco){
    		with (endereco){
				posAt=value.indexOf("@");
				posPonto=value.lastIndexOf(".");
				if (posAt<1||posPonto-posAt<2){
					return false;
					}
				else{
					return true;
					}
				}
			}

		function validarForm(formulario){
            var txtOutput="";
                var valido=true;
                var txtStatus="";
                with (formulario){
//  Verificar preenchimento do campo USERNAME
//  Altera-se formato e alerta-se o utilizador se o campo não estiver preenchido
                    if (verPreenche(username)==false){				
                        username.focus();
                        document.getElementById('frmUserName').style.color='red';
                        txtStatus=txtStatus + "username nao esta preenchido \n ";
                        valido=false;
                    } else {
					    document.getElementById('frmUserName').style.color='blue';
				    }

//  Verificar preenchimento do campo Nome de utilizador
//  Altera-se formato e alerta-se o utilizador se o campo não estiver preenchido
				    if (verPreenche(nome)==false){		
                        nome.focus();
                        document.getElementById('frmNome').style.color='red';
                        txtStatus=txtStatus + "Nome nao esta preenchido \n ";
                        valido=false;
                    } else {
                        document.getElementById('frmNome').style.color='blue';
                    }

//  Verificar preenchimento do campo Email
//  Altera-se formato e alerta-se o utilizador se o campo não estiver preenchido
//  No caso do email, além de verificar o prenchimento, também se verifica o formato do endereço
				    if (verPreenche(email)==false){		
                        email.focus();
                        document.getElementById('frmEmail').style.color='red';
                        txtStatus=txtStatus + "Email nao esta preenchido \n ";
                        valido=false;
                    } else if (validarMail(email)==false){
                        email.focus();
                        document.getElementById('frmEmail').style.color='red';
                        txtStatus=txtStatus + "Email com formato invalido \n ";
                        valido=false;
                    }else {
                        document.getElementById('frmEmail').style.color='blue';
                    }

//  Verificar preenchimento do campo Perfil
//  Altera-se formato e alerta-se o utilizador se o campo não estiver preenchido
				    if (verPreenche(profile)==false){		
                        profile.focus();
                        document.getElementById('frmProfile').style.color='red';
                        txtStatus=txtStatus + "Perfil nao esta preenchido \n ";
                        valido=false;
                    } else {
                        document.getElementById('frmProfile').style.color='blue';
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
        <h1>Alterar um utilizador na base de dados.</h1><br><br>
        <?php
            $action   = $_POST['action'] ?? 'edit';   // default
            $username = $_POST['username'] ?? null;

            if($username){
                printf("<br>Alterar o utilizador ",$_POST['username']);  
            
                if ($action === 'edit') {
        ?>

                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return validarForm(this);" method="post">
                        <table align="center">
                            <tr id="frmUserName">
                                <td align="right" width="150">Nome de utilizador: </td>
                                <td align="left"><input type="text" name="username" size="40" readonly></td>
                                <td align="left" width="25">&nbsp</td>
                            </tr>
                            <tr id="frmNome" >
                                <td align="right">Nome: </td>
                                <td align="left"><input type="text" name="nome" size="40"></td>
                                <td align="left" width="25">*</td>
                            </tr>
                            <tr id="frmEmail" >
                                <td align="right">Email: </td>
                                <td align="left"><input type="text" name="email" size="40"></td>
                                <td align="left" width="25">*</td>
                            </tr>
                            <tr id="frmProfile" >
                                <td align="right"><label for="profile">Perfil: </label></td>
                                <td align="left">
<?php                               
		try {
			$pdo = new PDO($dsn, $user, $pass, $options);

			// A ligação foi bem sucedida, agora pode executar consultas
			$sql_query = 'SELECT p.code, p.designation  FROM profile p;';
			$stmt = $pdo->query($sql_query);     
                echo '<select name="profile" id="profile">';
                echo '<option value="">--Selecione um perfil--</option>';
			while ($row = $stmt->fetch()) {
				echo '<option value="' . htmlspecialchars($row['code'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($row['designation'], ENT_QUOTES, 'UTF-8') . '</option>';
			}
            echo '</select>';
        } catch (PDOException $e) {
            echo "DB error: " . $e->getMessage();
        }

?>                                    
                                </td>
                                <td align="left" width="25">*</td>
                            </tr>
                            <tr>
                                <td id="status" colspan="3">* preenchimeto obrigatorio</td>
                            </tr>
                            <tr>
                                <td align="right">
                                    <button type="submit" name="action" value="save">Guardar</button>
                                </td>
                                <td width="25">&nbsp;</td>
                                <td align="left"><input type="reset" value="Cancelar"></td>
                            </tr>
                        </table>
                    </form>
                    <table align="center">
                        <tr>
                            <td align="center">&nbsp;</td>
                            <td align="center"><a href="<?php echo $pathOnly; ?>/userList.php">Voltar à Lista</a></td>
                        </tr>
                    </table>
        <?php 
                    try {
    //  Obter dados de utilizador da base de dados e preencher o formulário de edição com os dados
                        $sql_query = 'SELECT u.username,u.user,u.email, u.profile  FROM utilizadores u WHERE u.username = :username';
                        $stmt = $pdo->prepare($sql_query);
                        $stmt->execute(['username' => $_POST['username']]);
                        $userData = $stmt->fetch();

                        if ($userData) {
                            // Preencher o formulário com os dados do utilizador
                            echo "<script>";
                            echo "document.getElementsByName('username')[0].value = '".htmlspecialchars($userData['username'], ENT_QUOTES, 'UTF-8')."';";
                            echo "document.getElementsByName('nome')[0].value = '".htmlspecialchars($userData['user'], ENT_QUOTES, 'UTF-8')."';";
                            echo "document.getElementsByName('email')[0].value = '".htmlspecialchars($userData['email'], ENT_QUOTES, 'UTF-8')."';";
                            echo "document.getElementById('profile').value = '".htmlspecialchars($userData['profile'], ENT_QUOTES, 'UTF-8')."';";
                            echo "</script>";
                        } else {
                            echo "Utilizador não encontrado.";
                        }

                    } catch (PDOException $e) {
                        echo "Erro DB: " . $e->getMessage();
                    }  
                } elseif ($action === 'save') {
                    // Processar os dados do formulário e atualizar o utilizador na base de dados
                    // (a implementação desta parte depende da estrutura da tabela e dos campos do formulário)
                    echo "Dados do formulário recebidos para salvar o utilizador: <br>";
                    echo "Username: " . htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') . "<br>";
                    echo "Nome: " . htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8') . "<br>";
                    echo "Email: " . htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') . "<br>";
                    echo "Perfil: " . htmlspecialchars($_POST['profile'], ENT_QUOTES, 'UTF-8') . "<br>";

                    try {
//  Converter e 'limpar' os dados recebidos do formulário
//  A limpesa também podia ser feita no Javascript, antes do envio do formulário, mas é boa prática fazê-la no servidor
//  -- operador 'null  coalescing' (??) evita aviso/erro por variáveis indefinidas/vazias
//  -- Trim remove espaços em branco no início e no fim --
                        $username = trim($_POST["username"] ?? "");
                        $nome     = trim($_POST["nome"] ?? "");
                        $email    = trim($_POST["email"] ?? "");
                        $profile  = trim($_POST["profile"] ?? "");

                        if ($username === "" || $nome === "" || $email === "" || $profile === "") {
                            die("Erro: campos obrigatórios em falta.");
                        }
//  Verificar se o novo email já existe na base de dados                    
                        $sql = "SELECT ufn_ExistsMail(:email, :username) AS emailExists";
                        $stmt = $pdo->prepare($sql);

                        $stmt->execute([
                            ":email" => $email,
                            ":username" => $username
                        ]);
                        
                        $result = $stmt->fetch();
                        if ($result && $result['emailExists'] == 1) {
                            die("Erro: O email '" . $email . "' já existe. Por favor escolha outro email.");
                        }

//  Atualizar o utilizador na base de dados usando uma consulta SQL UPDATE
                        $sql_update = "UPDATE utilizadores SET user = :nome, email = :email, profile = :profile WHERE username = :username";
                        $stmt = $pdo->prepare($sql_update);
                        
//  Associar os valores aos parâmetros e executar a instrução SQL
                        $stmt->execute([
                            ':nome' => $nome, 
                            ':email' => $email, 
                            ':profile' => $profile,
                            ':username' => $username
                        ]);
                        echo "Utilizador atualizado com sucesso.";
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