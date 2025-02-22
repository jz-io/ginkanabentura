<?php
$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'];
$answer = $data['answer'];
$filename = "HTML/Emaitzak/emaitza_{$username}.txt";

file_put_contents($filename, "Answer: {$answer}\n", FILE_APPEND);
echo "Answer saved successfully.";
?>