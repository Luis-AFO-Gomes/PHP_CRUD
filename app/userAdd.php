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
	require_once __DIR__ . '/config.php';
?> 
<html>
    <head>
	    <title>Exemplo Crud em PHP: Inserir Utilizador</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="style/style.css" type="text/css">
	
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

//  Verificar preenchimento do campo PASSWORD
//  Altera-se formato e alerta-se o utilizador se o campo não estiver preenchido
				    if (verPreenche(pass)==false){		
                        pass.focus();
                        document.getElementById('frmPass').style.color='red';
                        txtStatus=txtStatus + "Password nao esta preenchido \n ";
                        valido=false;
                    } else {
                        document.getElementById('frmPass').style.color='blue';
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


                }

                if (valido==false){
                    alert ("Verifique preenchimento do formulario \n "+txtStatus+" \n *Campos de Preenchimento obrigatorio","erro de preenchimento");
    			}
			return valido;
		}
	</script>
	</head>

	<body>
        <h1>Inserir um novo utilizador na base de dados.</h1><br><br>
        <?php
            if(isset($_POST["username"])){
                printf("<br>A Inserir o utilizador ",$_POST['username']);

//  Ligação à base de dados, igual ao exemplo de listar utilizadores (index.php)
//                $host = 'php_crud-mysql-1';
//                $db   = 'php_crud';
//                $user = 'root';
//                $pass = 'my5@fEp@s5';
//                $charset = 'utf8mb4';

//                $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
//                $options = [
//                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
//                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
//                ];

                try {
//                    $pdo = new PDO($dsn, $user, $pass, $options);

//  Converter e 'limpar' os dados recebidos do formulário
//  A limpesa também podia ser feita no Javascript, antes do envio do formulário, mas é boa prática fazê-la no servidor
//  -- operador 'null  coalescing' (??) evita aviso/erro por variáveis indefinidas/vazias
//  -- Trim remove espaços em branco no início e no fim --
                    $username = trim($_POST["username"] ?? "");
                    $pass     = $_POST["pass"] ?? "";   // não se faz trim() porque pode haver espaços na password...
                    $nome     = trim($_POST["nome"] ?? "");
                    $email    = trim($_POST["email"] ?? "");

//  Verifica se os campos obrigatórios estão preenchidos
//  Tal como acima, a verificação no Javascript não elimina a necessidade de verificação no servidor
                    if ($username === "" || $pass === "" || $nome === "" || $email === "") {
                        die("Erro: campos obrigatórios em falta.");
                    }
                    $sql = "SELECT ufn_ExistsUser(:username) AS userExists";
                    $stmt = $pdo->prepare($sql);

//  Associar os valores aos parâmetros e executar a instrução
                    $stmt->execute([
                        ":username" => $username
                    ]);
                    
                    $result = $stmt->fetch();
                    if ($result && $result['userExists'] == 1) {
                        die("Erro: O nome de utilizador '" . $username . "' já existe. Por favor escolha outro nome de utilizador.");
                    }

//  Encriptar a password - HASH - por segurança
//  A password nunca deve ser escrita ou comunicada em texto livre
                    $passHash = password_hash($pass, PASSWORD_DEFAULT);

//  Preparar a instruçao de SQL para inserção na base de dados
//  Utilizar "instruções preparadas" significa escrever código com parametros - variáveis precedidas de ':' - 
//  em vez de valores diretos
//  Além de ser uma boa-prática para facilitar leitura e manutenção do código
//  O uso de "instruções preparadas" também é uma questão de segurança pois previne ataques de SQL Injection
                    $sql = "INSERT INTO utilizadores (username, password, user, email)
                            VALUES (:username, :password, :user, :email)";
                    $stmt = $pdo->prepare($sql);

//  Associar os valores aos parâmetros e executar a instrução
                    $stmt->execute([
                        ":username" => $username,
                        ":password" => $passHash,
                        ":user"     => $nome,
                        ":email"    => $email,
                    ]);

                    echo "<br>Utilizador criado com sucesso: utilizador = '" .$username."'";

                } catch (PDOException $e) {
                    echo "Erro DB: " . $e->getMessage();
                }

                
            } else {
            ?>

                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return validarForm(this);" method="post">
                    <table align="center">
                        <tr id="frmUserName">
                            <td align="right" width="150">Nome de utilizador: </td>
                            <td align="left"><input type="text" name="username" size="40"></td>
                            <td align="left" width="25">*</td>
                        </tr>
                        <tr id="frmPass" >
                            <td align="right">Password: </td>
                            <td align="left"><input type="password" name="pass" size="40"></td>
                            <td align="left" width="25">*</td>
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