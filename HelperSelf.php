<?php
/*

📌 کانال ایلیا سورس
برای دریافت سورس های بیشتر به کانال ما سر بزنید :)
@Source_Eliya

*/
include "config.php";
include "jdf.php";
ob_start();

//=====================================================
#Config
define('API_KEY', $bot_token);
$admin = $admin_id;
$weburl = $web_url;

//=====================================================
$update = json_decode(file_get_contents('php://input'));
$message = $update->message ?? null;
$chat_id = $message->chat->id ?? null;
$message_id = $message->message_id ?? null;
$from_id = $message->from->id ?? null;
$fromid = $update->callback_query->from->id ?? null;
$firstname = $update->callback_query->from->first_name ?? '';
$textmessage = $message->text ?? '';
$inline = $update->inline_query->query ?? '';
$chatsid = $update->callback_query->from->id ?? null;
$data = $update->callback_query->data ?? '';
$inline_message_id = $update->callback_query->inline_message_id ?? '';
$re_id = $update->message->reply_to_message->from->id ?? null;
$rt = $update->message->reply_to_message ?? null;
$replyid = $update->message->reply_to_message->message_id ?? null;
$edit = $update->edited_message->text ?? '';
$message_edit_id = $update->edited_message->message_id ?? null;
$chat_edit_id = $update->edited_message->chat->id ?? null;
$edit_for_id = $update->edited_message->from->id ?? null;

$database = json_decode(file_get_contents($data_file), true) ?? [];
$config_data = json_decode(file_get_contents($config_file), true) ?? [];

$first_name = $message->from->first_name ?? '';

// بررسی عضویت در کانال
$forchannel = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=$channel_username&user_id=".$from_id));
$tch = $forchannel->result->status ?? 'left';

//=====================================================
#Function
function bot($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        error_log(curl_error($ch));
        return null;
    }else{
        return json_decode($res);
    }
}

function SendMessage($chat_id, $text){
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>$text,
        'parse_mode'=>'MarkDown'
    ]);
}

//=====================================================
#Code

// بررسی وضعیت Turnbot
$Turnbot = $config_data['Turnbot'] ?? 1;
$inlin = '';

// مدیریت اینلاین کوئری
if (strpos($inline,'panel_') !== false) {
    $inlin = str_replace("panel_","",$inline);
    
    if ($Turnbot == 1) {
        bot("answerInlineQuery",[
            "inline_query_id"=>$update->inline_query->id,
            "results"=>json_encode([[
                "type"=>"article",
                "id"=>base64_encode(rand(5,555)),
                "title"=>"تنظیمات سلف",
                "input_message_content"=>["parse_mode"=>"MarkDown","message_text"=>"پنل مدیریت ربات سلف"],
                "reply_markup"=>["inline_keyboard"=>[
                    [["text"=>"• Rᴏʙᴏᴛ sᴛᴀᴛᴜs :","callback_data"=>"text_$inlin"],
                    ["text"=>"✓","callback_data"=>"turnoff_$inlin"]],
                    [["text"=>"• ʀᴏʙᴏᴛ sᴇᴛᴛɪɴɢs","callback_data"=>"settings_$inlin"],
                    ["text"=>"• ʀᴏʙᴏᴛ ɢᴜɪᴅᴇ","callback_data"=>"help_$inlin"]],
                    [["text"=>"• sᴇʀᴠᴇʀ ɪɴғᴏʀᴍᴀᴛɪᴏɴ","callback_data"=>"upping_$inlin"],
                    ["text"=>"• ᴅᴀᴛᴇ ᴀɴᴅ ᴛɪᴍᴇ","callback_data"=>"uptime_$inlin"]],
                    [["text"=>"• ʟɪsᴛ ᴏғ ᴇɴᴇᴍɪᴇs","callback_data"=>"enemy_$inlin"],
                    ["text"=>"• ʟɪsᴛ ᴏғ ᴀɴsᴡᴇʀs","callback_data"=>"answer_$inlin"]],
                ]]
            ]])
        ]);
    } else {
        bot("answerInlineQuery",[
            "inline_query_id"=>$update->inline_query->id,
            "results"=>json_encode([[
                "type"=>"article",
                "id"=>base64_encode(rand(5,555)),
                "title"=>"• Self Settings",
                "input_message_content"=>["parse_mode"=>"MarkDown","message_text"=>"• sᴇʟғ ʀᴏʙᴏᴛ ᴍᴀɴᴀɢᴇᴍᴇɴᴛ ᴘᴀɴᴇʟ"],
                "reply_markup"=>["inline_keyboard"=>[
                    [["text"=>"✘","callback_data"=>"turnon_$inlin"],
                    ["text"=>"• ʀᴏʙᴏᴛ sᴛᴀᴛᴜs","callback_data"=>"text_$inlin"]],
                ]]
            ]])
        ]);
    }
}

// بررسی عضویت در کانال
if ($tch != "creator" && $tch != "administrator" && $tch != "member" && $textmessage == "/start"){
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text'=>"سلام خدمت شما کاربر گرامی $first_name
اول در کانال های سازنده سلف Version 1 ما عضو شوید سپس بر روی دکمه استارت کلیک کنید🌹
ایدی کانال ها:
@Source_Eliya 

 عضو شوید تا بتوانید از ربات استفاده کنید.
⚠️ بعد از عضو شدن بزن رو /start",
        'reply_markup'=>json_encode([
            'remove_keyboard'=>true
        ]),
        'parse_mode'=>'HTML'
    ]);
    exit();
}

// دستور /start
if($textmessage == "/start"){
    if ($from_id == $admin) {
        bot('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"✓ شما ادمین ربات هستین",
            'parse_mode'=>'MarkDown'
        ]);
    }else{
        bot('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"✘ شما ادمین ربات نیستین",
            'parse_mode'=>'MarkDown'
        ]);
    }
}

// مدیریت callback ها
if (!empty($data) && $fromid == $admin) {
    // خروج از پنل
    if($data == "exit_"){
        bot("answerCallbackQuery",[
            "callback_query_id"=>$update->callback_query->id,
            "text"=>"صبر کنید"
        ]);
        
        if ($Turnbot == 1) {
            bot("editMessageText", [
                "chat_id" => $chatsid,
                "message_id" => $update->callback_query->message->message_id,
                "text"=>"• sᴇʟғ ʀᴏʙᴏᴛ ᴍᴀɴᴀɢᴇᴍᴇɴᴛ ᴘᴀɴᴇʟ",
                'parse_mode'=>"html",
                "reply_markup"=>json_encode([
                    "inline_keyboard"=>[
                        [["text"=>"• Rᴏʙᴏᴛ sᴛᴀᴛᴜs :","callback_data"=>"text_$inlin"],
                        ["text"=>"✓","callback_data"=>"turnoff_$inlin"]],
                        [["text"=>"• ʀᴏʙᴏᴛ sᴇᴛᴛɪɴɢs","callback_data"=>"settings_$inlin"],
                        ["text"=>"• ʀᴏʙᴏᴛ ɢᴜɪᴅᴇ","callback_data"=>"help_$inlin"]],
                        [["text"=>"• sᴇʀᴠᴇʀ ɪɴғᴏʀᴍᴀᴛɪᴏɴ","callback_data"=>"upping_$inlin"],
                        ["text"=>"• ᴅᴀᴛᴇ ᴀɴᴅ ᴛɪᴍᴇ","callback_data"=>"uptime_$inlin"]],
                        [["text"=>"• ʟɪsᴛ ᴏғ ᴇɴᴇᴍɪᴇs","callback_data"=>"enemy_$inlin"],
                        ["text"=>"• ʟɪsᴛ ᴏғ ᴀɴsᴡᴇʀs","callback_data"=>"answer_$inlin"]],
                    ]
                ])
            ]);
        } else {
            bot("editMessageText", [
                "chat_id" => $chatsid,
                "message_id" => $update->callback_query->message->message_id,
                "text"=>"• sᴇʟғ ʀᴏʙᴏᴛ ᴍᴀɴᴀɢᴇᴍᴇɴᴛ ᴘᴀɴᴇʟ",
                'parse_mode'=>"html",
                "reply_markup"=>json_encode([
                    "inline_keyboard"=>[
                        [["text"=>"✘","callback_data"=>"turnon_$inlin"],
                        ["text"=>"• ʀᴏʙᴏᴛ sᴛᴀᴛᴜs","callback_data"=>"text_$inlin"]],
                    ]
                ])
            ]);
        }
    }
    
    // روشن کردن ربات
    elseif($data == "turnon_"){
        $config_data['Turnbot'] = 1;
        file_put_contents($config_file, json_encode($config_data));
        
        bot("editMessage�ᴀɴsᴡᴇʀ + Tᴇxᴛ
➲ Cʟᴇᴀɴ Aɴsᴡᴇʀs
➲ Aɴsᴡᴇʀʟɪsᴛ

#Eɴᴇᴍʏ :

➲ Eɴᴇᴍʏ ᴏɴ
➲ Eɴᴇᴍʏ ᴏғғ
➲ Sᴇᴛᴇɴᴇᴍʏ | UsᴇʀIᴅ ᴏʀ Rᴇᴘʟʏ
➲ Dᴇʟᴇɴᴇᴍʏ | UsᴇʀIᴅ ᴏʀ Rᴇᴘʟʏ
➲ Cʟᴇᴀɴᴇɴᴇᴍʏʟɪsᴛ
➲ Eɴᴇᴍʏʟɪsᴛ
➲ Nᴜᴍʙᴇʀ

#SᴜᴘᴇʀGʀᴏᴜᴘ :

➲ Cʟᴇᴀɴ +(1-1000)
➲ Dᴇʟ + Rᴇᴘʟʏ
➲ Bᴀɴ + ʀᴇᴘʟᴀʏ
➲ Tʀᴀɴsʟᴀᴛᴇ Rᴇᴘʟʏ+ғᴀ|ᴇɴ|ᴀʀ SᴜᴘᴇʀGʀᴏᴜᴘ
➲ Pɪɴ + ʀᴇᴘʟʏ
➲ Uɴᴘɪɴ

#Usᴇʀ :

➲ Rᴇᴍ (Rᴇᴘʟʏ) (JᴜsᴛPᴠ)
➲ ɪᴅ (Rᴇᴘʟʏ)
➲ Wᴇʙʜᴏᴏᴋ + ᴛᴏᴋᴇɴ + ᴀᴅᴅʀᴇs
➲ Mᴇ
➲ Pʀᴏғɪʟᴇ + Fɪʀsᴛɴᴀᴍᴇ | ʟᴀsᴛNᴀᴍᴇ | ᴛᴇxᴛʙɪᴏ
➲ Sᴇᴛᴜsᴇʀɴᴀᴍᴇ + Tᴇxᴛ
➲ Mᴀʀᴋʀᴇᴀᴅ ᴏɴ|ᴏғғ
➲ Tʏᴘɪɴɢ + ᴏɴ|ᴏғғ
➲ Pᴏᴋᴇʀ  + ᴏɴ|ᴏғғ
➲ Sᴛᴀᴛs
➲ Bʟᴏᴄᴋ + Usᴇʀɴᴀᴍᴇ
➲ Uɴʙʟᴏᴄᴋ + Usᴇʀɴᴀᴍᴇ
➲ Sᴇssɪᴏɴs
➲ Sᴜᴘ + ᴛᴇxᴛ

#Oᴛʜᴇʀ :

➲ Lɪᴋᴇ + Tᴇxᴛ
➲ ᴄᴏɴᴅɪᴛɪᴏɴ
➲ ʟᴇғᴛ
➲ Sᴀᴠᴇ
➲ Sᴘᴀᴍ + متن + تعداد
➲ Bʟᴜᴇ + اسم شما
➲ Hɪᴅᴅᴇɴ پیام خصوصی + ایدی عددی کاربر
➲ Sʜᴏʀᴛ + لینک شما
➲ Aᴘᴋ + اسم برنامه
➲ ᴄᴀʟᴄ عدد +یا- عدد
➲ Sᴛɪᴄᴋᴇʀ + متن
➲ Jᴏᴋᴇ
➲ Gᴏᴏɢʟᴇ + متنی ک میخای سرچ شه
➲ Gɪғ + موضوع گیف
➲ Pɪᴄ + موضوع عکس
➲ Vᴏɪᴄᴇ + متن ویس
",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "exit_$inlin"]],
]
])
]);
}

if($data == "enemy_" && $fromid == $admin){
$enemy = file_get_contents("$weburl/enemy.txt");
bot("editmessagetext", [
"text"=>"
■□■□■□■□■
enemy List: \n
$enemy 
■□■□■□■□■
",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "🗑 حذف لیست دشمنان", "callback_data" => "cleanenemy_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "exit_$inlin"]],
]
])
]);
}
if($data == "cleanenemy_" && $fromid == $admin){
	unlink("enemy.txt");
$enemy = file_get_contents("$weburl/enemy.txt");
bot("editmessagetext", [
"text"=>"
■□■□■□■□■
enemy List:  \n
$enemy 
■□■□■□■□■
",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "🗑 حذف لیست دشمنان", "callback_data" => "cleanenemy_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "exit_$inlin"]],
]
])
]);
}
if($data == "answer_" && $fromid == $admin){
if (count($data['answering']) > 0) {
    $txxxt = "Answer List: 
";
    $counter = 1;
    foreach ($data['answering'] as $k => $ans) {
        $txxxt .= "$counter: $k => $ans \n";
        $counter++;
    }
bot("editmessagetext", [
"text"=>"
• $txxxt
",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "exit_$inlin"]],
]
])
]);
}
}
if($data == "settings_" && $fromid == $admin){
bot("editmessagetext", [
"text"=>"به پنل تنظیمات سلف Version 1 خوش امدی 
 کاربر : [$firstname](tg://user?id=$fromid)",
'parse_mode'=>'MarkDown',
"inline_message_id"=>$inline_message_id,
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text"=>"• ᴏɴʟɪɴᴇ","callback_data"=>"online_$inlin"],
["text"=>"• ᴛʏᴘɪɴɢ","callback_data"=>"typing_$inlin"],
["text"=>"• ᴘᴏᴋᴇʀ","callback_data"=>"pooker_$inlin"]],
[["text"=>"• ᴍᴀʀᴋʀᴇᴀᴅ","callback_data"=>"mrkread_$inlin"],
["text"=>"• ᴇɴᴇᴍʏ","callback_data"=>"enemye_$inlin"]],
[["text"=>"• ᴛɪᴍᴇ ᴏɴ ɴᴀᴍᴇ","callback_data"=>"timename_$inlin"],
["text"=>"• ᴛɪᴍᴇ ᴏɴ ʙɪᴏ","callback_data"=>"timebio_$inlin"]],
[["text"=>"• ᴛɪᴍᴇ ᴏɴ ᴘʀᴏғɪʟᴇ","callback_data"=>"Timeprofile_$inlin"]],
[["text"=>"🔘 ʙᴀᴄᴋ","callback_data"=>"exit_$inlin"]],
]
])
]);
}
#typing
if ((int)json_decode(file_get_contents('Config.json'))->Typing == 1) {
if($data == "typing_" && $fromid == $admin){
bot("editmessagetext", [
"text"=>"• به تنظیمات تایپینگ خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✓", "callback_data" => "typingoff_$inlin"],
["text" => "Lock status :", "callback_data" => "text_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
}
if ((int)json_decode(file_get_contents('Config.json'))->Typing == 0) {
if($data == "typing_" && $fromid == $admin){
bot("editmessagetext", [
"text"=>"• به تنظیمات تایپینگ خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✘", "callback_data" => "typingon_$inlin"],
["text" => "Lock status :", "callback_data" => "text_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
}
if($data == "typingon_" && $fromid == $admin){
$Conf->Typing= 1;
file_put_contents('Config.json', json_encode($Conf));
bot("answerCallbackQuery",[
"callback_query_id"=>$update->callback_query->id,
"text"=>"تایپینگ روشن شد ✓"
]);
bot("editmessagetext", [
"text"=>"• به تنظیمات تایپینگ خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✓", "callback_data" => "typingoff_$inlin"],["text" => "Lock status :", "callback_data" => "typingon_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
if($data == "typingoff_" && $fromid == $admin){
$Conf->Typing  = 0;
file_put_contents('Config.json', json_encode($Conf));
bot("answerCallbackQuery",[
"callback_query_id"=>$update->callback_query->id,
"text"=>"تایپینگ خاموش شد ✘"
]);
bot("editmessagetext", [
"text"=>"• به تنظیمات تایپینگ خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✘", "callback_data" => "typingon_$inlin"],
["text" => "Lock status :", "callback_data" => "typingon_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
#Online
if ((int)json_decode(file_get_contents('Config.json'))->Online == 1) {
if($data == "online_" && $fromid == $admin){
bot("editmessagetext", [
"text"=>"• به تنظیمات انلاینر خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✓", "callback_data" => "onlineoff_$inlin"],
["text" => "Lock status :", "callback_data" => "text_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
}
if ((int)json_decode(file_get_contents('Config.json'))->Online == 0) {
if($data == "online_" && $fromid == $admin){
bot("editmessagetext", [
"text"=>"• به تنظیمات انلاینر خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✘", "callback_data" => "onlineoff_$inlin"],
["text" => "Lock status :", "callback_data" => "text_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
}
if($data == "onlineon_" && $fromid == $admin){
$Conf->Online= 1;
file_put_contents('Config.json', json_encode($Conf));
bot("answerCallbackQuery",[
"callback_query_id"=>$update->callback_query->id,
"text"=>"انلاینر روشن شد ✓"
]);
bot("editmessagetext", [
"text"=>"• به تنظیمات انلاینر خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✓", "callback_data" => "onlineoff_$inlin"],
["text" => "Lock status :", "callback_data" => "typingon_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}

if($data == "onlineoff_" && $fromid == $admin){
$Conf->Online  = 0;
file_put_contents('Config.json', json_encode($Conf));
bot("answerCallbackQuery",[
"callback_query_id"=>$update->callback_query->id,
"text"=>"انلاینر خاموش شد ✘"
]);
bot("editmessagetext", [
"text"=>"• به تنظیمات انلاینر خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✘", "callback_data" => "onlineon_$inlin"],
["text" => "Lock status :", "callback_data" => "typingon_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
#Enemy
if ((int)json_decode(file_get_contents('Config.json'))->Enemy == 1) {
if($data == "enemye_" && $fromid == $admin){
bot("editmessagetext", [
"text"=>"• به تنظیمات دشمن خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✓", "callback_data" => "enemyeoff_$inlin"],
["text" => "Lock status :", "callback_data" => "text_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
}
if ((int)json_decode(file_get_contents('Config.json'))->Enemy == 0) {
if($data == "enemye_" && $fromid == $admin){
bot("editmessagetext", [
"text"=>"• به تنظیمات دشمن خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✘", "callback_data" => "enemyeon_$inlin"],
["text" => "Lock status :", "callback_data" => "text_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
}
if($data == "enemyeon_" && $fromid == $admin){
$Conf->Enemy= 1;
file_put_contents('Config.json', json_encode($Conf));
bot("answerCallbackQuery",[
"callback_query_id"=>$update->callback_query->id,
"text"=>"دشمن روشن شد ✓"
]);
bot("editmessagetext", [
"text"=>"• به تنظیمات دشمن خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✓", "callback_data" => "enemyeoff_$inlin"],
["text" => "Lock status :", "callback_data" => "typingon_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}

if($data == "enemyeoff_" && $fromid == $admin){
$Conf->Enemy  = 0;
file_put_contents('Config.json', json_encode($Conf));
bot("answerCallbackQuery",[
"callback_query_id"=>$update->callback_query->id,
"text"=>"دشمن خاموش شد ✘"
]);
bot("editmessagetext", [
"text"=>"• به تنظیمات دشمن خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✘", "callback_data" => "enemyeon_$inlin"],
["text" => "Lock status :", "callback_data" => "typingon_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
#MarkRead
if ((int)json_decode(file_get_contents('Config.json'))->Markread == 1) {
if($data == "mrkread_" && $fromid == $admin){
bot("editmessagetext", [
"text"=>"• به تنظیمات مشاهده خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✓", "callback_data" => "mrkreadoff_$inlin"],
["text" => "Lock status :", "callback_data" => "text_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
}
if ((int)json_decode(file_get_contents('Config.json'))->Markread == 0) {
if($data == "mrkread_" && $fromid == $admin){
bot("editmessagetext", [
"text"=>"• به تنظیمات مشاهده خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✘", "callback_data" => "mrkreadon_$inlin"],
["text" => "Lock status :", "callback_data" => "text_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
}
if($data == "mrkreadon_" && $fromid == $admin){
$Conf->Markread= 1;
file_put_contents('Config.json', json_encode($Conf));
bot("answerCallbackQuery",[
"callback_query_id"=>$update->callback_query->id,
"text"=>"خواندن روشن شد ✓"
]);
bot("editmessagetext", [
"text"=>"• به تنظیمات مشاهده خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✓", "callback_data" => "mrkreadoff_$inlin"],
["text" => "Lock status :", "callback_data" => "typingon_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}

if($data == "mrkreadoff_" && $fromid == $admin){
$Conf->Markread  = 0;
file_put_contents('Config.json', json_encode($Conf));
bot("answerCallbackQuery",[
"callback_query_id"=>$update->callback_query->id,
"text"=>"خواندن خاموش شد ✘"
]);
bot("editmessagetext", [
"text"=>"• به تنظیمات مشاهده خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✘", "callback_data" => "mrkreadon_$inlin"],
["text" => "Lock status :", "callback_data" => "typingon_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
#Poker
if ((int)json_decode(file_get_contents('Config.json'))->Poker == 1) {
if($data == "pooker_" && $fromid == $admin){
bot("editmessagetext", [
"text"=>"• به تنظیمات پوکر خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✓", "callback_data" => "pookeroff_$inlin"],
["text" => "Lock status :", "callback_data" => "text_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
}
if ((int)json_decode(file_get_contents('Config.json'))->Poker == 0) {
if($data == "pooker_" && $fromid == $admin){
bot("editmessagetext", [
"text"=>"• به تنظیمات پوکر خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✘", "callback_data" => "pookeron_$inlin"],
["text" => "Lock status :", "callback_data" => "text_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
}
if($data == "pookeron_" && $fromid == $admin){
$Conf->Poker= 1;
file_put_contents('Config.json', json_encode($Conf));
bot("answerCallbackQuery",[
"callback_query_id"=>$update->callback_query->id,
"text"=>"پوکر روشن شد ✓"
]);
bot("editmessagetext", [
"text"=>"• به تنظیمات پوکر خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✓", "callback_data" => "pookeroff_$inlin"],["text" => "Lock status :", "callback_data" => "typingon_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}

if($data == "pookeroff_" && $fromid == $admin){
$Conf->Poker  = 0;
file_put_contents('Config.json', json_encode($Conf));
bot("answerCallbackQuery",[
"callback_query_id"=>$update->callback_query->id,
"text"=>"پوکر خاموش شد ✘"
]);
bot("editmessagetext", [
"text"=>"• به تنظیمات پوکر خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✘", "callback_data" => "pookeron_$inlin"],
["text" => "Lock status :", "callback_data" => "typingon_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
#Time Profile
if ((int)json_decode(file_get_contents('Config.json'))->Timeprofile == 1) {
if($data == "timeprofile_" && $fromid == $admin){
bot("editmessagetext", [
"text"=>"• به تنظیمات تایم رو پروفایل خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✓", "callback_data" => "timeprofileoff_$inlin"],["text" => "Lock status :", "callback_data" => "text_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
}
if ((int)json_decode(file_get_contents('Config.json'))->Timeprofile == 0) {
if($data == "timeprofile_" && $fromid == $admin){
bot("editmessagetext", [
"text"=>"• به تنظیمات تایم رو پروفایل خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✘", "callback_data" => "timeprofileon_$inlin"],
["text" => "Lock status :", "callback_data" => "text_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
}
if($data == "timeprofileon_" && $fromid == $admin){
$Conf->Timeprofile= 1;
file_put_contents('Config.json', json_encode($Conf));
bot("editmessagetext", [
"text"=>"• به تنظیمات تایم رو پروفایل خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✓", "callback_data" => "timeprofileoff_$inlin"],["text" => "Lock status :", "callback_data" => "typingon_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
if($data == "timeprofileoff_" && $fromid == $admin){
$Conf->Timeprofile  = 0;
file_put_contents('Config.json', json_encode($Conf));
bot("editmessagetext", [
"text"=>"• به تنظیمات تایم رو پروفایل خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✘", "callback_data" => "timeprofileon_$inlin"],
["text" => "Lock status :", "callback_data" => "typingon_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
#Time Name
if ((int)json_decode(file_get_contents('Config.json'))->Timename== 1) {
if($data == "timename_" && $fromid == $admin){
bot("editmessagetext", [
"text"=>"• به تنظیمات تایم رو نام خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✓", "callback_data" => "timenameon_$inlin"],
["text" => "Lock status :", "callback_data" => "text_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
}
if ((int)json_decode(file_get_contents('Config.json'))->Timename == 0) {
if($data == "timename_" && $fromid == $admin){
bot("editmessagetext", [
"text"=>"• به تنظیمات تایم رو نام خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "Lock status :", "callback_data" => "text_$inlin"],
["text" => "✘", "callback_data" => "timenameon_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
}
if($data == "timenameon_" && $fromid == $admin){
$Conf->Timename= 1;
file_put_contents('Config.json', json_encode($Conf));
bot("editmessagetext", [
"text"=>"• به تنظیمات تایم رو نام خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✓", "callback_data" => "timenameoff_$inlin"],
["text" => "Lock status :", "callback_data" => "typingon_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
if($data == "timenameoff_" && $fromid == $admin){
$Conf->Timename  = 0;
file_put_contents('Config.json', json_encode($Conf));
bot("editmessagetext", [
"text"=>"• به تنظیمات تایم رو نام خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✘", "callback_data" => "timenameon_$inlin"],
["text" => "Lock status :", "callback_data" => "typingon_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
#Time Bio
if ((int)json_decode(file_get_contents('Config.json'))->Timebio== 1) {
if($data == "timebio_" && $fromid == $admin){
bot("editmessagetext", [
"text"=>"• به تنظیمات تایم رو بیو خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✓", "callback_data" => "timebiooff_$inlin"],
["text" => "Lock status :", "callback_data" => "text_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
}
if ((int)json_decode(file_get_contents('Config.json'))->Timebio == 0) {
if($data == "timebio_" && $fromid == $admin){
bot("editmessagetext", [
"text"=>"• به تنظیمات تایم رو بیو خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✘", "callback_data" => "timebioon_$inlin"],
["text" => "Lock status :", "callback_data" => "text_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
}
if($data == "timebioon_" && $fromid == $admin){
$Conf->Timebio= 1;
file_put_contents('Config.json', json_encode($Conf));
bot("editmessagetext", [
"text"=>"• به تنظیمات تایم رو بیو خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✓", "callback_data" => "timebiooff_$inlin"],
["text" => "Lock status :", "callback_data" => "typingon_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
if($data == "timebiooff_" && $fromid == $admin){
$Conf->Timebio  = 0;
file_put_contents('Config.json', json_encode($Conf));
bot("editmessagetext", [
"text"=>"• به تنظیمات تایم رو بیو خوش امدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[["text" => "✘", "callback_data" => "timebioon_$inlin"],
["text" => "Lock status :", "callback_data" => "typingon_$inlin"]],
[["text" => "🔘 ʙᴀᴄᴋ", "callback_data" => "settings_$inlin"]],
]
])
]);
}
#Inline_Time
if(strpos($inline,'time') !== false) {
	$day_number = jdate('j');
$month_number = jdate('n');
$year_number = jdate('y');
$day_name = jdate('l');
$times = jdate("H:i:s");
$datses=jdate('r');
$eid=jdate('z');
$montines=jdate('F');
$slaehe=jdate('o');
$dasisisites=jdate('e');
$dasisisitesss=jdate('q');
$dasisisitesssss=jdate('f');
$dasisisitessssssss=jdate('k');
$dasisisitesssssssssss=jdate('Q');
$inlin = str_replace("panel_","",$inline);
bot("answerInlineQuery",[
"inline_query_id"=>$update->inline_query->id,
"results"=>json_encode([[
"type"=>"article",
"id"=>base64_encode(rand(5,555)),
"title"=>"• تاریخ و زمان سلف Version 1",
"input_message_content"=>["parse_mode"=>"MarkDown","message_text"=>"
به تاریخ و ساعت سلف Version 1 خوش اومدید
"],
"reply_markup"=>["inline_keyboard"=>[
[['text'=>"$dasisisites",'callback_data'=>'text']],
  [['text'=>"$year_number/$month_number/$day_number",'callback_data'=>'text'],
['text'=>"• تاریخ:",'callback_data'=>'text']],
[['text'=>"$times",'callback_data'=>'text'],
['text'=>"• ساعت:",'callback_data'=>'text']],
[['text'=>"$day_name",'callback_data'=>'text'],
['text'=>"• اسم روز:",'callback_data'=>'text']],
[['text'=>"$montines",'callback_data'=>'text'],
['text'=>"• اسم ماه:",'callback_data'=>'text']],
[['text'=>"$slaehe",'callback_data'=>'text'],
['text'=>"• سال:",'callback_data'=>'text']],
[['text'=>"$dasisisitesss",'callback_data'=>'text'],
['text'=>"• اسم حیوان سال:",'callback_data'=>'text']],
[['text'=>"$dasisisitesssss",'callback_data'=>'text'],
['text'=>"• اسم فصل:",'callback_data'=>'text']],
[['text'=>"%$dasisisitessssssss",'callback_data'=>'text'],
['text'=>"• درصد مونده به عید:",'callback_data'=>'text']],
[['text'=>"$dasisisitesssssssssss",'callback_data'=>'text'],
['text'=>"• روز های مونده به عید:",'callback_data'=>'text']],
[['text'=>"$datses",'callback_data'=>'text']],
]]
]])
]);
}
if($data == "uptime_" && $fromid == $admin){
	$day_number = jdate('j');
$month_number = jdate('n');
$year_number = jdate('y');
$day_name = jdate('l');
$times = jdate("H:i:s");
$datses=jdate('r');
$eid=jdate('z');
$montines=jdate('F');
$slaehe=jdate('o');
$dasisisites=jdate('e');
$dasisisitesss=jdate('q');
$dasisisitesssss=jdate('f');
$dasisisitessssssss=jdate('k');
$dasisisitesssssssssss=jdate('Q');
bot("editmessagetext", [
"text"=>"به تاریخ و ساعت سلف Version 1 خوش اومدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
[['text'=>"$dasisisites",'callback_data'=>'text']],
  [['text'=>"$year_number/$month_number/$day_number",'callback_data'=>'text'],
['text'=>"• تاریخ:",'callback_data'=>'text']],
[['text'=>"$times",'callback_data'=>'text'],
['text'=>"• ساعت:",'callback_data'=>'text']],
[['text'=>"$day_name",'callback_data'=>'text'],
['text'=>"• اسم روز:",'callback_data'=>'text']],
[['text'=>"$montines",'callback_data'=>'text'],
['text'=>"• اسم ماه:",'callback_data'=>'text']],
[['text'=>"$slaehe",'callback_data'=>'text'],
['text'=>"• سال:",'callback_data'=>'text']],
[['text'=>"$dasisisitesss",'callback_data'=>'text'],
['text'=>"• اسم حیوان سال:",'callback_data'=>'text']],
[['text'=>"$dasisisitesssss",'callback_data'=>'text'],
['text'=>"• اسم فصل:",'callback_data'=>'text']],
[['text'=>"%$dasisisitessssssss",'callback_data'=>'text'],
['text'=>"• درصد مونده به عید:",'callback_data'=>'text']],
[['text'=>"$dasisisitesssssssssss",'callback_data'=>'text'],
['text'=>"• روز های مونده به عید:",'callback_data'=>'text']],
[['text'=>"$datses",'callback_data'=>'text']],
[["text"=>"🔘 ʙᴀᴄᴋ","callback_data"=>"exit_$inlin"]],
]
])
]);
}
#Inline_Ping
if(strpos($inline,'ping_') !== false) {
$load = sys_getloadavg();
$mem = memory_get_usage();
$ver = phpversion();
$load = sys_getloadavg();
$mem = memory_get_usage();
$ver = phpversion();
if (strpos(PHP_OS, 'L') !== false or strpos(PHP_OS, 'l') !== false) {
 $a = file_get_contents("/proc/meminfo");
 $b = explode('MemTotal:', $a)[1];
 $stats = explode(' kB', $b)[0] / 1024 / 1024;
if ($stats != 0) {
 $mem_total = $stats . 'GB';
} else { 
 $mem_total = 'No Access';
}
} else {
 $mem_total = '!';
}
$inlin = str_replace("ping","",$inline);
bot("answerInlineQuery",[
"inline_query_id"=>$update->inline_query->id,
"results"=>json_encode([[
"type"=>"article",
"id"=>base64_encode(rand(5,555)),
"title"=>"• sᴇʀᴠᴇʀ ɪɴғᴏʀᴍᴀᴛɪᴏɴ",
"input_message_content"=>["parse_mode"=>"MarkDown","message_text"=>"
به  پینگ سلف Version 1 خوش اومدید
"],
"reply_markup"=>["inline_keyboard"=>[
   [['text'=>"• sᴇʀᴠᴇʀ ɪɴғᴏʀᴍᴀᴛɪᴏɴ",'callback_data'=>'text']],
[['text'=>"$load[0] M.s",'callback_data'=>'text'],
['text'=>"• سرعت تبادل داده:",'callback_data'=>'text']],
[['text'=>"$ver",'callback_data'=>'text'],
['text'=>"• ورژن php:",'callback_data'=>'text']],
[['text'=>"$mem KB",'callback_data'=>'text'],
['text'=>"• مقدار استفاده از حافظه:",'callback_data'=>'text']],
[['text'=>"$mem_total",'callback_data'=>'text'],
['text'=>"•مقدار حافظه:",'callback_data'=>'text']],
]]
]])
]);
}
if($data == "upping_" && $fromid == $admin){
	$load = sys_getloadavg();
$mem = memory_get_usage();
$ver = phpversion();
$load = sys_getloadavg();
$mem = memory_get_usage();
$ver = phpversion();
if (strpos(PHP_OS, 'L') !== false or strpos(PHP_OS, 'l') !== false) {
 $a = file_get_contents("/proc/meminfo");
 $b = explode('MemTotal:', $a)[1];
 $stats = explode(' kB', $b)[0] / 1024 / 1024;
if ($stats != 0) {
 $mem_total = $stats . 'GB';
} else { 
 $mem_total = 'No Access';
}
} else {
 $mem_total = '!';
}
bot("editmessagetext", [
"text"=>"به  پینگ سلف Version 1 خوش اومدید",
"inline_message_id"=>$inline_message_id,
'parse_mode'=>"html",
"reply_markup"=>json_encode([
"inline_keyboard"=>[
   [['text'=>"• sᴇʀᴠᴇʀ ɪɴғᴏʀᴍᴀᴛɪᴏɴ",'callback_data'=>'text']],
[['text'=>"$load[0] M.s",'callback_data'=>'text'],
['text'=>"• سرعت تبادل داده:",'callback_data'=>'text']],
[['text'=>"$ver",'callback_data'=>'text'],
['text'=>"• ورژن php:",'callback_data'=>'text']],
[['text'=>"$mem KB",'callback_data'=>'text'],
['text'=>"• مقدار استفاده از حافظه:",'callback_data'=>'text']],
[['text'=>"$mem_total",'callback_data'=>'text'],
['text'=>"•مقدار حافظه:",'callback_data'=>'text']],
[["text"=>"🔘 ʙᴀᴄᴋ","callback_data"=>"exit_$inlin"]],
]
])
]);
}
}
if($data == "text_" && $fromid == $admin){
bot("answerCallbackQuery",[
"callback_query_id"=>$update->callback_query->id,
"text"=>"• Tʜɪs sᴇᴄᴛɪᴏɴ ᴄᴀɴɴᴏᴛ ʙᴇ ᴄʜᴀɴɢᴇᴅ"
]);
}
/*

📌 کانال ایلیا سورس
برای دریافت سورس های بیشتر به کانال ما سر بزنید :)
@Source_Eliya

*/