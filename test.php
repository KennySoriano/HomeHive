<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomeHive - Upload Contract</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --font-primary: 'Poppins', sans-serif;
            --color-bg: #F5F7FA;
            --color-card: rgba(255, 255, 255, 0.9);
            --color-primary: #FFB22C;
            --color-secondary: #FFCC4D;
            --color-accent: #FFE0D3;
            --color-text: #fafafa;
            --color-muted: #6c757d;
            --color-border: #e0e0e0;
            --shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            --hh-honey-hh-sunset-amber: #FFA726;
            --hh-sunset-amber: #FB8C00;
            --hh-rich-gold: #F57C00;
            --hh-bee-brown: #5D4037;
            --hh-cream-white: #FFF8E1;
            --hh-golden-shadow: #FFCC80;
            --hh-soft-honey: #FFE0B2;
            
               --s: 37px; 
              --s: 44px; 
              --c1: #e6973d;
              --c2: #272c20;
  
              --c:#0000,var(--c1) .5deg 119.5deg,#0000 120deg;
              --g1:conic-gradient(from  60deg at 56.25% calc(425%/6),var(--c));
              --g2:conic-gradient(from 180deg at 43.75% calc(425%/6),var(--c));
              --g3:conic-gradient(from -60deg at 50%   calc(175%/12),var(--c));
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-primary);
            background:
    var(--g1),var(--g1) var(--s) calc(1.73*var(--s)),
    var(--g2),var(--g2) var(--s) calc(1.73*var(--s)),
    var(--g3) var(--s) 0,var(--g3) 0 calc(1.73*var(--s)) 
    var(--c2);
  background-size: calc(2*var(--s)) calc(3.46*var(--s));
            color: #333;
            line-height: 1.6;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .container {
            width: 100%;
            max-width: 900px;
            margin-top: 20px;
        }

        .back-btn {
            background-color: var(--hh-rich-gold);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 12px 20px;
            font-family: var(--font-primary);
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            width: auto;
        }

        .back-btn:hover {
            background-color: var(--hh-sunset-amber);
        }

        .main-card {
            background-color: var(--hh-bee-brown);
            border-radius: 12px;
            padding: 30px;
            box-shadow: var(--shadow);
            color: var(--color-text);
            width: 100%;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            font-weight: 600;
            font-size: 28px;
            color: var(--hh-cream-white);
        }

        .content-split {
            display: flex;
            gap: 30px;
            margin-bottom: 25px;
        }

        .left-column {
            flex: 1;
        }

        .right-column {
            flex: 1;
        }

        .disclaimer {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 8px;
            height: 100%;
        }

        .disclaimer h2 {
            font-size: 18px;
            margin-bottom: 15px;
            color: var(--hh-soft-honey);
        }

        .disclaimer p {
            margin-bottom: 12px;
            font-size: 14px;
        }

        .disclaimer ul {
            margin-left: 20px;
            margin-bottom: 15px;
        }

        .disclaimer li {
            margin-bottom: 8px;
            font-size: 14px;
        }

        .no-contract {
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }

        .no-contract a {
            color: var(--hh-golden-shadow);
            text-decoration: none;
            font-weight: 500;
        }

        .no-contract a:hover {
            text-decoration: underline;
        }

        .upload-area {
            border: 2px dashed var(--hh-golden-shadow);
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .upload-area:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .upload-icon {
            font-size: 40px;
            margin-bottom: 15px;
            color: var(--hh-golden-shadow);
        }

        .upload-text {
            margin-bottom: 15px;
        }

        .upload-text p {
            margin-bottom: 5px;
        }

        .browse-btn {
            background-color: var(--hh-rich-gold);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 12px 20px;
            font-family: var(--font-primary);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .browse-btn:hover {
            background-color: var(--hh-sunset-amber);
        }

        .file-input {
            display: none;
        }

        .file-name {
            margin-top: 15px;
            font-size: 14px;
            color: var(--hh-soft-honey);
            word-break: break-word;
        }

        .preview-container {
            display: none;
            margin-bottom: 20px;
        }

        .preview-title {
            font-size: 16px;
            margin-bottom: 15px;
            color: var(--hh-soft-honey);
            text-align: center;
        }

        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .image-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid var(--hh-golden-shadow);
            aspect-ratio: 3/4;
        }

        .image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .remove-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: rgba(244, 67, 54, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease;
        }

        .remove-btn:hover {
            background-color: rgba(244, 67, 54, 1);
        }

        .file-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 8px;
            font-size: 11px;
            text-align: center;
        }

        .pdf-item {
            background-color: white;
            padding: 16px;
            border-radius: 8px;
            border: 2px solid var(--hh-golden-shadow);
            color: #333;
            text-align: left;
            position: relative;
        }

        .pdf-item p {
            margin-bottom: 8px;
        }

        .submit-btn {
            background-color: var(--hh-rich-gold);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 14px 24px;
            font-family: var(--font-primary);
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .submit-btn:hover {
            background-color: var(--hh-sunset-amber);
        }

        /* Mobile-first responsive design */
        @media (max-width: 768px) {
            body {
                padding: 16px;
            }
            
            .container {
                max-width: 100%;
                margin-top: 10px;
            }
            
            .back-btn {
                width: 100%;
                justify-content: center;
                padding: 14px;
                font-size: 16px;
            }
            
            .main-card {
                padding: 20px;
                border-radius: 10px;
            }
            
            .content-split {
                flex-direction: column;
                gap: 20px;
            }
            
            .logo h1 {
                font-size: 24px;
            }
            
            .disclaimer {
                padding: 16px;
            }
            
            .disclaimer h2 {
                font-size: 16px;
            }
            
            .upload-area {
                padding: 20px;
            }
            
            .upload-icon {
                font-size: 36px;
            }
            
            .browse-btn, .submit-btn {
                padding: 12px 20px;
            }
            
            .image-gallery {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                gap: 12px;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .container {
                max-width: 95%;
            }
            
            .main-card {
                padding: 25px;
            }
            
            .content-split {
                gap: 25px;
            }
        }

        @media (min-width: 1025px) {
            .container {
                max-width: 900px;
            }
        }

        /* Small screen adjustments */
        @media (max-height: 600px) and (orientation: landscape) {
            body {
                padding: 10px;
            }
            
            .container {
                margin-top: 10px;
            }
            
            .main-card {
                padding: 20px;
            }
            
            .upload-area {
                padding: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <button class="back-btn" onclick="window.history.back()">← Back</button>
        
        <div class="main-card">
            <div class="logo">
                <h1>HomeHive</h1>
            </div>
            
            <div class="content-split">
                <div class="left-column">
                    <div class="disclaimer">
                        <h2>Before You Upload</h2>
                        <p>Please ensure you're uploading clear, high-quality photos of your contract. Make sure all text is legible and each page is visible in the frame.</p>
                        <p><strong>Requirements:</strong></p>
                        <ul>
                            <li>Signatures from both parties</li>
                            <li>Notarized document</li>
                            <li>Clear, readable text</li>
                            <li>Complete document (all pages)</li>
                            <li>Upload multiple pages as separate images</li>
                        </ul>
                        <p>After uploading, your contract will be validated by the HomeHive admin before it is processed.</p>
                    </div>
                </div>
                
                <div class="right-column">
                    <div class="upload-area" id="uploadArea">
                        <div class="upload-icon">+</div>
                        <div class="upload-text">
                            <p>Drag & drop your contract files here</p>
                            <p>or</p>
                        </div>
                        <button class="browse-btn" onclick="document.getElementById('fileInput').click()">Browse Files</button>
                        <input type="file" id="fileInput" class="file-input" accept=".jpg,.jpeg,.png,.pdf" multiple>
                        <div class="file-name" id="fileName">No files selected</div>
                    </div>
                </div>
            </div>
            
            <div class="no-contract">
                <p>Don't have a contract copy yet? <a href="#">Click here to download one</a> from your property manager.</p>
            </div>
            
            <div class="preview-container" id="previewContainer">
                <div class="preview-title">Uploaded Pages</div>
                <div class="image-gallery" id="imageGallery">
                    <!-- Files will be added here dynamically -->
                </div>
            </div>
            
            <button class="submit-btn">Submit Contract</button>
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('fileInput');
        const fileName = document.getElementById('fileName');
        const uploadArea = document.getElementById('uploadArea');
        const previewContainer = document.getElementById('previewContainer');
        const imageGallery = document.getElementById('imageGallery');
        
        let uploadedFiles = [];
        
        fileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });
        
        function handleFiles(files) {
            if (files && files.length > 0) {
                // Show preview container
                previewContainer.style.display = 'block';
                
                // Process each file
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    
                    // Check if file is an image
                    if (file.type.match('image.*')) {
                        addImageToGallery(file);
                    } 
                    // Check if file is a PDF
                    else if (file.type === 'application/pdf') {
                        addPdfToGallery(file);
                    }
                }
                
                updateFileNameDisplay();
            } else {
                fileName.textContent = 'No files selected';
                if (uploadedFiles.length === 0) {
                    previewContainer.style.display = 'none';
                }
            }
        }
        
        function addImageToGallery(file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                // Create image item
                const imageItem = document.createElement('div');
                imageItem.className = 'image-item';
                imageItem.dataset.fileId = generateFileId();
                
                // Create image
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Contract page';
                
                // Create remove button
                const removeBtn = document.createElement('button');
                removeBtn.className = 'remove-btn';
                removeBtn.innerHTML = '×';
                removeBtn.onclick = function() {
                    removeFile(file, imageItem.dataset.fileId);
                };
                
                // Create file info
                const fileInfo = document.createElement('div');
                fileInfo.className = 'file-info';
                fileInfo.textContent = file.name.length > 15 ? file.name.substring(0, 12) + '...' : file.name;
                
                // Assemble the item
                imageItem.appendChild(img);
                imageItem.appendChild(removeBtn);
                imageItem.appendChild(fileInfo);
                
                // Add to gallery
                imageGallery.appendChild(imageItem);
                
                // Add to uploaded files array
                uploadedFiles.push({
                    file: file,
                    id: imageItem.dataset.fileId,
                    element: imageItem
                });
            };
            
            reader.readAsDataURL(file);
        }
        
        function addPdfToGallery(file) {
            // Create PDF item
            const pdfItem = document.createElement('div');
            pdfItem.className = 'pdf-item';
            pdfItem.dataset.fileId = generateFileId();
            
            // Create PDF content
            const pdfContent = document.createElement('div');
            pdfContent.innerHTML = `
                <p><strong>PDF Document</strong></p>
                <p>File: ${file.name}</p>
                <p>PDF files cannot be previewed in the browser</p>
            `;
            
            // Create remove button
            const removeBtn = document.createElement('button');
            removeBtn.className = 'remove-btn';
            removeBtn.innerHTML = '×';
            removeBtn.onclick = function() {
                removeFile(file, pdfItem.dataset.fileId);
            };
            
            // Assemble the item
            pdfItem.appendChild(pdfContent);
            pdfItem.appendChild(removeBtn);
            
            // Add to gallery
            imageGallery.appendChild(pdfItem);
            
            // Add to uploaded files array
            uploadedFiles.push({
                file: file,
                id: pdfItem.dataset.fileId,
                element: pdfItem
            });
        }
        
        function removeFile(fileToRemove, fileId) {
            // Find the file in the uploaded files array
            const fileIndex = uploadedFiles.findIndex(item => item.id === fileId);
            
            if (fileIndex !== -1) {
                // Remove from uploaded files array
                uploadedFiles.splice(fileIndex, 1);
                
                // Remove from DOM
                const elementToRemove = document.querySelector(`[data-file-id="${fileId}"]`);
                if (elementToRemove) {
                    elementToRemove.remove();
                }
                
                // Update file name display
                updateFileNameDisplay();
                
                // Hide preview container if no files left
                if (uploadedFiles.length === 0) {
                    previewContainer.style.display = 'none';
                }
            }
        }
        
        function generateFileId() {
            return 'file_' + Math.random().toString(36).substr(2, 9);
        }
        
        function updateFileNameDisplay() {
            if (uploadedFiles.length === 0) {
                fileName.textContent = 'No files selected';
            } else if (uploadedFiles.length === 1) {
                fileName.textContent = uploadedFiles[0].file.name;
            } else {
                fileName.textContent = `${uploadedFiles.length} files selected`;
            }
        }
        
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.backgroundColor = 'rgba(255, 255, 255, 0.1)';
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.backgroundColor = '';
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.backgroundColor = '';
            
            if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                handleFiles(e.dataTransfer.files);
            }
        });

        // Handle window resize for better mobile experience
        window.addEventListener('resize', function() {
            // Adjust gallery layout on resize
            const galleryItems = document.querySelectorAll('.image-item, .pdf-item');
            galleryItems.forEach(item => {
                item.style.maxWidth = '100%';
            });
        });
    </script>
</body>
</html>