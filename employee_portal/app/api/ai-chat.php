<?php
session_start();
require __DIR__ . '/../helpers/env.php'; 
header('Content-Type: application/json');

try {
    $apiKey = env('OPENAI_API_KEY');
    if (!$apiKey) throw new Exception("AI API key not set.");

    $question = $_POST['question'] ?? '';
    if (!$question) throw new Exception("Please ask a question.");

    $userId = $_SESSION['user_id'] ?? 0;
    $userFullName = $_SESSION['full_name'] ?? 'Employee';

    $userData = [
        'id' => $userId,
        'full_name' => $userFullName
    ];

    $prompt = "You are a friendly AI assistant for an employee portal. 
User: " . json_encode($userData) . ". 
Question: $question. 
Give step-by-step guidance, explain portal flows, and suggest inputs if needed.";

    $data = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => 'You are a helpful AI assistant for an employee portal.'],
            ['role' => 'user', 'content' => $prompt]
        ],
        'temperature' => 0.6
    ];

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: ' . 'Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    if ($response === false) throw new Exception("cURL Error: " . curl_error($ch));
    curl_close($ch);

    $result = json_decode($response, true);
    $answer = $result['choices'][0]['message']['content'] ?? "Sorry, I couldn't generate an answer.";

    echo json_encode(['answer' => $answer]);

} catch (Exception $e) {
    // Catch any error and respond in JSON
    echo json_encode(['answer' => "Error: " . $e->getMessage()]);
    if (isset($ch)) curl_close($ch);
}