<?php
function secure_upload_file($fileArray, $targetDir = __DIR__ . '/../uploads/', $allowedTypes = ['image']) {
    if (!isset($fileArray['error']) || is_array($fileArray['error'])) {
        return ['success' => false, 'error' => 'Invalid upload parameters.'];
    }

    switch ($fileArray['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            return ['success' => false, 'error' => 'No file uploaded.'];
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return ['success' => false, 'error' => 'Exceeded file size limit.'];
        default:
            return ['success' => false, 'error' => 'Unknown upload error.'];
    }

    $fileSize = $fileArray['size'];
    $fileTmpPath = $fileArray['tmp_name'];
    $originalName = basename($fileArray['name']);
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    // Extension whitelist
    $allowedExtensions = [];
    if (in_array('image', $allowedTypes)) {
        $allowedExtensions = array_merge($allowedExtensions, ['jpg', 'jpeg', 'jfif', 'png', 'webp', 'gif']);
    }
    if (in_array('video', $allowedTypes)) {
        $allowedExtensions = array_merge($allowedExtensions, ['mp4', 'webm', 'mov']);
    }

    if (!in_array($ext, $allowedExtensions)) {
        return ['success' => false, 'error' => 'File extension .' . $ext . ' is not permitted.'];
    }

    // Size check: 10MB for images, 100MB for videos
    $maxSize = in_array('video', $allowedTypes) ? 104857600 : 10485760;
    if ($fileSize > $maxSize) {
        return ['success' => false, 'error' => 'File exceeds maximum allowed size (' . ($maxSize / 1048576) . 'MB).'];
    }

    // MIME type check
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $fileTmpPath);
    finfo_close($finfo);

    $allowedMimeTypes = [
        'image/jpeg', 'image/png', 'image/webp', 'image/gif',
        'video/mp4', 'video/webm', 'video/quicktime'
    ];

    if (!in_array($mimeType, $allowedMimeTypes)) {
        return ['success' => false, 'error' => 'File MIME type (' . $mimeType . ') is invalid.'];
    }

    // Generate safe random filename
    $newFilename = date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $destination = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $newFilename;

    if (!move_uploaded_file($fileTmpPath, $destination)) {
        return ['success' => false, 'error' => 'Failed to move uploaded file to target directory.'];
    }

    return [
        'success' => true,
        'filename' => $newFilename,
        'original_name' => $originalName,
        'file_path' => 'uploads/' . $newFilename,
        'file_size' => $fileSize,
        'mime_type' => $mimeType,
        'ext' => $ext
    ];
}
