<!DOCTYPE html>
<html lang="en">
<head>
    <title>Upload Files - File Manager</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/pages/upload.css') }}">
</head>
<body>
    <div class="container">
        <div class="upload-card">
            <div class="header">
                <h1>
                    <i class="fas fa-cloud-upload-alt"></i>
                    File Upload Center
                </h1>
                <p>Upload your PDF and TXT files securely</p>
            </div>

            <div class="content">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>{{ session('success') }}</strong>
                            @if(session('uploaded_files'))
                                <ul>
                                    @foreach(session('uploaded_files') as $file)
                                        <li>{{ $file }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Please fix the following errors:</strong>
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form action="{{ route('upload.file') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf

                    <div class="upload-area" id="uploadArea" onclick="document.getElementById('files').click()">
                        <div class="upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="upload-text">
                            <h3>Drag & Drop Files Here</h3>
                            <p>or click to browse</p>
                        </div>
                        <div class="file-info">
                            <i class="fas fa-info-circle"></i>
                            Supported formats: PDF, TXT | Maximum size: 10MB per file
                        </div>
                    </div>

                    <input 
                        type="file" 
                        name="files[]" 
                        id="files" 
                        class="file-input"
                        multiple 
                        accept=".pdf,.txt"
                        required
                    >

                    <div class="file-list" id="fileList">
                        <div class="file-list-header">
                            <h3>
                                <i class="fas fa-file-alt"></i>
                                Selected Files
                                <span class="files-count" id="filesCount">0</span>
                            </h3>
                            <div class="clear-all" id="clearAll">
                                <i class="fas fa-times-circle"></i>
                                Clear All
                            </div>
                        </div>
                        <div class="file-items" id="fileItems"></div>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn btn-primary" id="uploadBtn" disabled>
                            <i class="fas fa-upload"></i>
                            Upload Files
                        </button>
                        <a href="{{ route('files.list') }}" class="btn btn-secondary">
                            <i class="fas fa-folder-open"></i>
                            View Uploaded Files
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('files');
        const fileList = document.getElementById('fileList');
        const fileItems = document.getElementById('fileItems');
        const filesCount = document.getElementById('filesCount');
        const uploadBtn = document.getElementById('uploadBtn');
        const clearAllBtn = document.getElementById('clearAll');
        const uploadForm = document.getElementById('uploadForm');
        const uploadArea = document.getElementById('uploadArea');

        let selectedFiles = [];

        fileInput.addEventListener('change', function(e) {
            handleFiles(e.target.files);
        });

        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const dt = e.dataTransfer;
            const files = dt.files;
            
            fileInput.files = files;
            handleFiles(files);
        });

        clearAllBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            selectedFiles = [];
            fileInput.value = '';
            updateFileList();
        });

        function handleFiles(files) {
            selectedFiles = Array.from(files);
            updateFileList();
        }

        function updateFileList() {
            fileItems.innerHTML = '';

            if (selectedFiles.length === 0) {
                fileList.classList.remove('active');
                uploadBtn.disabled = true;
                return;
            }

            fileList.classList.add('active');
            uploadBtn.disabled = false;
            filesCount.textContent = selectedFiles.length;

            selectedFiles.forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                
                const fileSize = formatFileSize(file.size);
                const extension = file.name.split('.').pop().toLowerCase();
                const iconClass = extension === 'pdf' ? 'pdf' : 'txt';
                const iconName = extension === 'pdf' ? 'fa-file-pdf' : 'fa-file-alt';

                fileItem.innerHTML = `
                    <div class="file-icon ${iconClass}">
                        <i class="fas ${iconName}"></i>
                    </div>
                    <div class="file-details">
                        <div class="file-name">${file.name}</div>
                        <div class="file-size">${fileSize}</div>
                    </div>
                    <button type="button" class="remove-file" data-index="${index}" title="Remove file">
                        <i class="fas fa-times"></i>
                    </button>
                `;

                fileItems.appendChild(fileItem);
            });

            document.querySelectorAll('.remove-file').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const index = parseInt(this.getAttribute('data-index'));
                    removeFile(index);
                });
            });
        }

        function removeFile(index) {
            selectedFiles.splice(index, 1);
            
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            fileInput.files = dt.files;

            updateFileList();
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        uploadForm.addEventListener('submit', function(e) {
            if (selectedFiles.length === 0) {
                e.preventDefault();
                alert('Please select at least one file to upload.');
                return;
            }

            uploadForm.classList.add('uploading');
            uploadBtn.textContent = 'Uploading...';
        });
    </script>
</body>
</html>