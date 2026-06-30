<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class ChatGPTHttpService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type'  => 'application/json',
            ],
            'timeout' => 90,
        ]);
    }

    /**
     * Envia pergunta ao modelo
     *
     * @param string $question   Pergunta principal
     * @param array  $options
     *
     * Opções:
     * - context (string)         -> contexto adicional
     * - system (string)          -> prompt system
     * - model (string)
     * - temperature (float)
     * - format ('text'|'json')
     * - json_schema (array|null)
     *
     * @return string|array
     */
    public function ask(string $question, array $options = [])
    {
        if (!$question) {
            throw new \InvalidArgumentException('Pergunta vazia.');
        }

        $format = $options['format'] ?? 'text';
        $forceJson = $format === 'json' || !empty($options['json_schema']);

        // -------------------------
        // Monta mensagens
        // -------------------------
        $messages = [];

        if (!empty($options['system'])) {
            $messages[] = [
                'role' => 'system',
                'content' => $options['system']
            ];
        }

        if (!empty($options['context'])) {
            $messages[] = [
                'role' => 'system',
                'content' => "Contexto:\n" . $options['context']
            ];
        }

        if ($forceJson) {
            $messages[] = [
                'role' => 'system',
                'content' => 'Responda SOMENTE em JSON válido.'
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => $question
        ];

        // -------------------------
        // Body da requisição
        // -------------------------
        $body = [
            'model' => $options['model'] ?? env('OPENAI_MODEL', 'gpt-4.1-mini'),
            'input' => $messages,
            'temperature' => $options['temperature'] ?? 0.3,
        ];

        // -------------------------
        // JSON Schema estruturado
        // -------------------------
        if (!empty($options['json_schema'])) {

            $body['text'] = [
                'format' => [
                    'type'   => 'json_schema',
                    'name'   => 'response',
                    'schema' => $this->normalizeSchema($options['json_schema']),
                    'strict' => true
                ]
            ];

        } elseif ($format === 'json') {

            $body['text'] = [
                'format' => [
                    'type' => 'json_object'
                ]
            ];
        }

        // -------------------------
        // Chamada API
        // -------------------------
        try {
            $response = $this->client->post('responses', [
                'json' => $body
            ]);
        } catch (ClientException $e) {
            $error = (string) $e->getResponse()->getBody();
            throw new \RuntimeException("Erro OpenAI:\n" . $error);
        }

        $result = json_decode($response->getBody(), true);

        $text = $this->extractText($result);

        if (!$text) {
            throw new \RuntimeException('Resposta vazia da OpenAI.');
        }

        // -------------------------
        // Retorno JSON
        // -------------------------
        if ($forceJson) {
            $decoded = json_decode($text, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException("JSON inválido:\n" . $text);
            }
            return $decoded;
        }

        return trim($text);
    }

    // -----------------------------------------------------
    // Extrai texto da resposta
    // -----------------------------------------------------
    private function extractText(array $result): string
    {
        $text = '';

        foreach ($result['output'] ?? [] as $item) {
            if (($item['type'] ?? '') !== 'message') continue;

            foreach ($item['content'] ?? [] as $content) {
                if (($content['type'] ?? '') === 'output_text') {
                    $text .= $content['text'];
                }
            }
        }

        return $text;
    }

    // -----------------------------------------------------
    // Normaliza JSON Schema
    // -----------------------------------------------------
    private function normalizeSchema(array $schema): array
    {
        if (!isset($schema['type'])) {
            $schema['type'] = 'object';
        }

        if ($schema['type'] === 'object') {
            $schema['properties'] = $schema['properties'] ?? [];
            $schema['additionalProperties'] = $schema['additionalProperties'] ?? false;

            foreach ($schema['properties'] as $key => $prop) {
                if (!is_array($prop)) {
                    unset($schema['properties'][$key]);
                    continue;
                }

                if (!isset($prop['type'])) {
                    $prop['type'] = 'string';
                }

                $schema['properties'][$key] = $this->normalizeSchema($prop);
            }
        }

        if ($schema['type'] === 'array') {
            $schema['items'] = $this->normalizeSchema($schema['items'] ?? ['type' => 'string']);
        }

        return $schema;
    }
}



// # ChatGPTHttpService

// A classe ChatGPTHttpService é um cliente HTTP para comunicação com um modelo de IA.

// Ela permite enviar perguntas com ou sem contexto adicional e receber respostas em texto ou JSON.

// ## Função principal

// ask(string $question, array $options = [])

// ## Opções

// context, system, model, temperature, format, json_schema

// ## Consumo da API

// ### Texto simples
// $resp = $service->ask("Explique POO em PHP");

// ### Com contexto
// $resp = $service->ask("Resuma", ['context' => $texto]);

// ### JSON simples
// $resp = $service->ask("Gere um usuário", ['format' => 'json']);

// ### JSON estruturado
// $resp = $service->ask("Sugira título", ['json_schema' => $schema]);

// ### Completo
// $resp = $service->ask("Analise", [
//   'system' => 'Você é um analista',
//   'context' => $conteudo,
//   'temperature' => 0.2,
//   'format' => 'json'
// ]);
