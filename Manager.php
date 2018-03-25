<?php


ob_start();
define('API_KEY',' '); // token your bot
function bot($method,$datas=[]){
    $url = 'https://api.telegram.org/bot'.API_KEY.'/'.$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}

$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$chat_id = $message->chat->id;
$text = $message->text;
$id = $message->from->id;
$reply = $message->reply_to_message;
$id_reply = $reply->from->id;
$msgid_pin = $reply->message_id;
$msgid = $message->message_id;
$admin = file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=$chat_id&user_id=$id");
$get_muted = file_get_contents('vev/muted.txt');
$muted = explode("\n", $get_muted);


//start code forward

$get_fwd = file_get_contents('vev/fwd.txt');
$fwd = explode("\n", $get_fwd);


if($message->chat->type == "supergroup"){
if($text == '/fwd' and strpos($admin, '"status":"member"') == FALSE and !in_array($chat_id, $fwd)){
    file_put_contents("vev/fwd.txt", "\n" . $chat_id,FILE_APPEND);
    bot('sendMessage',[
    'chat_id'=>$chat_id,
    'text'=>'*Done*',
    'parse_mode'=>"MarkDown",
    'reply_to_message_id'=>$msgid,
    ]);
}

if($text == '/fwd' and strpos($admin, '"status":"member"') == FALSE and in_array($chat_id, $fwd)){
    bot('sendMessage',[
    'chat_id'=>$chat_id,
    'text'=>'*Already locked*',
    'parse_mode'=>"MarkDown",
    'reply_to_message_id'=>$msgid,
    ]);
}


if($text == '/unfwd' and strpos($admin, '"status":"member"') == FALSE and in_array($chat_id, $fwd)){
$open = file_get_contents('vev/fwd.txt');
$str = str_replace($chat_id, '', $open);
file_put_contents("vev/fwd.txt", $str);
    bot('sendMessage',[
    'chat_id'=>$chat_id,
    'text'=>'*Done*',
    'parse_mode'=>"MarkDown",
    'reply_to_message_id'=>$msgid,
    ]);
}

if($text == '/unfwd' and strpos($admin, '"status":"member"') == FALSE and !in_array($chat_id, $fwd)){
    bot('sendMessage',[
    'chat_id'=>$chat_id,
    'text'=>'*Already unlocked*',
    'parse_mode'=>"MarkDown",
    'reply_to_message_id'=>$msgid,
    ]);
}


$get = file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=$chat_id&user_id=".$id);
$info = json_decode($get, true);
$rank = $info['result']['status'];

if($message->forward_from_chat->id and $rank == "member" and in_array($chat_id, $fwd)){
    bot('deleteMessage',[
    'chat_id'=>$chat_id,
    'message_id'=>$msgid,
    ]);
}


//start code link

$get_link= file_get_contents('vev/link.txt');
$link = explode("\n", $get_link);

if($text == '/link' and strpos($admin, '"status":"member"') == FALSE and !in_array($chat_id, $link)){
    file_put_contents("vev/link.txt", "\n" . $chat_id,FILE_APPEND);
    bot('sendMessage',[
    'chat_id'=>$chat_id,
    'text'=>'*Done*',
    'parse_mode'=>"MarkDown",
    'reply_to_message_id'=>$msgid,
    ]);
}

if($text == '/link' and strpos($admin, '"status":"member"') == FALSE and in_array($chat_id, $link)){
    bot('sendMessage',[
    'chat_id'=>$chat_id,
    'text'=>'*Already locked*',
    'parse_mode'=>"MarkDown",
    'reply_to_message_id'=>$msgid,
    ]);
}

if($text == '/unlink' and strpos($admin, '"status":"member"') == FALSE and in_array($chat_id, $link)){
$open = file_get_contents('vev/link.txt');
$str = str_replace($chat_id, '', $open);
file_put_contents("vev/link.txt", $str);
    bot('sendMessage',[
    'chat_id'=>$chat_id,
    'text'=>'*Done*',
    'parse_mode'=>"MarkDown",
    'reply_to_message_id'=>$msgid,
    ]);
}

if($text == '/unlink' and strpos($admin, '"status":"member"') == FALSE and !in_array($chat_id, $link)){
    bot('sendMessage',[
    'chat_id'=>$chat_id,
    'text'=>'*Already unlocked*',
    'parse_mode'=>"MarkDown",
    'reply_to_message_id'=>$msgid,
    ]);
}


$get = file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=$chat_id&user_id=".$id);
$info = json_decode($get, true);
$rank = $info['result']['status'];

if(preg_match('/t.me/',$text) and $rank == "member" and in_array($chat_id, $link)){
    bot('deleteMessage',[
    'chat_id'=>$chat_id,
    'message_id'=>$msgid,
    ]);
}

//start code kick

$reply = $message->reply_to_message;
$id_reply = $reply->from->id;

if($reply and $text == "/kick" or $text == "طرد" and strpos($admin, '"status":"member"') == FALSE){
    bot('kickChatMember',[
        'chat_id'=>$chat_id,
        'user_id'=>$id_reply,
    ]);
}

if($reply and $text == "/unkick" or $text == "الغاء الطرد" and strpos($admin, '"status":"member"') == FALSE){
    bot('unbanChatMember',[
        'chat_id'=>$chat_id,
        'user_id'=>$id_reply,
    ]);
}


//start code pin

$reply = $message->reply_to_message;
$msgid_pin = $reply->message_id;

if($reply and $text == "/pin" or $text == "تثبيت" and strpos($admin, '"status":"member"') == FALSE){
    bot('pinChatMessage',[
        'chat_id'=>$chat_id,
        'message_id'=>$msgid_pin,
    ]);
}

if($reply and $text == "/unpin" or $text == "الغاء التثبيت" and strpos($admin, '"status":"member"') == FALSE){
    bot('unpinChatMessage',[
        'chat_id'=>$chat_id,
        'message_id'=>$msgid_pin,
    ]);
}


// start muted

$get_mute = file_get_contents('vev/muted.txt');
$bots= explode("\n", $get_mute);

if($text == "/mute" and strpos($admin, '"status":"member"') == FALSE){
    bot('sendMessage',[
      'chat_id'=>$chat_id,
      'user_id'=>$message->reply_to_messsage->from->id,
      'text'=>"*Muted*",
      'parse_mode'=>"MarkDown",
      'disable_web_page_preview'=>true,
      'reply_to_message_id'=>$msgid,
]);
    bot('restrictChatMember',[
      'chat_id'=>$chat_id,
      'can_send_messages'=>false,
      'user_id'=>$id_reply,
    ]);
}

if($text == "/unmute" and strpos($admin, '"status":"member"') == FALSE){
    bot('sendMessage',[
      'chat_id'=>$chat_id,
      'user_id'=>$message->reply_to_messsage->from->id,
      'text'=>"*UnMuted*",
      'parse_mode'=>"MarkDown",
      'disable_web_page_preview'=>true,
      'reply_to_message_id'=>$msgid,
]);
    bot('restrictChatMember',[
      'chat_id'=>$chat_id,
      'user_id'=>$id_reply,
      'can_send_messages'=>true,
      'can_send_media_messages'=>true,
      'can_send_other_messages'=>true,
      'can_add_web_page_previews'=>true,
    ]);

}


if($text == '/cmd' and strpos($admin, '"status":"member"') == FALSE){
    bot('sendMessage',[
    'chat_id'=>$chat_id,
    'text'=>' Orders Admins :-

Closed Forward
/fwd : locked
/unfwd : unlocked

Closed links
/link : locked
/unlink  : unlocked

kick User
/kick : kicked
/unkick  : unkicked

pinned Message
/pin : pinned
/unpin : unpinned

Muted User
/mute : muted
/unmute : unmuted
' ,
    'reply_to_message_id'=>$msgid,
    ]);

    }
   }

