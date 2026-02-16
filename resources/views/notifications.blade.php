<x-admin-layout>
    <div class="py-12">
        
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-light: rgba(79, 70, 229, 0.1);
            --success-color: #10b981;
            --danger-color: #ef4444;
            --gray-100: #f8fafc;
            --gray-200: #e5e7eb;
            --gray-600: #4b5563;
            --gray-800: #1f2937;
        }

        body {
            min-height: 100vh;
            padding: 20px;
        }

        .notification-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 900px;
            margin: 0 auto;
        }

        .notification-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #7c3aed 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .notification-header h1 {
            margin: 0;
            font-weight: 600;
            font-size: 2rem;
        }

        .notification-header p {
            opacity: 0.9;
            margin: 10px 0 0 0;
            font-size: 1.1rem;
        }

        .notification-body {
            padding: 30px;
        }

        .form-label {
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .btn-send {
            background: linear-gradient(135deg, var(--primary-color) 0%, #7c3aed 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            font-weight: 600;
            border-radius: 10px;
            width: 100%;
            transition: all 0.3s;
            font-size: 1.1rem;
        }

        .btn-send:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 70, 229, 0.4);
        }

        .btn-send:active {
            transform: translateY(0);
        }

        /* Image Upload Styles */
        .image-upload-container {
            border: 2px dashed var(--gray-200);
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            background: var(--gray-100);
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 20px;
        }

        .image-upload-container:hover {
            border-color: var(--primary-color);
            background: var(--primary-light);
        }

        .image-upload-container.dragover {
            border-color: var(--primary-color);
            background: var(--primary-light);
        }

        .upload-icon {
            font-size: 48px;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .image-preview-container {
            margin-top: 20px;
        }

        .image-preview {
            width: 100%;
            max-height: 200px;
            object-fit: contain;
            border-radius: 10px;
            border: 2px solid var(--gray-200);
            padding: 10px;
            background: white;
        }

        .image-options {
            display: flex;
            gap: 20px;
            margin-top: 30px;
        }

        .image-option {
            flex: 1;
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .image-option.active {
            border-color: var(--primary-color);
            background: var(--primary-light);
        }

        .image-option-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .image-option-icon {
            font-size: 24px;
            color: var(--primary-color);
        }

        .tab-content {
            margin-top: 20px;
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
        }

        /* Preview Card */
        .preview-card {
            background: var(--gray-100);
            border: 2px solid var(--gray-200);
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
        }

        .notification-preview {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .preview-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #7c3aed 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            flex-shrink: 0;
        }

        .preview-content {
            flex: 1;
        }

        .preview-title {
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 5px;
            font-size: 1.1rem;
        }

        .preview-body {
            color: var(--gray-600);
            margin-bottom: 10px;
        }

        .preview-image {
            width: 100%;
            max-height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 10px;
        }

        /* Success Message */
        .success-message {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .success-message img {
            max-width: 200px;
            border-radius: 8px;
            margin-top: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .notification-card {
                margin: 10px;
            }
            
            .notification-body {
                padding: 20px;
            }
            
            .image-options {
                flex-direction: column;
            }
        }
    </style>
    <div class="container">
        <div class="notification-card">
            <!-- Header -->
            <div class="notification-header">
                <p>Send notifications to all mobile app users</p>
            </div>

            <!-- Body -->
            <div class="notification-body">
                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="success-message">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="mb-2"><i class="fas fa-check-circle me-2"></i> {{ session('success') }}</h5>
                                <p class="mb-0">Notification has been sent successfully to all users.</p>
                                @if(session('image_url'))
                                    <p class="mt-2 mb-1"><small>Image used:</small></p>
                                    <img src="{{ session('image_url') }}" alt="Notification Image" class="img-thumbnail">
                                @endif
                            </div>
                            <button type="button" class="btn-close btn-close-white" onclick="this.parentElement.parentElement.remove()"></button>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Validation Errors</h6>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('notifications.send') }}" method="POST" enctype="multipart/form-data" id="notificationForm">
                    @csrf

                    <!-- Channel -->
                    <div class="mb-4">
                        <label for="title" class="form-label">
                            <i class="fas fa-mobile me-2"></i> Channel/App *
                        </label>
                        <select class="form-select" id="channel" name="channel"  required>                            
                            <option value="datartech">Datartech</option>
                            <option value="comrades">Comrades</option>
                        </select>
                    </div>

                    <!-- Title -->
                    <div class="mb-4">
                        <label for="title" class="form-label">
                            <i class="fas fa-heading me-2"></i> Notification Title *
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="title" 
                               name="title" 
                               placeholder="Enter notification title" 
                               value="{{ old('title') }}"
                               required
                               maxlength="100">
                        <small class="text-muted">Max 100 characters</small>
                    </div>

                    <!-- Message Body -->
                    <div class="mb-4">
                        <label for="body" class="form-label">
                            <i class="fas fa-comment-alt me-2"></i> Message Body *
                        </label>
                        <textarea class="form-control" 
                                  id="body" 
                                  name="body" 
                                  rows="3" 
                                  placeholder="Enter notification message" 
                                  required
                                  maxlength="255">{{ old('body') }}</textarea>
                        <small class="text-muted">Max 255 characters</small>
                    </div>

                    <!-- Image Selection -->
                    <div class="mb-4">
                        <label class="form-label d-block">
                            <i class="fas fa-image me-2"></i> Notification Image
                        </label>
                        
                        <!-- Image Option Selector -->
                        <div class="image-options mb-3">
                            <div class="image-option active" data-target="upload-tab">
                                <div class="image-option-header">
                                    <i class="fas fa-upload image-option-icon"></i>
                                    <h6 class="mb-0">Upload Image</h6>
                                </div>
                                <p class="mb-0 text-muted">Upload an image from your computer</p>
                            </div>
                            
                            <div class="image-option" data-target="url-tab">
                                <div class="image-option-header">
                                    <i class="fas fa-link image-option-icon"></i>
                                    <h6 class="mb-0">Use URL</h6>
                                </div>
                                <p class="mb-0 text-muted">Use an image from a URL</p>
                            </div>
                            
                            <div class="image-option" data-target="default-tab">
                                <div class="image-option-header">
                                    <i class="fas fa-image image-option-icon"></i>
                                    <h6 class="mb-0">User Uploaded</h6>
                                </div>
                                <p class="mb-0 text-muted">View Uploaded Files</p>
                            </div>
                        </div>

                        <!-- Tab Content -->
                        <div class="tab-content">
                            <!-- Upload Tab -->
                            <div class="tab-pane active" id="upload-tab">
                                <div class="image-upload-container" id="uploadContainer">
                                    <div class="upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <h5>Drag & Drop or Click to Upload</h5>
                                    <p class="text-muted mb-3">Supported formats: JPG, PNG, GIF (Max 2MB)</p>
                                    <input type="file" 
                                           id="image" 
                                           name="image" 
                                           accept="image/*" 
                                           class="d-none" 
                                           onchange="previewUploadedImage(this)">
                                    <button type="button" class="btn btn-primary" onclick="document.getElementById('image').click()">
                                        <i class="fas fa-folder-open me-2"></i> Browse Files
                                    </button>
                                </div>
                                <div class="image-preview-container" id="uploadPreviewContainer" style="display: none;">
                                    <h6>Uploaded Image Preview:</h6>
                                    <img id="uploadPreview" class="image-preview" alt="Uploaded Image Preview">
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearUploadedImage()">
                                            <i class="fas fa-trash me-1"></i> Remove Image
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- URL Tab -->
                            <div class="tab-pane" id="url-tab">
                                <div class="mb-3">
                                    <label for="image_url" class="form-label">Image URL</label>
                                    <input type="url" 
                                           class="form-control" 
                                           id="image_url" 
                                           name="image_url" 
                                           placeholder="https://example.com/image.jpg"
                                           value="{{ old('image_url') }}">
                                    <small class="text-muted">Enter a valid URL to an image</small>
                                </div>
                                <div class="image-preview-container" id="urlPreviewContainer" style="display: none;">
                                    <h6>URL Image Preview:</h6>
                                    <img id="urlPreview" class="image-preview" alt="URL Image Preview">
                                </div>
                            </div>

                            <!-- Default Tab -->
                            <div class="tab-pane" id="default-tab">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Default image will be used for this notification.
                                </div>
                                <div class="image-preview-container">
                                    <h6>Default Image Preview:</h6>
                                    <img src="https://erp.softeriatech.com/v2/images/home/766-473.png" 
                                         class="image-preview" 
                                         alt="Default Image Preview">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Live Preview -->
                    <div class="preview-card">
                        <h6 class="mb-3">
                            <i class="fas fa-eye me-2"></i> Live Preview
                        </h6>
                        <div class="notification-preview">
                            <div class="preview-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="preview-content">
                                <div class="preview-title" id="previewTitle">
                                    {{ old('title', 'Notification Title') }}
                                </div>
                                <div class="preview-body" id="previewBody">
                                    {{ old('body', 'Notification message will appear here...') }}
                                </div>
                                <div id="previewImageContainer">
                                    <!-- Image preview will be inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4">
                        <button type="submit" class="btn btn-send" id="submitBtn">
                            <i class="fas fa-paper-plane me-2"></i> Send Notification to All Users
                        </button>
                        <small class="d-block text-center text-muted mt-2">
                            This will send the notification to all devices subscribed to "datartech" topic
                        </small>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updatePreview();
            setupImageUrlPreview();
            setupDragAndDrop();
        });

        // Image option selection
        document.querySelectorAll('.image-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove active class from all options
                document.querySelectorAll('.image-option').forEach(opt => {
                    opt.classList.remove('active');
                });
                
                // Add active class to clicked option
                this.classList.add('active');
                
                // Show corresponding tab
                const target = this.dataset.target;
                document.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.remove('active');
                });
                document.getElementById(target).classList.add('active');
                
                // Clear other inputs when switching tabs
                if (target === 'upload-tab') {
                    document.getElementById('image_url').value = '';
                } else if (target === 'url-tab') {
                    clearUploadedImage();
                } else if (target === 'default-tab') {
                    document.getElementById('image').value = '';
                    document.getElementById('image_url').value = '';
                }
                
                updatePreview();
            });
        });

        // Drag and drop functionality
        function setupDragAndDrop() {
            const uploadContainer = document.getElementById('uploadContainer');
            
            uploadContainer.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });
            
            uploadContainer.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
            });
            
            uploadContainer.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const file = files[0];
                    if (file.type.startsWith('image/')) {
                        const input = document.getElementById('image');
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        input.files = dataTransfer.files;
                        previewUploadedImage(input);
                    } else {
                        alert('Please drop an image file (JPG, PNG, GIF)');
                    }
                }
            });
        }

        // Preview uploaded image
        function previewUploadedImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('uploadPreview');
                    preview.src = e.target.result;
                    document.getElementById('uploadPreviewContainer').style.display = 'block';
                    updatePreview(e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
                
                // Switch to upload tab
                document.querySelector('[data-target="upload-tab"]').click();
            }
        }

        // Clear uploaded image
        function clearUploadedImage() {
            document.getElementById('image').value = '';
            document.getElementById('uploadPreviewContainer').style.display = 'none';
            document.getElementById('uploadPreview').src = '';
            updatePreview();
        }

        // Setup URL preview
        function setupImageUrlPreview() {
            const urlInput = document.getElementById('image_url');
            
            urlInput.addEventListener('input', function() {
                const url = this.value.trim();
                const previewContainer = document.getElementById('urlPreviewContainer');
                const preview = document.getElementById('urlPreview');
                
                if (url && isValidUrl(url)) {
                    preview.src = url;
                    previewContainer.style.display = 'block';
                    updatePreview(url);
                    
                    // Switch to URL tab
                    if (document.querySelector('[data-target="url-tab"]').classList.contains('active') === false) {
                        document.querySelector('[data-target="url-tab"]').click();
                    }
                } else {
                    previewContainer.style.display = 'none';
                    updatePreview();
                }
            });
            
            // Check initial value
            if (urlInput.value.trim()) {
                const previewContainer = document.getElementById('urlPreviewContainer');
                const preview = document.getElementById('urlPreview');
                preview.src = urlInput.value;
                previewContainer.style.display = 'block';
            }
        }

        // Validate URL
        function isValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        }

        // Update live preview
        function updatePreview(imageSrc = null) {
            // Update title
            const titleInput = document.getElementById('title');
            const previewTitle = document.getElementById('previewTitle');
            previewTitle.textContent = titleInput.value || 'Notification Title';
            
            // Update body
            const bodyInput = document.getElementById('body');
            const previewBody = document.getElementById('previewBody');
            previewBody.textContent = bodyInput.value || 'Notification message will appear here...';
            
            // Update image preview
            const previewImageContainer = document.getElementById('previewImageContainer');
            
            // Get image source based on active tab
            let finalImageSrc = imageSrc;
            
            if (!finalImageSrc) {
                const activeTab = document.querySelector('.image-option.active').dataset.target;
                
                if (activeTab === 'upload-tab' && document.getElementById('uploadPreview').src) {
                    finalImageSrc = document.getElementById('uploadPreview').src;
                } else if (activeTab === 'url-tab' && document.getElementById('image_url').value) {
                    finalImageSrc = document.getElementById('image_url').value;
                } else if (activeTab === 'default-tab') {
                    finalImageSrc = 'https://erp.softeriatech.com/v2/images/home/766-473.png';
                }
            }
            
            // Update preview image
            if (finalImageSrc) {
                previewImageContainer.innerHTML = `
                    <img src="${finalImageSrc}" 
                         class="preview-image" 
                         alt="Notification Image Preview"
                         onerror="this.style.display='none'">
                `;
            } else {
                previewImageContainer.innerHTML = '';
            }
        }

        // Character counters
        const titleInput = document.getElementById('title');
        const bodyInput = document.getElementById('body');
        
        titleInput.addEventListener('input', function() {
            updatePreview();
            updateCharCounter(this, 100, 'title');
        });
        
        bodyInput.addEventListener('input', function() {
            updatePreview();
            updateCharCounter(this, 255, 'body');
        });
        
        function updateCharCounter(input, max, type) {
            const length = input.value.length;
            let counter = input.parentNode.querySelector('.char-counter');
            
            if (!counter) {
                counter = document.createElement('small');
                counter.className = 'char-counter float-end';
                input.parentNode.appendChild(counter);
            }
            
            counter.textContent = `${length}/${max}`;
            counter.style.color = length > max * 0.9 ? '#ef4444' : '#6b7280';
        }
        
        // Initialize counters
        updateCharCounter(titleInput, 100, 'title');
        updateCharCounter(bodyInput, 255, 'body');

        // Form submission
        document.getElementById('notificationForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Sending...';
            submitBtn.disabled = true;
        });
    </script>
        
    </div>
</x-admin-layout>

