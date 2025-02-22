<?php
$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'];
$answer = $data['answer'];
$filename = "emaitza_{$username}.txt";
$content = "Answer: {$answer}\n";

// GitHub repository details
$repo = 'jz-io/ginkanabentura';
$path = "Emaitzak/{$filename}";
$token = 'YOUR_GITHUB_PERSONAL_ACCESS_TOKEN';

// Get the current content of the file (if it exists)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.github.com/repos/{$repo}/contents/{$path}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'PHP');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: token {$token}"
));
$response = curl_exec($ch);
curl_close($ch);

$responseData = json_decode($response, true);
if (isset($responseData['sha'])) {
    // File exists, update it
    $sha = $responseData['sha'];
    $content = base64_encode($content . base64_decode($responseData['content']));
    $data = array(
        'message' => "Update {$filename}",
        'content' => $content,
        'sha' => $sha
    );
} else {
    // File does not exist, create it
    $content = base64_encode($content);
    $data = array(
        'message' => "Create {$filename}",
        'content' => $content
    );
}

// Send the request to GitHub
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.github.com/repos/{$repo}/contents/{$path}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'PHP');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: token {$token}"
));
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
$response = curl_exec($ch);
curl_close($ch);

if ($response) {
    echo "Answer saved successfully.";
} else {
    echo "Failed to save the answer.";
}
?>
