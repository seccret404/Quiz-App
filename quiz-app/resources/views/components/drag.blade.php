<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
    <style>
        * {
            font-family: Arial, sans-serif;
            box-sizing: border-box;
        }

        .upload-container {
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            width: 350px;
            margin: 50px auto;
        }

        .upload-icon {
            font-size: 40px;
            color: #4A90E2;
        }

        .upload-text {
            color: #777;
            margin: 10px 0;
            font-size: 18px;
        }

        .upload-btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 18px;
            background-color: #4E73DF;
            color: #fff;
            border: none;
            border-radius: 40px;
            cursor: pointer;
            font-weight: bold;
        }

        .upload-btn:hover {
            background-color: #357ABD;
        }

        .file-input {
            display: none;
        }

        .format-text {
            color: #777;
            font-size: 18px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="upload-container" id="drop-zone">
        <div class="upload-icon">📄</div>
        <p class="upload-text">Drag and Drop file here</p>
        <p>Or</p>
        <label for="file-input" class="upload-btn">Browse File</label>
        <input type="file" name="pdf" id="file-input" class="file-input" accept="application/pdf">
        <p class="format-text">Formats: pdf</p>
    </div>

    <script>
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = "#357ABD";
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.style.borderColor = "#4A90E2";
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = "#4A90E2";
            const files = e.dataTransfer.files;
            if (files.length > 0 && files[0].type === "application/pdf") {
                alert("File uploaded: " + files[0].name);
            } else {
                alert("Only PDF files are allowed!");
            }
        });

        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file && file.type === "application/pdf") {
                alert("File uploaded: " + file.name);
            } else {
                alert("Only PDF files are allowed!");
            }
        });
    </script>

</body>
</html>
