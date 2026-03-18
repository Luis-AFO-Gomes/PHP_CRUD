<?php

    function envOrFail(string $key): string {
        $value = getenv($key);
        if ($value === false || $value === '') {
            throw new RuntimeException("Missing environment variable: {$key}");
        }
        return $value;
    }

    // Variáveis para ligação à base de dados
    // alterar conforme a configuração do ambiente
    // em ambiente de produção, estas variáveis não devem estar em ficheiro de código nem sincronizadas com GIT, 
    // devem, pelo contrário, ser definidas como variáveis de ambiente no servidor onde a aplicação está a correr
    // Não é obrigatótio definir os parâmetros de ligação numa variável, podem ser inseridos directamente no DSN
    // mas é uma boa prática para facilitar a leitura e manutenção do código
    $host = envOrFail('DB_HOST');
    $port = envOrFail('DB_PORT');
    $db   = envOrFail('DB_NAME');
    $user = 'root';
    $pass = envOrFail('DB_ROOT_PASS');
	$charset = 'utf8mb4';

    
// Data Source Name (DSN) - string de ligação à base de dados
	$dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
//    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

	$options = [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	];

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
        $pdo = new PDO(
            $dsn,
            $user,
            $pass,
            $options
        );
    } catch (PDOException $e) {
        http_response_code(500);
        exit("Database connection failed.");
    }
