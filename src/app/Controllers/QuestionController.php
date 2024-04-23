<?php

namespace App\Controllers;

use App\Http\Request;
use Exception;
use OpenAI;

class QuestionController extends Controller {

    private $staticPrompt = "Você é um endpoint de uma API Rest que responde apenas com informações exatas e concisas. Responda APENAS com a resposta direta à pergunta a seguir, sem adicionar nenhuma formatação ou texto adicional.\n\nPergunta:\n\n";

    public function index() {
        $mpbQuestion = "Quem são considerados os principais expoentes da MPB e por quê?";
        $completePrompt = $this->constructCompletePrompt($mpbQuestion);

        try {
            $responseContent = $this->fetchOpenAIResponse($completePrompt);
            return $this->response(['response' => $responseContent]);
        } catch (Exception $error) {
            return $this->response(['error' => $error->getMessage()], 400);
        }
    }

    public function respond(Request $request) {
        $dynamicPart = $this->getQuestionFromRequest($request);
        
        if (empty($dynamicPart)) {
            return $this->response(['error' => 'No question provided'], 400);
        }
        
        $completePrompt = $this->constructCompletePrompt($dynamicPart);

        try {
            $responseContent = $this->fetchOpenAIResponse($completePrompt);
            return $this->response(['response' => $responseContent]);
        } catch (Exception $error) {
            return $this->response(['error' => $error->getMessage()], 400);
        }
    }

    private function getQuestionFromRequest(Request $request) {
        return $request->input('question');
    }

    private function constructCompletePrompt($dynamicPart) {
        return $this->staticPrompt . $dynamicPart;
    }

    private function fetchOpenAIResponse($prompt) {
        $client = OpenAI::client(OPEN_IA_KEY);
        
        $result = $client->chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);
    
        $responseContent = stripslashes(trim($result->choices[0]->message->content, '"'));
    
        return $responseContent;
    }
}    
