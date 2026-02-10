<!DOCTYPE html>
<html>
<head>
    <title>Uploaded Files</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .btn {
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
            margin-right: 5px;
            display: inline-block;
        }
        .btn-download {
            background-color: #2196F3;
            color: white;
        }
        .btn-delete {
            background-color: #f44336;
            color: white;
            border: none;
            cursor: pointer;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 3px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .action-buttons {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        .action-buttons button {
            padding: 8px 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-select-all {
            background-color: #4CAF50;
            color: white;
        }
        .btn-bulk-delete {
            background-color: #f44336;
            color: white;
        }
        .btn-bulk-download {
            background-color: #2196F3;
            color: white;
        }
        .action-buttons button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <h1>Uploaded Files <i class="fa fa-file-text" aria-hidden="true"></i></h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div style="margin-bottom: 20px;">
        <a href="{{ route('upload.form') }}"><i class="fa fa-chevron-left" aria-hidden="true"></i> Back to Upload</a>
    </div>

    @if(count($files) > 0)
        <div class="action-buttons">
            <button type="button" class="btn-select-all" id="selectAllBtn" onclick="toggleSelectAll()">
                <i class="fas fa-check-square"></i> Select All
            </button>
            <button type="button" class="btn-bulk-delete" id="bulkDeleteBtn" onclick="deleteSelected()" disabled>
                <i class="fas fa-trash"></i> Delete Selected
            </button>
            <button type="button" class="btn-bulk-download" id="bulkDownloadBtn" onclick="downloadSelected()" disabled>
                <i class="fas fa-download"></i> Download Selected
            </button>
        </div>

        <form id="bulkDeleteForm" action="{{ route('file.bulkDelete') }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
            <input type="hidden" name="files" id="bulkDeleteFiles">
        </form>

        <table>
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                    </th>
                    <th>Filename</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($files as $file)
                    <tr>
                        <td>
                            <input type="checkbox" class="file-checkbox" value="{{ basename($file) }}" onchange="updateActionButtons()">
                        </td>
                        <td>{{ basename($file) }}</td>
                        <td>
                            <a href="{{ route('file.download', basename($file)) }}" class="btn btn-download" title="Download File">
                                <i class="fas fa-download"></i>
                            </a>
                            <form action="{{ route('file.delete', basename($file)) }}" method="POST" style="display: inline;" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this file?')" title="Delete File">
                                    <i class="fas fa-trash"></i> 
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
    <div style="text-align: center; padding: 40px;">
        <p>No files uploaded yet.</p>
        <a href="{{ route('upload.form') }}" class="btn btn-primary">Upload Files</a>
    </div>   
    @endif
    <div style="text-align: center; padding: 20px;">
    <a href="{{ route('upload.form') }}" class="btn btn-primary">Upload Files</a>
    </div>

    <script>
        function toggleSelectAll() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.file-checkbox');
            const selectAllBtn = document.getElementById('selectAllBtn');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            
            if (selectAllCheckbox.checked) {
                selectAllBtn.innerHTML = '<i class="fas fa-square"></i> Deselect All';
            } else {
                selectAllBtn.innerHTML = '<i class="fas fa-check-square"></i> Select All';
            }
            
            updateActionButtons();
        }

        function updateActionButtons() {
            const checkboxes = document.querySelectorAll('.file-checkbox:checked');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const bulkDownloadBtn = document.getElementById('bulkDownloadBtn');
            const selectAllCheckbox = document.getElementById('selectAll');
            const allCheckboxes = document.querySelectorAll('.file-checkbox');
            
            if (checkboxes.length > 0) {
                bulkDeleteBtn.disabled = false;
                bulkDownloadBtn.disabled = false;
            } else {
                bulkDeleteBtn.disabled = true;
                bulkDownloadBtn.disabled = true;
            }

            if (checkboxes.length === allCheckboxes.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else if (checkboxes.length > 0) {
                selectAllCheckbox.indeterminate = true;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
        }

        function deleteSelected() {
            const checkboxes = document.querySelectorAll('.file-checkbox:checked');
            
            if (checkboxes.length === 0) {
                alert('Please select at least one file to delete.');
                return;
            }
            
            if (!confirm(`Are you sure you want to delete ${checkboxes.length} file(s)?`)) {
                return;
            }
            
            const files = Array.from(checkboxes).map(cb => cb.value);
            document.getElementById('bulkDeleteFiles').value = JSON.stringify(files);
            document.getElementById('bulkDeleteForm').submit();
        }

        function downloadSelected() {
            const checkboxes = document.querySelectorAll('.file-checkbox:checked');
            
            if (checkboxes.length === 0) {
                alert('Please select at least one file to download.');
                return;
            }
            
            checkboxes.forEach(checkbox => {
                const filename = checkbox.value;
                const link = document.createElement('a');
                link.href = '{{ url("file/download") }}/' + filename;
                link.download = filename;
                link.click();
            });
        }
    </script>
</body>
</html>