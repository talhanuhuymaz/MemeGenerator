<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meme Generator</title>
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
        }

        h1 {
            text-align: center;
            color: #4a4a4a;
            margin-bottom: 30px;
            font-size: 2.5em;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 25px;
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #4a4a4a;
        }

        input[type="file"] {
            width: 100%;
            padding: 10px;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            cursor: pointer;
            margin-bottom: 15px;
        }

        input[type="file"]:hover {
            border-color: #764ba2;
        }

        .text-input-group {
            margin-bottom: 15px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .checkbox-group label {
            margin: 0 0 0 8px;
            cursor: pointer;
        }

        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 12px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus, input[type="number"]:focus, select:focus {
            outline: none;
            border-color: #764ba2;
            box-shadow: 0 0 0 3px rgba(118, 75, 162, 0.1);
        }

        .font-controls {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        input[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        input[type="submit"]:active {
            transform: translateY(0);
        }

        /* Preview container */
        #imagePreview {
            max-width: 100%;
            height: 300px;
            margin: 20px 0;
            border-radius: 12px;
            border: 2px dashed #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 16px;
            display: none;
        }

        #imagePreview img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 10px;
            object-fit: contain;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }

            .font-controls {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            h1 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Meme Generator</h1>
        <form action="generate_meme.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="image">Upload Your Image</label>
                <input type="file" name="image" id="image" accept="image/*" required onchange="previewImage(this)">
                <div id="imagePreview">
                    <span>Image preview will appear here</span>
                </div>
            </div>

            <div class="form-group">
                <div class="text-input-group">
                    <div class="checkbox-group">
                        <input type="checkbox" name="top_text_enabled" id="top_text_enabled" checked>
                        <label for="top_text_enabled">Add Top Text</label>
                    </div>
                    <input type="text" name="top_text" placeholder="Enter top text" id="top_text">
                </div>

                <div class="text-input-group">
                    <div class="checkbox-group">
                        <input type="checkbox" name="bottom_text_enabled" id="bottom_text_enabled" checked>
                        <label for="bottom_text_enabled">Add Bottom Text</label>
                    </div>
                    <input type="text" name="bottom_text" placeholder="Enter bottom text" id="bottom_text">
                </div>
            </div>

            <div class="form-group">
                <div class="font-controls">
                    <div>
                        <label for="font_size">Font Size (px)</label>
                        <input type="number" name="font_size" id="font_size" value="40" min="10" max="100">
                    </div>
                    <div>
                        <label for="font_family">Font Style</label>
                        <select name="font_family" id="font_family">
                            <option value="Impact">Impact (Classic Meme)</option>
                            <option value="Arial">Arial</option>
                            <option value="Helvetica">Helvetica</option>
                            <option value="Times New Roman">Times New Roman</option>
                            <option value="Comic Sans MS">Comic Sans MS</option>
                        </select>
                    </div>
                </div>
            </div>

            <input type="submit" value="Generate Meme">
        </form>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            preview.style.display = 'flex';

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.innerHTML = '<span>Image preview will appear here</span>';
            }
        }

        // Enable/disable text inputs based on checkboxes
        document.getElementById('top_text_enabled').addEventListener('change', function() {
            document.getElementById('top_text').disabled = !this.checked;
        });

        document.getElementById('bottom_text_enabled').addEventListener('change', function() {
            document.getElementById('bottom_text').disabled = !this.checked;
        });
    </script>
</body>
</html> 