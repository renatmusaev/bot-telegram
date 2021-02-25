<?php

namespace App\Http\Controllers;

use App\Command;
use App\Keyboard as ModelKeyboard;
use Illuminate\Http\Request;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class BotController extends Controller
{
    public function telegram()
    {
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
                                'keyboard' => [$buttons],
                                'resize_keyboard' => true,
                                'one_time_keyboard' => true
                            ]);
                            $message['reply_markup'] = $keyboard;
                        }
                    }
					
					Telegram::sendMessage($message);
                } else {				
					Telegram::sendMessage([
						'chat_id' => $chat_id,
						'text' => "Empty",
					]);
				}
            }
            else
            {
                $keyboard = ModelKeyboard::with([
                    'children',
                    'photos',
                ])->where('name', $text)->first();
                
                if (!empty($keyboard)) {
                    if (
                        $keyboard->text == null &&
                        $keyboard->photos->isEmpty()
                    ) {
                        Telegram::sendMessage([
                            'chat_id' => $chat_id,
                            'text' => "empty content",
                        ]);
                    } else {
                        if (!empty($keyboard->text)) {
                            $message['chat_id'] = $chat_id;
                            $message['text'] = $keyboard->text;

                            if (!$keyboard->children->isEmpty()) {
                                foreach ($keyboard->children as $child) {
                                    $buttons[] = $child->name;
                                }
            
                                if (isset($buttons)) {
                                    $keyboard = Keyboard::make([
                                        'keyboard' => [$buttons],
                                        'resize_keyboard' => true,
                                        'one_time_keyboard' => true
                                    ]);
                                    $message['reply_markup'] = $keyboard;
                                }
                            }
                            Telegram::sendMessage($message);
                        }

                        //
                        if ($keyboard->photos->isEmpty()) {
                            foreach ($keyboard->photos as $photo) {
                                Telegram::setAsyncRequest(false)->sendPhoto([
                                    'chat_id' => $chat_id,
                                    'photo' => \Telegram\Bot\FileUpload\InputFile::create("https://telegrambot.klac.kz/storage/{$photo->photo}"),
                                    'caption' => $photo->caption
                                ]);
                            }
                        }
                    }
                } else {
                    Telegram::sendMessage([
                        'chat_id' => $chat_id,
                        'text' => "keyboard object empty",
                    ]);
                }
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