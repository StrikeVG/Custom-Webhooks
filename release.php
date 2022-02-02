<?php
require_once("./discord_send.php");
require_once("./user_translate.php");
function process_release($body) {
    $data = json_decode($body, true);
    http_response_code(200);
    if($data["action"] != "released") {
        return;
    }

    discord_send(
        [
            "embeds" => [
                [
                    "title" => "New Release of " . $data["repository"]["name"] . " (".$data["release"]["name"].") 🎉",
                    "description" => $data["release"]["body"],
                    "url" => $data["release"]["url"],
                    "color" => 7405759
                ]
            ],
            "allowed_mentions" => [
                "parse" => [
                    "users"
                ]
            ]
        ]
    );
}
?>