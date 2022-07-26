<?php

$confibot = json_decode(file_get_contents('./resource/conf.json') , true);

function clientes($message){
	$chat_id = $message["chat"]["id"];
	$from_id = $message["from"]["id"];

	$text = strtolower($message['text']);
	preg_match_all('/[a-z-A-Z-0-9]*+/', $text, $args);
	$args = array_values(array_filter($args[0]));
	$cmd = $args[0];

	atualizasaldo($chat_id);

	if ($cmd == 'start'){

		$nome = $message['from']['first_name'];
		$idwel = $message['from']['id'];
		$conf = json_decode(file_get_contents("./resource/conf.json") , true);

		if ($conf['welcome'] != ""){
			$txt = $conf["welcome"];

			$txt = str_replace("{nome}", $nome, $txt);
			$txt = str_replace("{id}", $idwel, $txt);

		}else{
			$txt = "*Ola $nome , User meus comandos Abaixo para Interagir comigo !*";
		}
		

		$menu =  ['inline_keyboard' => [
		   [['text'=>'iaee','callback_data'=>"fazendo oque aqui?"]]
		    ,]];
		
 
$botoes[] = ['text'=>"INICIAR APOSTAS",'callback_data'=>"loja"]; 

$botoes[] = ['text'=>"",'callback_data'=>"valores"];

$botoes[] = ['text'=>"ESCALAÇÃO DOS TIMES",'callback_data'=>"menu_infos"]; 

$botoes[] = ['text'=>"",'callback_data'=>"ajuda"]; 

$botoes[] = ['text'=>"💠 PIX",'callback_data'=>"pix"]; 

$botoes[] = ['text'=>"🛠️ Criador do BOT",'callback_data'=>"dev_info"]; 

$botoes[] = ['text'=>"💡 Suporte",'url'=>"https://t.me/rskorges"]; 
 
 $menu['inline_keyboard'] = array_chunk($botoes, 2); 
 
 $txt ="*Seja bem vindo(a) $nome*";
 
		bot("sendMessage",array("chat_id"=> $chat_id , "text" => $txt,"reply_markup" =>$menu,"reply_to_message_id"=> $message['message_id'],"parse_mode" => 'Markdown'));
	 
	}
	
	if (preg_match("/[0-9-A-Z]{10}/", $message['text'],$cod)){

		usagift($message,$cod[0]);
		die();
	}
		 
	}




function query($msg){
	

	$idquery = $msg['id'];
	$idfrom = $msg['from']['id'];
	$message = $msg['message'];
	$dataquery = $msg['data'];

	$userid = $msg['from']['id'];
	$userid2 = $msg['message']['reply_to_message']['from']['id'];
	$chatid = $msg['message']['chat']['id'];

	if ($userid != $userid2){
		bot("answerCallbackQuery",array("callback_query_id" => $idquery , "text" => "Sem permissao !","show_alert"=>false,"cache_time" => 10));
		die();
	}

	if (explode("_", $dataquery)[0] == "volta"){
		$cmd = explode("_", $dataquery)[1];
		$cmd($message);

	}else if (explode("_", $dataquery)[0] == "compracc"){
		$cmd = explode("_", $dataquery)[0];
		$cmd($message,$msg,explode("_", $dataquery)[1]);
	}else if (explode("_", $dataquery)[0] == "altersaldoe"){
		$cmd = explode("_", $dataquery)[0];
		$cmd($message,$msg,explode("_", $dataquery)[1],explode("_", $dataquery)[2]);

	}else if (explode("_", $dataquery)[0] == "users"){
		$cmd = explode("_", $dataquery)[0];
		$cmd($message,$msg,explode("_", $dataquery)[1],explode("_", $dataquery)[2]);
	}else if (explode("_", $dataquery)[0] == "viewcard"){
		$cmd = explode("_", $dataquery)[0];
		$cmd($message,$msg,explode("_", $dataquery)[1],explode("_", $dataquery)[2]);
	}else if (explode("_", $dataquery)[0] == "compraccs"){
		$cmd = explode("_", $dataquery)[0];
		
		$cmd($message,$msg,explode("_", $dataquery)[1],explode("_", $dataquery)[2],explode("_", $dataquery)[3]);
	}else if (explode("_", $dataquery)[0] == "envia"){
		$cmd = explode("_", $dataquery)[0];
		
		$cmd($message,$msg,explode("_", $dataquery)[1] , explode("_", $dataquery)[2] , explode("_", $dataquery)[3] );

	}else{
		$dataquery($message);
	}
}




/*alter user*/


function users($message , $query , $type , $position){


	
	$chat_id = $message["chat"]["id"];
	$idquery = $query['id'];


	$users = json_decode(file_get_contents("./usuarios.json"),true);

	
	$chunk = array_chunk($users, 10);

	$tt = sizeof($chunk);


	if ($type == "prox"){
		if ($chunk[ $position + 1]){
			
			$postio4n = $position +1;
		}else{
			die(bot("answerCallbackQuery",array("callback_query_id" => $idquery , "text" => "Fim!","show_alert"=> false,"cache_time" => 10)));
		}
	}else{
		if ($chunk[ $position - 1]){
			
			$postio4n = $position  - 1;

		}else{
			die(bot("answerCallbackQuery",array("callback_query_id" => $idquery , "text" => "Fim!","show_alert"=> false,"cache_time" => 10)));
		}
	}

	$userss = $chunk[$postio4n];

	$indexs = array_chunk(array_keys($users), 10)[$postio4n];

	$t = sizeof($users);

	$d = $postio4n +1;

	$txt .= "<b>🚀 Lista de Usuários do Bot $t</b>\n";
	foreach ($userss as $iduser => $value44) {

		$idcarteira = $indexs[$iduser];

		$nome = ($value44['nome'])? $value44['nome'] : "Sem Nome";

		$nome = str_replace(["</>" ], "", $nome);
		$saldo = ($value44['saldo']) ? $value44['saldo'] : 0;

		$dadta = (date("d/m/Y H:s:i" , $value44['cadastro']))? date("d/m/Y H:s:i" , $value44['cadastro']) : "Sem Data";

		$txt .= "\n🧰<b>Id da carteira:</b> {$idcarteira}\n";
		$txt .= "💎<b>Nome: </b>{$nome}\n";
		$txt .= "💰<b>Aposta: </b> {$saldo}\n";
		$txt .= "📅<b>Data Cadastro: </b> {$dadta}\n";

	}

	$menu =  ['inline_keyboard' => [

	[
		['text'=>"<<",'callback_data'=>"users_ant_{$postio4n}"] , ['text'=>">>",'callback_data'=>"users_prox_{$postio4n}"]
	] ,[
		['text'=>"🔙Volta",'callback_data'=>"menu"]
	]

	,]];

	bot("editMessageText",array( "message_id" => $message['message_id'] , "chat_id"=> $chat_id , "text" => $txt,"reply_to_message_id"=> $message['message_id'],  "parse_mode" => "html","reply_markup" =>$menu));




}


/*

envia msg para os users 

*/

function envia($message , $query , $opt , $postion ){
	$chat_id = $message["chat"]["id"];
	$dados = json_decode(file_get_contents("./usuarios.json") , true);
	$idquery = $query['id'];
	$msg = file_get_contents("./msgs.txt");

	$t = sizeof(array_chunk(array_keys($dados), 50));

	$json = array_chunk(array_keys($dados), 50)[$postion];
	if (!array_chunk(array_keys($dados), 50)[$postion]){
		die(bot("answerCallbackQuery",array("callback_query_id" => $idquery , "text" => "Todos os usuarios ja receberam a msg !!!","show_alert"=> false,"cache_time" => 10)));
	}

	$tenviados = 0;
	$tnenviados = 0;
	$usersdell = [];

	$nenv = $postion +1;

	foreach ($json as $value) {

		$sendmessage = bot("sendMessage" , array("chat_id" => $value , "text" => $msg , "parse_mode" => "Markdown" ));

		if (!$sendmessage){
			
			if ($opt == "sim" || $opt == 'sim'){
				delluser($value);
				$usersdell[] = $value;
			}
			$tnenviados++;
		}else{
			$tenviados++;
		}

	}

	$usersap = implode(",", $usersdell);

	$txt .= "<b>✨ Enviando .. !</b>\n\n";
	$txt .= "<b>📩 Msg: {$msg}</b>\n\n";
	$txt .= "<b>🔝 Enviado {$nenv} de {$t} !</b>\n";
	$txt .= "<b>✅ Enviados: {$tenviados}!</b>\n";
	$txt .= "<b>❌ Nao Enviados: {$tnenviados} !</b>\n";
	$txt .= "<b>🗑 Users Apagados: {$usersap}!</b>\n";

	$postio4n = $position++;

	$menu =  ['inline_keyboard' => [
		[ 
			['text'=>"continua",'callback_data'=>"envia_{$opt}_{$postio4n}"]
		]
	,]];

	bot("editMessageText",array( "message_id" => $message['message_id'] , "chat_id"=> $chat_id , "text" => $txt,"reply_to_message_id"=> $message['message_id'],"parse_mode" => 'html',"reply_markup" =>$menu));
}



/*

	perfil usuario !

*/


function menu_infos($message){
	$chat_id = $message["chat"]["id"];

	$historicocc = json_decode(file_get_contents("./ccsompradas.json") , true);
	$dados = json_decode(file_get_contents("./usuarios.json") , true);
	$historicosaldo = json_decode(file_get_contents("./salcocomprado.json") , true);
	$r = "R$";
	$conf = json_decode(file_get_contents("./resource/conf.json") , true);
$totalccs = (sizeof($historicocc[$chat_id]['ccs'])) ? sizeof($historicocc[$chat_id]['ccs']) : 0 ;
	$cliente = $dados[$chat_id];
	$menu =  ['inline_keyboard' => [
		   [['text'=>'iaee','callback_data'=>"fazendo oque aqui?"]]
		    ,]];

	$botoes[] = ['text'=>" Voltar",'callback_data'=>"volta_menu"];
	$menu['inline_keyboard'] = array_chunk($botoes, 2);

	$txt = "*VOCÊ ACABOU DE APOSTAR!

⥬ ID: *`$chat_id`

⥬ *Aposta:* `$r{$cliente[saldo]}`

⥬ *Retornos:* `$totalccs`";
		bot("editMessageText",array("message_id" => $message['message_id'] ,"chat_id"=> $chat_id , "text" => $txt,"reply_markup" =>$menu,"reply_to_message_id"=> $message['message_id'],"parse_mode" => 'Markdown'));

}


function dev_info($message){
	$chat_id = $message["chat"]["id"];
	$conf = json_decode(file_get_contents("./resource/conf.json") , true);

	$menu =  ['inline_keyboard' => [
		   [['text'=>'iaee','callback_data'=>"fazendo oque aqui?"]]
		    ,]];
	$botoes[] = ['text'=>"⚙️ DEV",'url'=>"https://t.me/rskorges"];
	$botoes[] = ['text'=>" Voltar",'callback_data'=>"volta_menu"];
	$menu['inline_keyboard'] = array_chunk($botoes, 2);
	
	$msg_dev = "{$conf[mensagem]}";

	$txt = "⚙️ | Versão do bot: *{$conf[versao]}*

➤ Ultima atualização: *{$conf[updateDAte]}*

➤ Linguagem: *Php*

➤ Atualizações da versão

➤* $msg_dev*

*❗️ | Não faço parte do suporte nem atendimento

👨🏽‍💻 | by: @rskorges*";
	bot("editMessageText",array( "message_id" => $message['message_id'] , "chat_id"=> $chat_id , "text" => $txt,"reply_to_message_id"=> $message['message_id'],"parse_mode" => 'Markdown',"reply_markup" =>$menu));
}


function usagift($message, $cod){
	
	$chat_id = $message["chat"]["id"];

	$gifts = json_decode(file_get_contents("./gifts.json") , true);
	$users = json_decode(file_get_contents("./usuarios.json") , true);
	$saldocomprado = json_decode(file_get_contents("./salcocomprado.json") , true);

	$menu =  ['inline_keyboard' => [[['text'=>"🔚 Volta",'callback_data'=>"volta_loja"]],]];

	if (!$gifts[$cod]){
		die(bot("sendMessage" , array("chat_id" => $chat_id , "text" => "*Ops, Este codigo nao foi encontrado ! , tente novamente*" , "reply_to_message_id" => $message['message_id'],"parse_mode" => "Markdown")));
	}
	
	$dg = $gifts[$cod];
	$valor = $dg['valor'];

	if ($dg['used'] == "true"){
		die(bot("sendMessage" , array("chat_id" => $chat_id , "text" => "*Desculpe , mas este codigo ja foi usado !*" , "reply_to_message_id" => $message['message_id'],"parse_mode" => "Markdown")));
	}

	// $date = strtotime("now");
	$date = strtotime("+1 week");
	$date1 = strtotime("now");

	$users[$chat_id]['saldo'] = $users[$chat_id]['saldo'] + $valor;
	$users[$chat_id]['dataLimite'] = $date;

	$saldocomprado[$chat_id][] = array("valor" => $valor , "datelimite" => $date , "date" => $date1 , "codigo" => $cod );

	$dsalva = json_encode($users,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT );
	$salva = file_put_contents('./usuarios.json', $dsalva);

	if ($salva){
		$gifts[$cod]['used'] = "true";
		$gifts[$cod]['cliente'] = $chat_id;

		$dsalva = json_encode($gifts,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT );
		$salva = file_put_contents('./gifts.json', $dsalva);
		// atualiza o historico de compradas 
		$dsalva2 = json_encode($saldocomprado,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT );
		$salva = file_put_contents('./salcocomprado.json', $dsalva2);

	$menu =  ['inline_keyboard' => [
		   [['text'=>'iaee','callback_data'=>"fazendo oque aqui?"]]
		    ,]];
		$botoes[] = ['text'=>"Menu",'callback_data'=>"volta_menu"];

	$menu['inline_keyboard'] = array_chunk($botoes, 1);
	atualizasaldo($chat_id);
	$clientes = json_decode(file_get_contents("./usuarios.json") , true);
$saldo = $clientes[$chat_id]['saldo'];
 
$txt .= "\n*🎫 | Gift resgatado*
O seu gift de *R$$valor,00 *foi *Resgatado*.
*Agora você possui:* *R$$saldo,00* de *Saldo*.";

		bot("sendMessage" , array("chat_id" => $chat_id , "text" => $txt , "reply_markup"=> $menu,"reply_to_message_id" => $message['message_id'],"parse_mode" => "Markdown"));
	}else{
		die(bot("sendMessage" , array("chat_id" => $chat_id , "text" => "*Desculpe , Ocorreu um erro interno !*" , "reply_to_message_id" => $message['message_id'],"parse_mode" => "Markdown")));
	}

}


/*
	pix
*/

function pix($message){
	$chat_id = $message["chat"]["id"];
	$confibot = $GLOBALS[confibot];

	$txt = "💠 ESCOLHA ABAIXO UMA CHAVE 💠\nVocê deve realizar o pagamento para o {$confibot[userDono]}\n\n";
	$txt .= "*CHAVE ALEATORIA*:\n🔑<b>123456789</b>\n👤<b>NOME COMPLETO</b>\n<b>R$</b> {$saldo}";
	$txt .= "💎Apos a *confirmacao do pagamento* vc ira receber um codigo, *me envie que irei colocar na lista o seu nome completo*😉\n\n";
	$txt .= "⚠️*Confirme se as informações estão corretas!";

	$menu =  ['inline_keyboard' => [
		[['text'=>"🔚 Volta",'callback_data'=>"volta_loja"]]
	,]];

	bot("editMessageText",array( "message_id" => $message['message_id'] , "chat_id"=> $chat_id , "text" => $txt,"reply_to_message_id"=> $message['message_id'],  "parse_mode" => 'MarkdownV2',"reply_markup" =>$menu));
}



function compraccs($message , $query , $level , $idcc , $band){

	$confibot = $GLOBALS[confibot];
	$chat_id = $message["chat"]["id"];
	$nome = $message['reply_to_message']['from']['first_name'];
	$idquery = $query['id'];

	$clientes = json_decode(file_get_contents("./usuarios.json") , true);
	$conf = json_decode(file_get_contents("./resource/conf.json") , true);

	if (!$clientes[$chat_id]){
		die(bot("answerCallbackQuery",array("callback_query_id" => $idquery , "text" => "usuario sem registro , manda /start para se registra!!","show_alert"=> true,"cache_time" => 10)));
	}

	$user = $clientes[$chat_id];
	$country = $user['country'];
	$saldo = $clientes[$chat_id]['saldo'];
	$ccs = json_decode(file_get_contents("./ccs/{$country}/{$level}.json") , true);
	$valor = ($conf['price'][$level] ? $conf['price'][$level] : $conf['price']['default']);

	if ($user['saldo'] == 0){
		die(bot("answerCallbackQuery",array("callback_query_id" => $idquery , "text" => "Vc nao tem saldo suficiente para realiza esta compra!\nCompre saldo com o {$confibot[userDono]}!","show_alert"=> true,"cache_time" => 10)));
	} 

	if ($valor > $user['saldo']){
		die(bot("answerCallbackQuery",array("callback_query_id" => $idquery , "text" => "Vc nao tem saldo suficiente para realiza esta compra!\nCompre saldo com o {$confibot[userDono]}!","show_alert"=> true,"cache_time" => 10)));
	} 
	
	foreach ($ccs as $key => $dadoscc) {
		if ($key == $idcc){
			
			break;
		}
	}

	if (removesaldo($chat_id , $valor)){
		
		deletecc($chat_id , $idcc , $level);
		salvacompra($dadoscc , $chat_id , "ccs");

		bot("answerCallbackQuery",array("callback_query_id" => $idquery , "text" => "Foi Descontado $valor do seu saldo!! ","show_alert"=> false,"cache_time" => 10));

		$clientes = json_decode(file_get_contents("./usuarios.json") , true);
		$saldo = $clientes[$chat_id]['saldo'];

		$result = json_decode(bot("getMe" , array("vel" => "")) , true);
		$userbot = $result['result']['username'];

		$txt .= "✨*Detalhes da cc*\n";
		$txt .= "*💳cc: *_".$dadoscc['cc']."_\n";
		$txt .= "📆*mes / ano: *_" . $dadoscc['mes'] .'/'.$dadoscc['ano'] ."_\n";
		$txt .= "🔐*cvv: *_{$dadoscc[cvv]}_\n";
		$txt .= "🏳️*bandeira:* _$dadoscc[bandeira]_\n";
		$txt .= "💠*nivel:* _$dadoscc[nivel]_\n";
		$txt .= "⚜️*tipo:* _$dadoscc[tipo]_\n";
		$txt .= "🏛*banco:* _$dadoscc[banco]_\n";
		$txt .= "🌍*pais:* _$dadoscc[pais]_\n";
		$txt .= "⚠️*Seu saldo:* _{$saldo}_\n";

		$menu =  ['inline_keyboard' => [

			[["text" => "🔄 Compra novamente" , "url" => "http://telegram.me/$userbot?start=iae"]]

		,]];

		bot("editMessageText",array( "message_id" => $message['message_id'] , "chat_id"=> $chat_id , "text" => $txt,"reply_markup" =>$menu,"reply_to_message_id"=> $message['message_id'],"parse_mode" => 'Markdown'));

		die;
	}else{
		bot("answerCallbackQuery",array("callback_query_id" => $idquery , "text" => "Foi Descontado $valor do seu saldo!! ","show_alert"=> false,"cache_time" => 10));
		die;
	}

}

function altercard($message , $query , $type , $position , $level , $band){

	$confibot = $GLOBALS[confibot];


	$chat_id = $message["chat"]["id"];
	$nome = $message['reply_to_message']['from']['first_name'];
	$idquery = $query['id'];

	$clientes = json_decode(file_get_contents("./usuarios.json") , true);
	$conf = json_decode(file_get_contents("./resource/conf.json") , true);


	
	if (!$clientes[$chat_id]){
		die(bot("answerCallbackQuery",array("callback_query_id" => $idquery , "text" => "usuario sem registro , manda /start para se registra!!","show_alert"=> true,"cache_time" => 10)));
	}

	$user = $clientes[$chat_id];
	$country = $user['country'];
	$saldo = $clientes[$chat_id]['saldo'];
	$ccs = json_decode(file_get_contents("./ccs/{$country}/{$level}.json") , true);

	$cclista = []; 

	$buttons = [];

	foreach ($ccs as $key => $value) {
		if ($value['bandeira'] == $band){
			$value['idcc'] = $key;
			$cclista[] = $value;
		}
	}

	if ($type == "prox"){
		
		if ($cclista[ $position +1]){
			$postio4n = $position +1;
		}else{
			die(bot("answerCallbackQuery",array("callback_query_id" => $idquery , "text" => "Não a próxima cc!","show_alert"=> false,"cache_time" => 10)));
		}

	}else{

		if ($cclista[ $position -1]){
			$postio4n = $position -1;
		}else{
			die(bot("answerCallbackQuery",array("callback_query_id" => $idquery , "text" => "Não a cc anterio!","show_alert"=> false,"cache_time" => 10)));
		}
	}

	$valor = ($conf['price'][$level] ? $conf['price'][$level] : $conf['price']['default']);

	$dadoscc = $cclista[$postio4n];
	$t = $postio4n +1;
	$txt = "🔎*Mostrando {$t} de ".sizeof($cclista)."*\n\n";
	$txt .= "✨*Detalhes da cc*\n";
	$bin = substr($dadoscc['cc'], 0,6);
	$txt .= "*💳cc: *_".$bin.'xxxxxxxxx'."_\n";
	$txt .= "📆*mes / ano: *_" . $dadoscc['mes'] .'/'.$dadoscc['ano'] ."_\n";
	$txt .= "🔐*cvv: *_xxx_\n";
	$txt .= "🏳️*bandeira:* _$dadoscc[bandeira]_\n";
	$txt .= "💠*nivel:* _$dadoscc[nivel]_\n";
	$txt .= "⚜️*tipo:* _$dadoscc[tipo]_\n";
	$txt .= "🏛*banco:* _$dadoscc[banco]_\n";
	$txt .= "🌍*pais:* _$dadoscc[pais]_\n";
	$txt .="\n💰*Valor*: $valor (saldo)\n";
	$txt .= "⚠️*Seu saldo:* _{$saldo}_\n";
	$menu =  ['inline_keyboard' => [

		[["text" => "💰Compra💰" , "callback_data" => "compraccs_{$level}_{$dadoscc[idcc]}_{$band}"]],
		[["text" => "<<" , "callback_data" => "altercard_ant_{$postio4n}_{$level}_{$band}"] , ["text" => ">>" , "callback_data" => "altercard_prox_{$postio4n}_{$level}_{$band}"]],
		[['text'=>"🔚 Volta",'callback_data'=>"ccun"]]

	,]];


	bot("editMessageText",array( "message_id" => $message['message_id'] , "chat_id"=> $chat_id , "text" => $txt,"reply_markup" =>$menu,"reply_to_message_id"=> $message['message_id'],"parse_mode" => 'Markdown'));


}

function viewcard($message , $query , $band , $level){

	$confibot = $GLOBALS[confibot];


	$chat_id = $message["chat"]["id"];
	$nome = $message['reply_to_message']['from']['first_name'];
	$idquery = $query['id'];

	$clientes = json_decode(file_get_contents("./usuarios.json") , true);
	$conf = json_decode(file_get_contents("./resource/conf.json") , true);

	
	if (!$clientes[$chat_id]){
		die(bot("answerCallbackQuery",array("callback_query_id" => $idquery , "text" => "usuario sem registro , manda /start para se registra!!","show_alert"=> true,"cache_time" => 10)));
	}

	$user = $clientes[$chat_id];
	$country = $user['country'];
	$saldo = $clientes[$chat_id]['saldo'];

	$ccs = json_decode(file_get_contents("./ccs/{$country}/{$level}.json") , true);

	$cclista = []; 

	$buttons = [];

	foreach ($ccs as $key => $value) {
		if ($value['bandeira'] == $band){
			$value['idcc'] = $key;
			$cclista[] = $value;
		}
	}


	$valor = ($conf['price'][$level] ? $conf['price'][$level] : $conf['price']['default']);

	$dadoscc = $cclista[0];
	$txt = "🔎*Mostrando 1 de ".sizeof($cclista)."*\n\n";
	$txt .= "✨*Detalhes da cc*\n";
	$bin = substr($dadoscc['cc'], 0,6);
	$txt .= "*💳cc: *_".$bin.'xxxxxxxxx'."_\n";
	$txt .= "📆*mes / ano: *_" . $dadoscc['mes'] .'/'.$dadoscc['ano'] ."_\n";
	$txt .= "🔐*cvv: *_xxx_\n";
	$txt .= "🏳️*bandeira:* _$dadoscc[bandeira]_\n";
	$txt .= "💠*nivel:* _$dadoscc[nivel]_\n";
	$txt .= "⚜️*tipo:* _$dadoscc[tipo]_\n";
	$txt .= "🏛*banco:* _$dadoscc[banco]_\n";
	$txt .= "🌍*pais:* _$dadoscc[pais]_\n";
	$txt .="\n💰*Valor*: $valor (saldo)\n";
	$txt .= "⚠️*Seu saldo:* _{$saldo}_\n";
	$menu =  ['inline_keyboard' => [

		[["text" => "💰Compra💰" , "callback_data" => "compraccs_{$level}_{$dadoscc[idcc]}_{$band}"]],
		[["text" => "<<" , "callback_data" => "altercard_ant_0_{$level}_{$band}"] , ["text" => ">>" , "callback_data" => "altercard_prox_0_{$level}_{$band}"]],
		[['text'=>"🔚 Volta",'callback_data'=>"ccun"]]

	,]];


	bot("editMessageText",array( "message_id" => $message['message_id'] , "chat_id"=> $chat_id , "text" => $txt,"reply_markup" =>$menu,"reply_to_message_id"=> $message['message_id'],"parse_mode" => 'Markdown'));
}

function compracc($message,$query,$level){

	$confibot = $GLOBALS[confibot];


	$chat_id = $message["chat"]["id"];
	$nome = $message['reply_to_message']['from']['first_name'];
	$idquery = $query['id'];

	$clientes = json_decode(file_get_contents("./usuarios.json") , true);
	$conf = json_decode(file_get_contents("./resource/conf.json") , true);

	$menu =  ['inline_keyboard' => [

		[]

	,]];

	$menu['inline_keyboard'][] = [['text'=>"🔚 Volta",'callback_data'=>"volta_loja"]];

	if (!$clientes[$chat_id]){
		die(bot("answerCallbackQuery",array("callback_query_id" => $idquery , "text" => "usuario sem registro , manda /start para se registra!!","show_alert"=> true,"cache_time" => 10)));
	}

	$user = $clientes[$chat_id];
	$country = $user['country'];
	
	$ccs = json_decode(file_get_contents("./ccs/{$country}/{$level}.json") , true);

	$band = [];
	$buttons = [];

	foreach ($ccs as $key => $value) {
		if (!in_array($value['bandeira'], $band)){
			$band[] = $value['bandeira'];
			$buttons[] = ["text" => $value['bandeira'] , "callback_data" => 'viewcard_'.$value['bandeira'].'_'.$level];
		}
	}

	
	$menu['inline_keyboard'] = array_chunk($buttons , 2);

	$menu['inline_keyboard'][] = [['text'=>"🔚 Volta",'callback_data'=>"ccun"]];

	$txt = "\n*✅nivel:* _{$level}_\n*💳 Escolha a bandeira:*";

	bot("editMessageText",array( "message_id" => $message['message_id'] , "chat_id"=> $chat_id , "text" => $txt,"reply_markup" =>$menu,"reply_to_message_id"=> $message['message_id'],"parse_mode" => 'Markdown'));

		
}


/*

	function loja 
	exibir menu loja virtual

*/
function loja($message){
	$chat_id = $message["chat"]["id"];
	$nome = $message['reply_to_message']['from']['first_name'];
	$menu =  ['inline_keyboard' => [

		[['text'=>"🔚 Volta",'callback_data'=>"volta_menu"]]

		,
	]];
	$dados = json_decode(file_get_contents("./usuarios.json") , true);
	$r = "R$";
	$cliente = $dados[$chat_id];

		$b[] = ['text'=>"✪ Consul Unitárias",'callback_data'=>"ccun"];

        $b[] = ['text'=>"⎚ Mix",'callback_data'=>"mixconsul"];

        $b[] = ['text'=>"✪ Unitárias CCs",'callback_data'=>"error"];
        
		$b[] = ['text'=>"",'callback_data'=>"nada"];
        
		$b[] = ['text'=>"Voltar",'callback_data'=>"volta_menu"];
        
        
	
	$menu['inline_keyboard'] = array_chunk($b, 2); 
	
	bot("editMessageText",array( "message_id" => $message['message_id'] , "chat_id"=> $chat_id , "text" => "*Escolha uma partida e deixe seu palpite!*

⥬ _Acerte o placar

⥬ SALDO: *$r{$cliente[saldo]}*","reply_markup" =>$menu,"reply_to_message_id"=> $message['message_id'],"parse_mode" => 'Markdown'));
}

function error($message, $query){
	$chat_id = $message["chat"]["id"];
    $idquery = $query['id'];
    bot("answerCallbackQuery",array("callback_query_id" => $idquery , "text" => "Você não possuí permissão !","show_alert"=>false,"cache_time" => 10));
}

function menu($message){
	$chat_id = $message["chat"]["id"];
	$nome = $message['reply_to_message']['from']['first_name'];
	$idwel = $message['reply_to_message']['from']['id'];
	$conf = json_decode(file_get_contents("./resource/conf.json") , true);

	if ($conf['welcome'] != ""){
		$txt = $conf["welcome"];

		$txt = str_replace("{nome}", $nome, $txt);
		$txt = str_replace("{id}", $idwel, $txt);

	}else{
		$txt = "*Ola $nome , User meus comandos Abaixo para Interagir comigo !*";
	}


	$menu =  ['inline_keyboard' => [
		   [['text'=>'iaee','callback_data'=>"fazendo oque aqui?"]]
		    ,]];
	

$botoes[] = ['text'=>"INICIAR APOSTAS",'callback_data'=>"loja"]; 

$botoes[] = ['text'=>"",'callback_data'=>"valores"];

$botoes[] = ['text'=>"ESCALAÇÃO DOS TIMES",'callback_data'=>"menu_infos"]; 

$botoes[] = ['text'=>"",'callback_data'=>"ajuda"]; 

$botoes[] = ['text'=>"💠 PIX",'callback_data'=>"pix"]; 

$botoes[] = ['text'=>"🛠️ Criador do BOT",'callback_data'=>"dev_info"]; 

$botoes[] = ['text'=>"💡 Suporte",'url'=>"https://t.me/rskorges"]; 
 
 $menu['inline_keyboard'] = array_chunk($botoes, 2); 
 
 $txt ="*Seja bem vindo(a) $nome*";
 
	bot("editMessageText",array( "message_id" => $message['message_id'] , "chat_id"=> $chat_id , "text" => $txt,"reply_markup" =>$menu,"reply_to_message_id"=> $message['message_id'],"parse_mode" => 'Markdown'));
	
}


/*

	function ccn 
	exibir menu ccs 

*/


function ccun($message){
	$chat_id = $message["chat"]["id"];
	$nome = $message['reply_to_message']['from']['first_name'];


	$menu =  ['inline_keyboard' => [[], ]];

	$openprice = json_decode(file_get_contents("./resource/conf.json") , true);

	$users = json_decode(file_get_contents("./usuarios.json") , true);



	$ccs = [];
	$dir = './ccs/';

	$itens = scandir($dir);
	
	if ($itens !== false) { 
		foreach ($itens as $item) { 
			$ccs[] =  explode(".", $item)[0];
		}
	}

	$levels = array_values(array_filter($ccs));


	$butoes = [];

	if (count($levels) == 0){
		$confibot = $GLOBALS[confibot];
		$menu['inline_keyboard'][] = [['text'=>"🔚 Volta",'callback_data'=>"volta_loja"]];
		bot("editMessageText",array( "message_id" => $message['message_id'] , "chat_id"=> $chat_id , "text" => "*Ops, Estou sem estoque no momento , duvidas chame o* {$confibot[userDono]}","reply_markup" =>$menu,"reply_to_message_id"=> $message['message_id'],"parse_mode" => 'Markdown'));
		die();
	}


	foreach ($levels as $value) {
		
		$butoes[] = ['text'=> "$value",'callback_data'=>"compracc_{$value}"];
		
	}
	$butoes[] = ['text'=>"🔚 Volta",'callback_data'=>"volta_loja"];
	$butoes[] = ['text'=>"🌎 Altera pais",'callback_data'=>"selectbase"];

	$menu['inline_keyboard'] = array_chunk($butoes , 2);

	$confibot = $GLOBALS[confibot];

	bot("editMessageText",array( "message_id" => $message['message_id'] , "chat_id"=> $chat_id , "text" => "*💳Escolha o nivel:*","reply_markup" =>$menu,"reply_to_message_id"=> $message['message_id'],"parse_mode" => 'Markdown'));
}
