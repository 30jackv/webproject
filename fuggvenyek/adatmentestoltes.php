<?php
function loadData($filename): array {
    return json_decode(file_get_contents($filename), true);
}

function saveData($filename, $array): void {
    file_put_contents($filename, json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}