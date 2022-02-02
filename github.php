<?php
require("./settings.php");
require("./issue_comment.php");
require("./release.php");
if($_SERVER['REQUEST_METHOD'] != "POST") {
    http_response_code(400);
    return;
}

$webhookContent = file_get_contents('php://input');
/* $webhook = fopen('php://input' , 'r');
while (!feof($webhook)) {
    $webhookContent .= fread($webhook, 4096);
}
fclose($webhook); */
$headers = getallheaders();

$signature = $headers["X-Hub-Signature-256"];
if($signature == NULL) {
    http_response_code(401);
    return;
}
if($signature != "sha256=" . hash_hmac("sha256", $webhookContent, getenv("GITHUB_SECRET"))) {
    http_response_code(403);
    return;
}

$event = $headers["X-GitHub-Event"]; //"hub" is lowercase in local dev, "Hub" in prod
if($event == NULL) {
    http_response_code(400);
    return;
}

switch($event) {
    case "issue_comment":
        process_issue_comment($webhookContent);
        return;
    case "release":
        process_release($webhookContent);
        return;
    case "ping":
        http_response_code(200);
        return;
}
?>