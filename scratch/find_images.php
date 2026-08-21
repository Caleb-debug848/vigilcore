<?php
$dir = 'C:\\Users\\daony\\.gemini\\antigravity-ide\\brain\\6bac1cf1-7105-49ce-aabe-53155607eacd\\.user_uploaded\\';
$files = glob($dir . '*.*');
foreach ($files as $f) {
    $info = getimagesize($f);
    echo basename($f) . " => " . ($info ? $info[0]."x".$info[1] : "unknown") . " (" . filesize($f) . " bytes)\n";
}
