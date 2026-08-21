<?php
$userUploadDir = 'C:\\Users\\daony\\.gemini\\antigravity-ide\\brain\\6bac1cf1-7105-49ce-aabe-53155607eacd\\.user_uploaded\\';

$map = [
    'media_1787308967069.png' => 'public/images/services/mtn.png',
    'media_1787309261543.png' => 'public/images/services/camtel.png',
    'media_1787308967259.jpg' => 'public/images/services/camwater.png',
    'media_1787308967287.png' => 'public/images/services/startimes.png',
    'media_1787309990291.png' => 'public/images/services/smobilpay-ecommerce.png',
    'media_1787309990310.png' => 'public/images/services/smobilpay.png',
    'media_1787309990317.png' => 'public/images/services/eneo.png',
    'media_1787309990365.png' => 'public/images/services/sabc.png',
    'media_1787310050863.jpg' => 'public/images/services/canal.png',
];

foreach ($map as $src => $dst) {
    $fullSrc = $userUploadDir . $src;
    if (file_exists($fullSrc)) {
        // Read and save as high-quality PNG
        $info = getimagesize($fullSrc);
        if ($info['mime'] === 'image/jpeg') {
            $im = imagecreatefromjpeg($fullSrc);
            imagepng($im, $dst, 6);
            imagedestroy($im);
        } else {
            copy($fullSrc, $dst);
        }
        echo "Copied {$src} to {$dst} (Original size: {$info[0]}x{$info[1]})\n";
    } else {
        echo "ERROR: {$fullSrc} not found!\n";
    }
}
