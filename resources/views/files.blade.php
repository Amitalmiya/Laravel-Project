<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Files - File Manager</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/pages/files.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <i class="fas fa-folder-open"></i>
                File Manager
            </h1>
            <a href="{{ route('upload.form') }}" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Upload New Files
            </a>
        </div>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(count($files) > 0)
                <div class="stats-bar">
                    <div class="stat-item">
                        <i class="fas fa-file"></i>
                        <div>
                            <div class="stat-value">{{ count($files) }}</div>
                            <div class="stat-label">Total Files</div>
                        </div>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-check-square"></i>
                        <div>
                            <div class="stat-value" id="selectedCount">0</div>
                            <div class="stat-label">Selected</div>
                        </div>
                    </div>
                </div>

                <div class="action-bar">
                    <button type="button" class="btn btn-select-all" id="selectAllBtn" onclick="toggleSelectAll()">
                        <i class="fas fa-check-square"></i> 
                        Select All
                    </button>
                    <button type="button" class="btn btn-bulk-download" id="bulkDownloadBtn" onclick="downloadSelected()" disabled>
                        <i class="fas fa-download"></i> 
                        Download Selected
                    </button>
                    <button type="button" class="btn btn-bulk-delete" id="bulkDeleteBtn" onclick="deleteSelected()" disabled>
                        <i class="fas fa-trash"></i> 
                        Delete Selected
                    </button>
                </div>

                <form id="bulkDeleteForm" action="{{ route('file.bulkDelete') }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="files" id="bulkDeleteFiles">
                </form>

                <div class="file-table-wrapper">
                    <table class="file-table">
                        <thead>
                            <tr>
                                <th>
                                    <input 
                                        type="checkbox" 
                                        id="selectAllCheckbox" 
                                        class="file-checkbox"
                                        onchange="toggleSelectAll()"
                                    >
                                </th>
                                <th>File Name</th>
                                <th>Type</th>
                                <th style="text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($files as $file)
                                @php
                                    $filename = basename($file);
                                    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                    $iconClass = $extension === 'pdf' ? 'pdf' : ($extension === 'txt' ? 'txt' : 'default');
                                    
                                    // Get file size safely
                                    $filePath = public_path('storage/uploads/' . $filename);
                                    $fileSize = 0;
                                    if (file_exists($filePath)) {
                                        $fileSize = filesize($filePath);
                                    }
                                    $sizeInKB = round($fileSize / 1024, 2);
                                    $sizeInMB = round($fileSize / (1024 * 1024), 2);
                                @endphp
                                <tr id="row-{{ $loop->index }}">
                                    <td>
                                        <input 
                                            type="checkbox" 
                                            class="file-checkbox" 
                                            value="{{ $filename }}" 
                                            onchange="updateSelection({{ $loop->index }})"
                                            id="checkbox-{{ $loop->index }}"
                                        >
                                    </td>
                                    <td>
                                        <div class="file-info">
                                            <div class="file-icon-small {{ $iconClass }}">
                                                @if($extension === 'pdf')
                                                    <i class="fas fa-file-pdf"></i>
                                                @elseif($extension === 'txt')
                                                    <i class="fas fa-file-alt"></i>
                                                @else
                                                    <i class="fas fa-file"></i>
                                                @endif
                                            </div>
                                            <div class="file-details">
                                                <div class="file-name-text" title="{{ $filename }}">
                                                    {{ $filename }}
                                                </div>
                                                <div class="file-meta">
                                                    <i class="fas fa-hdd"></i>
                                                    @if($sizeInMB >= 1)
                                                        {{ $sizeInMB }} MB
                                                    @elseif($sizeInKB > 0)
                                                        {{ $sizeInKB }} KB
                                                    @else
                                                        0 KB
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="file-type-badge {{ $iconClass }}">
                                            {{ strtoupper($extension) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('file.download', $filename) }}" 
                                               class="btn-icon btn-download-icon" 
                                               title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <form action="{{ route('file.delete', $filename) }}" 
                                                  method="POST" 
                                                  style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button 
                                                    type="submit" 
                                                    class="btn-icon btn-delete-icon" 
                                                    onclick="return confirm('Are you sure you want to delete {{ $filename }}?')"
                                                    title="Delete"
                                                >
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h2>No Files Yet</h2>
                    <p>You haven't uploaded any files yet. Start by uploading your first file!</p>
                    <a href="{{ route('upload.form') }}" class="btn btn-primary">
                        <i class="fas fa-upload"></i>
                        Upload Your First File
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        let selectAllBtn = document.getElementById('selectAllBtn');
        let selectAllCheckbox = document.getElementById('selectAllCheckbox');
        let bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        let bulkDownloadBtn = document.getElementById('bulkDownloadBtn');
        let selectedCountEl = document.getElementById('selectedCount');

        function toggleSelectAll() {
            const checkboxes = document.querySelectorAll('.file-checkbox:not(#selectAllCheckbox)');
            const allChecked = selectAllCheckbox.checked;
            
            checkboxes.forEach((checkbox, index) => {
                checkbox.checked = allChecked;
                updateRowSelection(index);
            });
            
            updateUI();
        }

        function updateSelection(index) {
            updateRowSelection(index);
            updateUI();
        }

        function updateRowSelection(index) {
            const checkbox = document.getElementById(`checkbox-${index}`);
            const row = document.getElementById(`row-${index}`);
            
            if (checkbox && row) {
                if (checkbox.checked) {
                    row.classList.add('selected');
                } else {
                    row.classList.remove('selected');
                }
            }
        }

        function updateUI() {
            const checkboxes = document.querySelectorAll('.file-checkbox:not(#selectAllCheckbox):checked');
            const totalCheckboxes = document.querySelectorAll('.file-checkbox:not(#selectAllCheckbox)');
            const selectedCount = checkboxes.length;
            const totalCount = totalCheckboxes.length;
            
            if (selectedCountEl) {
                selectedCountEl.textContent = selectedCount;
            }
            
            if (selectedCount === totalCount && totalCount > 0) {
                selectAllCheckbox.checked = true;
                selectAllBtn.innerHTML = '<i class="fas fa-square"></i> Deselect All';
            } else {
                selectAllCheckbox.checked = false;
                selectAllBtn.innerHTML = '<i class="fas fa-check-square"></i> Select All';
            }
            
            if (selectedCount > 0) {
                bulkDeleteBtn.disabled = false;
                bulkDownloadBtn.disabled = false;
            } else {
                bulkDeleteBtn.disabled = true;
                bulkDownloadBtn.disabled = true;
            }
        }

        function deleteSelected() {
            const checkboxes = document.querySelectorAll('.file-checkbox:not(#selectAllCheckbox):checked');
            
            if (checkboxes.length === 0) {
                alert('Please select at least one file to delete.');
                return;
            }
            
            if (!confirm(`Are you sure you want to delete ${checkboxes.length} file(s)? This action cannot be undone.`)) {
                return;
            }
            
            const files = Array.from(checkboxes).map(cb => cb.value);
            document.getElementById('bulkDeleteFiles').value = JSON.stringify(files);
            document.getElementById('bulkDeleteForm').submit();
        }

        function downloadSelected() {
            const checkboxes = document.querySelectorAll('.file-checkbox:not(#selectAllCheckbox):checked');
            
            if (checkboxes.length === 0) {
                alert('Please select at least one file to download.');
                return;
            }
            
            checkboxes.forEach((checkbox, index) => {
                setTimeout(() => {
                    const filename = checkbox.value;
                    const link = document.createElement('a');
                    link.href = '{{ url("file/download") }}/' + encodeURIComponent(filename);
                    link.download = filename;
                    link.click();
                }, index * 200);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateUI();
        });
    </script>
</body>
</html>