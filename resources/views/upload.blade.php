<!DOCTYPE html>
<html>
<head>
    <title>Upload Your Files Here</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        .upload-form {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 2px dashed #4CAF50;
            border-radius: 4px;
            background: white;
        }
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        .btn-primary:hover {
            background-color: #45a049;
        }
        .btn-secondary {
            background-color: #2196F3;
            color: white;
            text-decoration: none;
            display: inline-block;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .file-info {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
        }
        .file-list {
            margin-top: 10px;
            padding: 10px;
            background: white;
            border-radius: 4px;
            max-height: 200px;
            overflow-y: auto;
        }
        .file-item {
            padding: 5px;
            border-bottom: 1px solid #eee;
        }
        .file-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <h1>Upload Your Files Here</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
            @if(session('uploaded_files'))
                <ul style="margin-top: 10px;">
                    @foreach(session('uploaded_files') as $file)
                        <li>{{ $file }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="upload-form">
        <form action="{{ route('upload.file') }}" method="POST" enctype="multipart/form-data">
            @csrf    
            <div class="form-group">
                <label for="files">Select Files (PDF or TXT, max 10MB each)</label>
                <input 
                    type="file" 
                    name="files[]" 
                    id="files" 
                    multiple 
                    accept=".pdf,.txt"
                    required
                >
                <div class="file-info">
                    You can select multiple files.
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Upload Files</button>
                <a href="{{ route('files.list') }}" class="btn btn-secondary">View Uploaded Files</a>
            </div>
        </form>
    </div>

</body>
</html>