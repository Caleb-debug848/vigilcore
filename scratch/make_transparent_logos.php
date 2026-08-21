<?php
ini_set('memory_limit', '512M');

function makeTransparentFloodFill($inputPath, $outputPath) {
    $info = getimagesize($inputPath);
    if (!$info) return false;
    
    $mime = $info['mime'];
    if ($mime === 'image/png') {
        $img = imagecreatefrompng($inputPath);
    } elseif ($mime === 'image/jpeg') {
        $img = imagecreatefromjpeg($inputPath);
    } else {
        return false;
    }

    $origW = imagesx($img);
    $origH = imagesy($img);

    // Downscale if too large to conserve memory and optimize web delivery
    $maxDim = 320;
    if ($origW > $maxDim || $origH > $maxDim) {
        $ratio = min($maxDim / $origW, $maxDim / $origH);
        $newW = (int)($origW * $ratio);
        $newH = (int)($origH * $ratio);
        $resized = imagecreatetruecolor($newW, $newH);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $img, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
        imagedestroy($img);
        $img = $resized;
        $width = $newW;
        $height = $newH;
    } else {
        $width = $origW;
        $height = $origH;
    }

    // Create a truecolor image with alpha channel
    $output = imagecreatetruecolor($width, $height);
    imagealphablending($output, false);
    imagesavealpha($output, true);
    $transparent = imagecolorallocatealpha($output, 0, 0, 0, 127);
    imagefilledrectangle($output, 0, 0, $width, $height, $transparent);

    // Identify if target is white background
    $isBgPixel = function($rgb) {
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        return ($r >= 235 && $g >= 235 && $b >= 235);
    };

    // BFS Queue to flood fill transparency starting from borders
    $visited = [];
    $queueX = [];
    $queueY = [];

    // Push all border pixels that match background
    for ($x = 0; $x < $width; $x++) {
        $rgb1 = imagecolorat($img, $x, 0);
        if ($isBgPixel($rgb1)) {
            $queueX[] = $x;
            $queueY[] = 0;
            $visited[$x . '_' . 0] = true;
        }
        $rgb2 = imagecolorat($img, $x, $height - 1);
        if ($isBgPixel($rgb2)) {
            $queueX[] = $x;
            $queueY[] = $height - 1;
            $visited[$x . '_' . ($height - 1)] = true;
        }
    }
    for ($y = 0; $y < $height; $y++) {
        $rgb1 = imagecolorat($img, 0, $y);
        if ($isBgPixel($rgb1)) {
            $queueX[] = 0;
            $queueY[] = $y;
            $visited[0 . '_' . $y] = true;
        }
        $rgb2 = imagecolorat($img, $width - 1, $y);
        if ($isBgPixel($rgb2)) {
            $queueX[] = $width - 1;
            $queueY[] = $y;
            $visited[($width - 1) . '_' . $y] = true;
        }
    }

    $head = 0;
    $count = count($queueX);
    while ($head < $count) {
        $x = $queueX[$head];
        $y = $queueY[$head];
        $head++;

        $neighbors = [
            [$x + 1, $y],
            [$x - 1, $y],
            [$x, $y + 1],
            [$x, $y - 1]
        ];

        foreach ($neighbors as [$nx, $ny]) {
            if ($nx < 0 || $nx >= $width || $ny < 0 || $ny >= $height) continue;
            $key = $nx . '_' . $ny;
            if (isset($visited[$key])) continue;
            
            $rgb = imagecolorat($img, $nx, $ny);
            if ($isBgPixel($rgb)) {
                $visited[$key] = true;
                $queueX[] = $nx;
                $queueY[] = $ny;
                $count++;
            }
        }
    }

    // Copy non-visited pixels to output image
    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $key = $x . '_' . $y;
            if (isset($visited[$key])) {
                imagesetpixel($output, $x, $y, $transparent);
            } else {
                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $a = ($rgb >> 24) & 0x7F;
                $color = imagecolorallocatealpha($output, $r, $g, $b, $a);
                imagesetpixel($output, $x, $y, $color);
            }
        }
    }

    imagepng($output, $outputPath, 9);
    imagedestroy($img);
    imagedestroy($output);
    return true;
}

// Re-copy pristine source images from user uploaded directory
$sourceMap = [
    'media_1787309990310.png' => 'public/images/services/smobilpay.png',
    'media_1787309990291.png' => 'public/images/services/smobilpay-ecommerce.png',
    'media_1787308849931.png' => 'public/images/services/mtn.png',
    'media_1787309261543.png' => 'public/images/services/camtel.png',
    'media_1787308967259.jpg' => 'public/images/services/camwater.jpg',
    'media_1787308967287.png' => 'public/images/services/startimes.png',
    'media_1787309990365.png' => 'public/images/services/sabc.png',
    'media_1787309990317.png' => 'public/images/services/eneo.png',
];

$userUploadDir = 'C:\\Users\\daony\\.gemini\\antigravity-ide\\brain\\6bac1cf1-7105-49ce-aabe-53155607eacd\\.user_uploaded\\';

foreach ($sourceMap as $srcFile => $destFile) {
    $fullSrc = $userUploadDir . $srcFile;
    if (file_exists($fullSrc)) {
        copy($fullSrc, $destFile);
    }
}

$services = [
    'public/images/services/smobilpay.png'           => 'public/images/services/smobilpay.png',
    'public/images/services/smobilpay-ecommerce.png' => 'public/images/services/smobilpay-ecommerce.png',
    'public/images/services/mtn.png'                 => 'public/images/services/mtn.png',
    'public/images/services/camtel.png'              => 'public/images/services/camtel.png',
    'public/images/services/camwater.jpg'            => 'public/images/services/camwater.png',
    'public/images/services/startimes.png'           => 'public/images/services/startimes.png',
    'public/images/services/sabc.png'                => 'public/images/services/sabc.png',
    'public/images/services/eneo.png'                => 'public/images/services/eneo.png',
];

foreach ($services as $in => $out) {
    if (file_exists($in)) {
        echo "Processing {$in}... ";
        $res = makeTransparentFloodFill($in, $out);
        echo ($res ? "OK\n" : "FAILED\n");
    }
}
echo "All transparent images generated successfully.\n";
