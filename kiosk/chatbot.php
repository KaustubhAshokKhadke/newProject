<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$raw = file_get_contents('php://input');
$payload = json_decode($raw ?: '{}', true);
$question = trim($payload['question'] ?? '');

if ($question === ''){
	echo json_encode(['answer' => 'कृपया वैध प्रश्न निवडा.']);
	exit;
}

$apiKey = getenv('OPENAI_API_KEY') ?: '';

// If no API key, return a friendly local fallback answer
if ($apiKey === ''){
	echo json_encode(['answer' => 'स्थानिक उत्तर (डेमो): हा प्रश्न शासनाच्या योजनांबद्दल आहे. कृपया अधिकृत पोर्टल तपासा किंवा किओस्कवर उपलब्ध माहिती पहा.']);
	exit;
}

$systemPrompt = 'तुम्ही महाराष्ट्र शासनाच्या योजनांबद्दल माहिती देणारे सहाय्यक आहात. उत्तर मराठीत संक्षिप्त, मुद्देसूद आणि 4-6 वाक्यांत द्या. जर खात्री नसेल तर अधिकृत संकेतस्थळ पाहण्याचा सल्ला द्या.';

$body = [
	'model' => 'gpt-4o-mini',
	'messages' => [
		[ 'role' => 'system', 'content' => $systemPrompt ],
		[ 'role' => 'user', 'content' => $question ]
	],
	'temperature' => 0.3,
	'max_tokens' => 300
];

try {
	$ch = curl_init('https://api.openai.com/v1/chat/completions');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'Content-Type: application/json',
		'Authorization: Bearer ' . $apiKey,
	]);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
	$response = curl_exec($ch);
	if ($response === false){
		throw new Exception('API call failed: ' . curl_error($ch));
	}
	$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($status < 200 || $status >= 300){
		throw new Exception('API HTTP ' . $status . ' ' . $response);
	}
	$data = json_decode($response, true);
	$answer = $data['choices'][0]['message']['content'] ?? 'क्षमस्व, उत्तर तयार करता आले नाही.';
	echo json_encode(['answer' => $answer]);
} catch (Throwable $e){
	echo json_encode(['answer' => 'त्रुटी: कृपया इंटरनेट/एपीआय सेटिंग तपासा.']);
}

