/**
 * Script untuk Halaman Detail Tugas & Unggah Pengumpulan Tugas (Mahasiswa / Shared)
 */
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('submissionFileInput');
    const dropzone = document.getElementById('submissionDropzone');
    const previewContainer = document.getElementById('submissionPreviewContainer');
    const previewFileName = document.getElementById('previewFileName');
    const previewFileSize = document.getElementById('previewFileSize');
    const fileTypeIcon = document.getElementById('fileTypeIcon');
    const interactivePreviewBox = document.getElementById('interactivePreviewBox');
    const docPreviewHolder = document.getElementById('docPreviewHolder');
    const fileUploadError = document.getElementById('fileUploadError');
    const btnChangeFile = document.getElementById('btnChangeFile');
    const btnRemoveFile = document.getElementById('btnRemoveFile');

    if (!fileInput) return;

    let currentObjectUrl = null;

    function formatBytes(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function getFileBadgeConfig(ext) {
        switch(ext) {
            case 'pdf':
                return { label: 'PDF', bg: '#FEE2E2', color: '#DC2626' };
            case 'doc':
            case 'docx':
                return { label: 'DOC', bg: '#EFF6FF', color: '#2563EB' };
            case 'zip':
                return { label: 'ZIP', bg: '#FEF3C7', color: '#D97706' };
            case 'txt':
                return { label: 'TXT', bg: '#F1F5F9', color: '#475569' };
            default:
                return { label: ext.toUpperCase().slice(0, 4) || 'FILE', bg: '#EFF6FF', color: '#039FFA' };
        }
    }

    function clearPreview() {
        if (currentObjectUrl) {
            URL.revokeObjectURL(currentObjectUrl);
            currentObjectUrl = null;
        }
        fileInput.value = '';
        if (previewContainer) previewContainer.style.display = 'none';
        if (interactivePreviewBox) interactivePreviewBox.style.display = 'none';
        if (docPreviewHolder) docPreviewHolder.innerHTML = '';
        if (dropzone) dropzone.style.display = 'block';
        if (fileUploadError) {
            fileUploadError.style.display = 'none';
            fileUploadError.textContent = '';
        }
    }

    if (btnRemoveFile) {
        btnRemoveFile.addEventListener('click', function(e) {
            e.preventDefault();
            clearPreview();
        });
    }

    if (btnChangeFile) {
        btnChangeFile.addEventListener('click', function(e) {
            e.preventDefault();
            fileInput.click();
        });
    }

    // Drag effects
    if (dropzone) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, function(e) {
                e.preventDefault();
                dropzone.style.borderColor = '#039FFA';
                dropzone.style.background = 'rgba(3, 159, 250, 0.08)';
            });
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, function(e) {
                e.preventDefault();
                dropzone.style.borderColor = 'rgba(3, 159, 250, 0.4)';
                dropzone.style.background = '#F8FAFC';
            });
        });
    }

    fileInput.addEventListener('change', function() {
        if (!fileInput.files || fileInput.files.length === 0) {
            clearPreview();
            return;
        }

        const file = fileInput.files[0];
        const ext = file.name.split('.').pop().toLowerCase();
        const allowedExts = ['pdf', 'doc', 'docx', 'zip', 'txt'];

        if (fileUploadError) {
            fileUploadError.style.display = 'none';
            fileUploadError.textContent = '';
        }

        if (!allowedExts.includes(ext)) {
            if (fileUploadError) {
                fileUploadError.textContent = '✕ Format berkas tidak didukung. Harap pilih PDF, DOC, DOCX, ZIP, atau TXT.';
                fileUploadError.style.display = 'block';
            }
            fileInput.value = '';
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            if (fileUploadError) {
                fileUploadError.textContent = '✕ Ukuran berkas melebihi batas maksimal 10 MB.';
                fileUploadError.style.display = 'block';
            }
            fileInput.value = '';
            return;
        }

        // Update preview details
        if (previewFileName) previewFileName.textContent = file.name;
        if (previewFileSize) previewFileSize.textContent = formatBytes(file.size);

        const badge = getFileBadgeConfig(ext);
        if (fileTypeIcon) {
            fileTypeIcon.textContent = badge.label;
            fileTypeIcon.style.background = badge.bg;
            fileTypeIcon.style.color = badge.color;
        }

        // Handle interactive preview
        if (currentObjectUrl) {
            URL.revokeObjectURL(currentObjectUrl);
            currentObjectUrl = null;
        }
        if (docPreviewHolder) docPreviewHolder.innerHTML = '';
        if (interactivePreviewBox) interactivePreviewBox.style.display = 'none';

        if (ext === 'pdf') {
            currentObjectUrl = URL.createObjectURL(file);
            const embed = document.createElement('embed');
            embed.src = currentObjectUrl;
            embed.type = 'application/pdf';
            embed.style.width = '100%';
            embed.style.height = '100%';
            embed.style.border = 'none';
            embed.style.display = 'block';
            if (docPreviewHolder) docPreviewHolder.appendChild(embed);
            if (interactivePreviewBox) interactivePreviewBox.style.display = 'block';
        } else if (ext === 'txt') {
            const reader = new FileReader();
            reader.onload = function(e) {
                const pre = document.createElement('pre');
                pre.style.margin = '0';
                pre.style.padding = '12px 14px';
                pre.style.maxHeight = '200px';
                pre.style.overflowY = 'auto';
                pre.style.fontSize = '12px';
                pre.style.fontFamily = 'monospace';
                pre.style.color = '#334155';
                pre.style.whiteSpace = 'pre-wrap';
                pre.textContent = e.target.result.slice(0, 1500) + (e.target.result.length > 1500 ? '\n\n... (konten terpotong untuk pratinjau)' : '');
                if (docPreviewHolder) docPreviewHolder.appendChild(pre);
                if (interactivePreviewBox) interactivePreviewBox.style.display = 'block';
            };
            reader.readAsText(file);
        }

        if (previewContainer) previewContainer.style.display = 'block';
        if (dropzone) dropzone.style.display = 'none';
    });
});
