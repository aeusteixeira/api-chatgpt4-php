<?php

namespace App\Controllers;

use App\Http\Request;
use Exception;
use OpenAI;

class QuestionImageController extends Controller {

    private $staticPrompt = "Você é um endpoint de uma API Rest que responde apenas com informações exatas e concisas. Responda APENAS com a resposta direta à pergunta a seguir, sem adicionar nenhuma formatação ou texto adicional.\n\nPergunta:\n\n";

    public function respond(Request $request) {
        $dynamicPart = $this->getQuestionFromRequest($request);
        $imagePart = $this->getImageFromRequest($request);
        
        if (empty($dynamicPart)) {
            return $this->response(['error' => 'No question provided'], 400);
        }
        if (empty($imagePart)) {
            return $this->response(['error' => 'No image provided'], 400);
        }
        
        $completePrompt = $this->constructCompletePrompt($dynamicPart);

        try {
            $responseContent = $this->fetchOpenAIResponse($completePrompt, $imagePart);
            return $this->response(['response' => $responseContent]);
        } catch (Exception $error) {
            return $this->response(['error' => $error->getMessage()], 400);
        }
    }

    private function getQuestionFromRequest(Request $request) {
        return $request->input('question');
    }

    private function getImageFromRequest(Request $request) {
        return $request->input('image');
    }

    private function constructCompletePrompt($dynamicPart) {
        return $this->staticPrompt . $dynamicPart;
    }

    private function fetchOpenAIResponse($prompt, $image) {
        $client = OpenAI::client(OPEN_IA_KEY);
        
        $result = $client->chat()->create([
            'model' => 'gpt-4-vision-preview',
            'messages' => [
                [
                    'role' => 'user', 
                    'content' => [
                        ['type' => 'text', 'text' => $prompt],
                        ['type' => 'image_url', 'image_url' => $image],
                    ]
                ],
            ],
        ]);
        $responseContent = stripslashes(trim($result->choices[0]->message->content, '"'));
    
        return $responseContent;
    }
}    
