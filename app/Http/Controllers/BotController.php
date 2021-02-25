<?php

namespace App\Http\Controllers;

use App\Command;
use Illuminate\Http\Request;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class BotController extends Controller
{
    public function telegram()
    {
        //dd($this->getCommand("/start"));
        $response = Telegram::getWebHookUpdates();
        if (!$response->isEmpty()) {
            $chat_id = $response['message']['chat']['id'];
            $text = $response['message']['text'];

            if ($text == '/start')
            {
                $command = $this->getCommand($text);

                if (!empty($command)) {
                    $message['chat_id'] = $chat_id;
                    $message['text'] = $command->text;

                    if (!$command->keyboards->isEmpty()) {
                        foreach ($command->keyboards as $key => $value) {
                            $buttons[] = $value->name;
                        }
    
                        if (isset($buttons)) {
                            $keyboard = Keyboard::make([
                                'keyboard' => $buttons,
                                'resize_keyboard' => true,
                                'one_time_keyboard' => true
                            ]);
                            $message['reply_markup'] = $keyboard;
                        }
                    }

                    Telegram::sendMessage($message);
                }
            }
            else
            {
                Telegram::sendMessage([
                    'chat_id' => $chat_id,
                    'text' => "Error",
                ]);
            }
        }
        
        return response('Hello User', 200)->header('Content-Type', 'text/plain');
    }

    //
    private function getCommand($text) {
        $command = Command::with([
            'keyboards'
        ])->where('name', $text)->first();

        return $command;
    }
}