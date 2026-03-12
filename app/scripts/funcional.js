function verPreenche(campo){
//  função para verificar se um dado campo de preenchimento obrigatorio está preenchido
    if (campo.value==null||campo.value==""){
//		alert("vazio");
        return false;
    }
//  verifica-se se o campo está vazio ou se tem uma string nula
    else{
//		alert("preenchido");
        return true;
    }
}
	
function validarMail(endereco){
//  função para validar o formato do email
    var posAt=endereco.value.indexOf("@");
    var posPonto=endereco.value.lastIndexOf(".");
    if (posAt<1||posPonto-posAt<2){
        return false;
        }
    else{
        return true;
        }
}

function validarForm(formulario){
// função para validar se o formulário de login    
    var txtOutput="";
    var valido=true;
    var txtStatus="";
    if (verPreenche(formulario.nome)==false){
//o campo do nome não está preenchido, alerta-se o utilizador				
        formulario.nome.focus();
        document.getElementById('frmNome').style.color='red';
        txtStatus=txtStatus + "Nome nao esta preenchido \n ";
        valido=false;
    } else {
//o campo do nome está preenchido, repoem-se o formato original
        document.getElementById('frmNome').style.color='blue';
    }

    if (verPreenche(formulario.pass)==false){
//o campo da password não está preenchido, alerta-se o utilizador				
        formulario.pass.focus();
        document.getElementById('frmPass').style.color='red';
        txtStatus=txtStatus + "Password nao esta preenchido \n ";
        valido=false;
    } else {
//o campo da password está preenchido, repoem-se o formato original
        document.getElementById('frmPass').style.color='blue';
    }

    if (valido==false){
        alert ("Verifique preenchimento do formulario \n "+txtStatus+" \n *Campos de Preenchimento obrigatorio","erro de preenchimento");
//  Linha abaixo apresenta o erro na própria página, em vez de numa janela de alerta
//  É útil ter esta opção presente pois alguns browsers bloqueiam janelas de alerta, evitando que a mesma seja apreentada ao utilizador
//  Para usar esta opção, basta descomentar a linha abaixo e comentar a linha do alert() acima        
//          document.getElementById('status').innerHTML="<strong>Verifique preenchimento do formulario</strong><br>"+txtStatus+"*Campos de Preenchimento obrigatorio";
    }
    return valido;
}