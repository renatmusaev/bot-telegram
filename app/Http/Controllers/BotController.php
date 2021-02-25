<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class BotController extends Controller
{
    public function telegram()
    {
        $response = Telegram::getWebHookUpdates();
        if (!$response->isEmpty()) {
            $chat_id = $response['message']['chat']['id'];
            $text = $response['message']['text'];

            Telegram::sendMessage([
                'chat_id' => $chat_id,
                'text' => $text,
            ]);
        }
        
        return response('Hello User', 200)->header('Content-Type', 'text/plain');
    }
}