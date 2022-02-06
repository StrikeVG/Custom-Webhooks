<?php
require_once("./discord_send.php");
require_once("./user_translate.php");
function process_release($body) {
    $data = json_decode($body, true);
    $maxlength = getenv("MSG_MAXLENGTH");
    http_response_code(200);
    if($data["action"] != "released") {
        return;
    }

    $re = '/@([A-Za-z0-9-]*)/m';

    // format headers for discord
    $message = preg_replace('/^#+ (.*)$/m', "**$1**");
    // remove release comment "generated with release config...
    $message = preg_replace('/^<!--.*$\n\n/m', "");

    $message = preg_replace_callback($re, function ($hits) {
        return "<@" . user_translate($hits[1]) . ">";
    }, $data["release"]["body"], -1);

    $message = strlen($message) > $maxlength ? substr($message, 0, $maxlength)."..." : $message;

    discord_send(
        [
            "embeds" => [
                [
                    "title" => "New Release of " . $data["repository"]["name"] . " (".$data["release"]["name"].") 🎉",
                    "description" => $message,
                    "url" => $data["release"]["html_url"],
                    "color" => 7405759,
                    "footer" => [
                        "text" => strlen($message) > $maxlength ? "View full changelog by clicking on the embed." : "",
                    ]
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