<?php
require("./user_translate.php");
require("./discord_send.php");

function process_issue_comment($body)
{
    $data = json_decode($body, true);
    if ($data["action"] != "created") {
        http_response_code(200);
        return;
    }
    $sender = "<@" . user_translate(strtolower($data["sender"]["login"])) . ">";
    $comment_body = $data["comment"]["body"];

    $re = '/@([A-Za-z0-9-]*)/m';

    $message = "";
    $comment_body = preg_replace_callback($re, function ($hits) use (&$message) {
        $message .= "<@" . user_translate($hits[1]) . ">, ";
        return "<@" . user_translate($hits[1]) . ">";
    }, $comment_body, -1, $count);

    if ($count == 0) {
        http_response_code(200);
        return;
    }

    $message .= $sender . " requires your attention:\n".$data["comment"]["body"];

    http_response_code(200);

    discord_send(
        [
            "embeds" => [
                [
                    "title" => "New Issue Comment - Input required on " . $data["repository"]["name"] . " #" . $data["issue"]["number"],
                    "description" => $message, "url" => $data["comment"]["html_url"],
                    "color" => 16776960
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
