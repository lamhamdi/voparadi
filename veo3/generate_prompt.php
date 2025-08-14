<?php
header('Content-Type: application/json');
require_once 'config.php';

function send_json_error($code, $message) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_error(405, 'Method Not Allowed');
}

if (!isset($_SESSION['user_id'])) {
    send_json_error(401, 'Unauthorized. Please log in.');
}

$data = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    send_json_error(400, 'Invalid JSON payload.');
}

$isSimple = $data['isSimple'] ?? false;
$imageData = $data['imageData'] ?? null;

if ($isSimple) {
    $query = $data['query'] ?? '';
    if (empty($query) && empty($imageData)) {
        send_json_error(400, 'Simple query or image cannot be empty.');
    }
    $textPrompt = "You are a world-class prompt engineer for text-to-video AI models. Your task is to create a single, highly detailed, and vivid prompt based on the user's idea and image.

**CRITICAL INSTRUCTIONS:**
1.  **Let the Image Define the Product's Look:** Do NOT describe the visual appearance of the product packaging in your prompt. The video AI will see this from the image.
2.  **Focus on Action & Ingredients:** Your primary job is to build a dynamic scene *around* the product. If the user's brief mentions ingredients, you MUST incorporate them into the scene's action and motion.
3.  **No Placeholders:** You are FORBIDDEN from using placeholders.

**User's Creative Brief:** " . (empty($query) ? "Create a compelling ad for the product in the image." : $query) . "

**Execution Plan:**
Your entire output must be a single JSON object conforming to the specified schema. Fill each field with rich, descriptive detail to create a cinematic and photorealistic scene.";

} else {
    // Advanced form logic
    $productName = $data['productName'] ?? '';
    $videoStyle = $data['videoStyle'] ?? 'cinematic';
    $targetAudience = $data['targetAudience'] ?? 'general';
    $videoDuration = $data['videoDuration'] ?? '15';
    $mood = $data['mood'] ?? 'energetic';
    $scriptLanguage = $data['scriptLanguage'] ?? 'en';
    $dialogue = $data['dialogue'] ?? '';

    $textPrompt = "You are a world-class prompt engineer for text-to-video AI models. Your task is to create a single, highly detailed, and vivid prompt based on the user's specific requirements and optional product image.

**CRITICAL INSTRUCTIONS:**
1.  **Let the Image Define the Product's Look:** If an image is provided, do NOT describe the visual appearance of the product packaging in your prompt. The video AI will see this from the image.
2.  **Adhere to User Specifications:** You MUST strictly follow the user's requirements for style, audience, duration, and mood.
3.  **Incorporate Dialogue:** If the user provides a script or dialogue, it MUST be integrated into the scene's description and action.
4.  **No Placeholders:** You are FORBIDDEN from using placeholders.

**User's Creative Brief:**
*   **Product Name:** " . (empty($productName) ? "The product in the image" : $productName) . "
*   **Video Style:** {$videoStyle}
*   **Target Audience:** {$targetAudience}
*   **Video Duration:** Approximately {$videoDuration} seconds.
*   **Desired Mood:** {$mood}
*   **Dialogue/Script to Include:** " . (empty($dialogue) ? "None provided." : $dialogue) . "
*   **Dialogue Language:** {$scriptLanguage}

**Execution Plan:**
Your entire output must be a single JSON object conforming to the specified schema. Use the creative brief above to fill each field with rich, descriptive detail to create a cinematic and photorealistic scene that matches the user's request.";
}

$parts = [['text' => $textPrompt]];
if (isset($imageData)) {
    $parts[] = ['inlineData' => ['mimeType' => 'image/jpeg', 'data' => $imageData]];
}

// Define the precise JSON schema the AI must follow
$jsonSchema = [
    'type' => 'OBJECT',
    'properties' => [
        'description' => ['type' => 'STRING', 'description' => 'A single, vivid paragraph describing the entire scene and action from start to finish.'],
        'style' => ['type' => 'STRING', 'description' => 'Overall visual style (e.g., photorealistic cinematic, anime, etc.).'],
        'camera' => ['type' => 'STRING', 'description' => 'Describe the camera movement and angle (e.g., slow orbital shot, top-down reveal).'],
        'lighting' => ['type' => 'STRING', 'description' => 'Describe the lighting (e.g., morning sunlight, dramatic studio lighting).'],
        'room' => ['type' => 'STRING', 'description' => 'Describe the environment or room setting.'],
        'elements' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING'], 'description' => 'A list of all key objects and elements in the scene, OTHER than the main product.'],
        'motion' => ['type' => 'STRING', 'description' => 'A detailed description of the movement and animation of the elements and the product.'],
        'ending' => ['type' => 'STRING', 'description' => 'Describe the final state of the scene.'],
        'text' => ['type' => 'STRING', 'description' => 'Any text to be overlaid on the video. Use "none" if no text.'],
        'keywords' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING'], 'description' => 'A list of relevant keywords for the video generation AI.']
    ],
    'required' => ['description', 'style', 'camera', 'lighting', 'elements', 'motion', 'ending', 'keywords']
];


$payload = [
    'contents' => [['role' => 'user', 'parts' => $parts]],
    'generationConfig' => [
        'responseMimeType' => 'application/json',
        'responseSchema' => $jsonSchema
    ]
];

$apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . API_KEY;

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if ($curl_error) {
    send_json_error(500, "cURL Error: " . $curl_error);
}
if ($httpcode !== 200) {
    send_json_error($httpcode, "API call failed. Details: " . $response);
}

$result = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    send_json_error(500, "Failed to decode API response.");
}

if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    send_json_error(500, 'Unexpected API response structure.');
}

$generatedPromptJsonString = $result['candidates'][0]['content']['parts'][0]['text'];
$generatedPromptObject = json_decode($generatedPromptJsonString, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    send_json_error(500, "Failed to parse the JSON from the AI's text response.");
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO prompts (user_id, product_name, video_style, target_audience, video_duration, mood, script_language, dialogue, generated_prompt) 
         VALUES (:user_id, :product_name, :video_style, :target_audience, :video_duration, :mood, :script_language, :dialogue, :generated_prompt)"
    );
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':product_name' => $productName ?? ($data['query'] ?? 'Simple Query'),
        ':video_style' => $videoStyle ?? ($generatedPromptObject['style'] ?? null),
        ':target_audience' => $targetAudience ?? null,
        ':video_duration' => $videoDuration ?? null,
        ':mood' => $mood ?? null,
        ':script_language' => $scriptLanguage ?? null,
        ':dialogue' => $dialogue ?? null,
        ':generated_prompt' => $generatedPromptJsonString
    ]);
    echo json_encode($generatedPromptObject);
} catch (PDOException $e) {
    send_json_error(500, 'Database error: ' . $e->getMessage());
}
?>
