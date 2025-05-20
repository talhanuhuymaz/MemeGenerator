<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to safely get font path
function getFontPath($font_family) {
    $windows_fonts_dir = 'C:/Windows/Fonts/';
    $font_extensions = array(
        'Impact' => array('impact.ttf', 'Impact.ttf'),
        'Arial' => array('arial.ttf', 'Arial.ttf'),
        'Helvetica' => array('arial.ttf', 'Arial.ttf'),
        'Times New Roman' => array('times.ttf', 'Times.ttf'),
        'Comic Sans MS' => array('comic.ttf', 'Comic.ttf')
    );

    // Try different case variations of the font file
    if (isset($font_extensions[$font_family])) {
        foreach ($font_extensions[$font_family] as $font_file) {
            $font_path = $windows_fonts_dir . $font_file;
            if (file_exists($font_path)) {
                return $font_path;
            }
        }
    }

    // Fallback to Arial
    $fallback_fonts = array('arial.ttf', 'Arial.ttf');
    foreach ($fallback_fonts as $font_file) {
        $font_path = $windows_fonts_dir . $font_file;
        if (file_exists($font_path)) {
            return $font_path;
        }
    }

    die('Error: No usable font found. Please check font installation.');
}

// Function to add text to image
function addTextToImage($image, $text, $position, $font_size, $font_family) {
    $width = imagesx($image);
    $height = imagesy($image);
    
    // Set text color (white with black outline for visibility)
    $white = imagecolorallocate($image, 255, 255, 255);
    if ($white === false) {
        die('Error: Could not allocate colors for image.');
    }
    
    $black = imagecolorallocate($image, 0, 0, 0);
    if ($black === false) {
        die('Error: Could not allocate colors for image.');
    }
    
    // Get font path
    $font = getFontPath($font_family);
    
    // Calculate text size
    $bbox = imagettfbbox($font_size, 0, $font, $text);
    if ($bbox === false) {
        die('Error: Could not calculate text size.');
    }
    
    $text_width = abs($bbox[2] - $bbox[0]);
    $text_height = abs($bbox[1] - $bbox[7]);
    
    // Calculate text position
    $x = intval(($width - $text_width) / 2);
    $y = intval($position === 'top' ? ($font_size + 10) : ($height - 10));
    
    // Add text outline (black)
    $outline_size = 2;
    for($ox = -$outline_size; $ox <= $outline_size; $ox++) {
        for($oy = -$outline_size; $oy <= $outline_size; $oy++) {
            $result = imagettftext(
                $image, 
                $font_size, 
                0, 
                intval($x + $ox), 
                intval($y + $oy), 
                $black, 
                $font, 
                $text
            );
            if ($result === false) {
                die('Error: Could not add text outline to image.');
            }
        }
    }
    
    // Add main text (white)
    $result = imagettftext(
        $image, 
        $font_size, 
        0, 
        intval($x), 
        intval($y), 
        $white, 
        $font, 
        $text
    );
    if ($result === false) {
        die('Error: Could not add main text to image.');
    }
}

// Validate file upload
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    die('Error: Please select an image to upload.');
}

// Validate file type
$allowed_types = array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF);
$image_info = @getimagesize($_FILES['image']['tmp_name']);
if ($image_info === false || !in_array($image_info[2], $allowed_types)) {
    die('Error: Please upload a valid JPEG, PNG, or GIF image.');
}

// Create image from uploaded file
$source_image = $_FILES['image']['tmp_name'];
switch ($image_info[2]) {
    case IMAGETYPE_JPEG:
        $image = imagecreatefromjpeg($source_image);
        break;
    case IMAGETYPE_PNG:
        $image = imagecreatefrompng($source_image);
        break;
    case IMAGETYPE_GIF:
        $image = imagecreatefromgif($source_image);
        break;
    default:
        die('Error: Unsupported image format.');
}

if ($image === false) {
    die('Error: Could not create image resource.');
}

// Handle transparency for PNG images
if ($image_info[2] === IMAGETYPE_PNG) {
    imagealphablending($image, true);
    imagesavealpha($image, true);
}

// Get and validate form data
$font_size = isset($_POST['font_size']) ? max(10, min(100, intval($_POST['font_size']))) : 40;
$font_family = isset($_POST['font_family']) ? $_POST['font_family'] : 'Impact';

// Add top text if enabled
if (isset($_POST['top_text_enabled']) && !empty($_POST['top_text'])) {
    addTextToImage($image, $_POST['top_text'], 'top', $font_size, $font_family);
}

// Add bottom text if enabled
if (isset($_POST['bottom_text_enabled']) && !empty($_POST['bottom_text'])) {
    addTextToImage($image, $_POST['bottom_text'], 'bottom', $font_size, $font_family);
}

// Create output directory if it doesn't exist
$output_dir = 'generated_memes';
if (!file_exists($output_dir)) {
    if (!mkdir($output_dir, 0777, true)) {
        die('Error: Could not create output directory.');
    }
}

// Create a unique filename
$output_filename = 'meme_' . time() . '_' . uniqid() . '.jpg';
$output_path = $output_dir . '/' . $output_filename;

// Save the image
$save_result = imagejpeg($image, $output_path, 90);
imagedestroy($image);

if (!$save_result) {
    die('Error: Could not save the generated meme.');
}

// Display the result page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generated Meme</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        h1 {
            color: #4a4a4a;
            margin-bottom: 30px;
            font-size: 2.5em;
            font-weight: 600;
        }

        .meme-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }

        img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 25px;
        }

        .button {
            display: inline-block;
            padding: 12px 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .button:active {
            transform: translateY(0);
        }

        .button.download {
            background: linear-gradient(135deg, #38ef7d 0%, #11998e 100%);
        }

        .error {
            color: #dc3545;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(220, 53, 69, 0.1);
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }

            h1 {
                font-size: 2em;
            }

            .buttons {
                flex-direction: column;
            }

            .button {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Generated Meme</h1>
        <?php if (file_exists($output_path)): ?>
            <div class="meme-container">
                <img src="<?php echo htmlspecialchars($output_path); ?>" alt="Generated Meme">
            </div>
            <div class="buttons">
                <a href="<?php echo htmlspecialchars($output_path); ?>" download class="button download">Download Meme</a>
                <a href="index.php" class="button">Create Another Meme</a>
            </div>
        <?php else: ?>
            <p class="error">Error: The generated image could not be found.</p>
            <div class="buttons">
                <a href="index.php" class="button">Try Again</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 