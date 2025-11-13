<?php
// SMART IMAGE DETECTION SYSTEM FOR TOYREX CORNER

// Check if functions are already declared to prevent redeclaration errors
if (!function_exists('getImagePath')) {
    function getImagePath($baseName) {
        $extensions = ['.png', '.jpg', '.jpeg', '.PNG', '.JPG', '.JPEG'];
        $basePath = "assets/images/";
        
        foreach ($extensions as $ext) {
            $fullPath = $basePath . $baseName . $ext;
            if (file_exists($fullPath)) {
                return $baseName . $ext;
            }
        }
        return 'default.jpg';
    }
}

if (!function_exists('getProductImage')) {
    function getProductImage($productName) {
        $imageMap = [
            'RX-93 Nu Gundam' => 'RX-93',
            'OZ-13MS Gundam Epyon' => 'QZ-13', 
            'Metal Robot Spirits Hi-ν Gundam' => 'Hi-v',
            'Nendoroid Raiden Shogun' => 'Raiden',
            'Nendoroid Robocosan' => 'Robocosan',
            'Nendoroid Hashirama Senju' => 'Hashirama',
            'Nendoroid Eren Yeager' => 'Eren',
            'Nendoroid Loid Forger' => 'Loid',
            'Sofvimates Chopper' => 'Chopper'
        ];
        
        $baseName = $imageMap[$productName] ?? 'default';
        return getImagePath($baseName);
    }
}

if (!function_exists('getBannerImage')) {
    function getBannerImage($bannerNumber) {
        return getImagePath('banner' . $bannerNumber);
    }
}

if (!function_exists('getLogoImage')) {
    function getLogoImage($logoName) {
        return getImagePath($logoName);
    }
}

if (!function_exists('checkAllImages')) {
    function checkAllImages() {
        $images = [
            'banners' => ['banner1', 'banner2', 'banner4', 'banner7'],
            'products' => ['Chopper', 'Eren', 'Hashirama', 'Hi-v', 'Loid', 'QZ-13', 'Raiden', 'Robocosan', 'RX-93'],
            'logos' => ['logo', 'logo1']
        ];
        
        $results = [];
        
        foreach($images as $category => $files) {
            $results[$category] = [];
            foreach($files as $file) {
                $found = getImagePath($file);
                $results[$category][$file] = [
                    'found' => ($found != 'default.jpg'),
                    'path' => $found
                ];
            }
        }
        
        return $results;
    }
}

if (!function_exists('displayImage')) {
    function displayImage($imageName, $alt = "Image", $class = "", $fallbackText = "") {
        $imagePath = getImagePath($imageName);
        $fullPath = "assets/images/" . $imagePath;
        
        if ($imagePath != 'default.jpg' && file_exists($fullPath)) {
            return '<img src="' . $fullPath . '" alt="' . $alt . '" class="' . $class . '" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">';
        } else {
            return '<div class="placeholder-image ' . $class . '"><span>' . ($fallbackText ?: $alt) . '</span></div>';
        }
    }
}
?>