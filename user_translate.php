<?php
function user_translate($original) {
    $user_translation = json_decode(getenv("USER_TRANSLATION"), true);
    if(!array_key_exists(strtolower($original), $user_translation)) {
        return $original;
    }
    return $user_translation[$original];
}
?>