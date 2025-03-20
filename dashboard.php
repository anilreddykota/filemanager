<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_folder = "uploads/user_" . $_SESSION["user_id"] . "/";
if (!is_dir($user_folder)) {
    mkdir($user_folder, 0777, true);
}

$message = ""; // Initialize message variable
$current_path = "";

// Handle folder navigation
if (isset($_GET["folder"])) {
    $current_path = $_GET["folder"];
    // Validate the path to prevent directory traversal
    if (strpos($current_path, "..") !== false) {
        $current_path = "";
    }
}

$current_dir = $user_folder . $current_path;
// Make sure the path exists and is within the user's folder
if (!is_dir($current_dir) || strpos(realpath($current_dir), realpath($user_folder)) !== 0) {
    $current_path = "";
    $current_dir = $user_folder;
}

// Generate breadcrumb navigation
function generateBreadcrumbs($path) {
    $parts = explode('/', trim($path, '/'));
    $breadcrumbs = '<li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>';
    $accumulated_path = '';
    
    foreach ($parts as $part) {
        if (empty($part)) continue;
        $accumulated_path .= '/' . $part;
        $breadcrumbs .= '<li class="breadcrumb-item"><a href="dashboard.php?folder=' . urlencode($accumulated_path) . '">' . htmlspecialchars($part) . '</a></li>';
    }
    
    return $breadcrumbs;
}

// Function to handle file downloads
function downloadFile($filepath, $filename)
{
    if (file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    } else {
        return "File not found.";
    }
}

// Rest of your functions remain the same...

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $file = $_FILES["file"];
    $filename = basename($file["name"]);
    $targetFilePath = $current_dir . '/' . $filename;

    if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
        $message = "File uploaded successfully!";
    } else {
        $message = "Failed to upload file.";
    }
}

// Handle create folder
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["new_folder_name"])) {
    $new_folder_name = $_POST["new_folder_name"];
    $new_folder_path = $current_dir . '/' . $new_folder_name;
    if (!is_dir($new_folder_path)) {
        if (mkdir($new_folder_path, 0777, true)) {
            $message = "Folder created successfully!";
        } else {
            $message = "Failed to create folder.";
        }
    } else {
        $message = "Folder already exists.";
    }
}

// Handle delete file
if (isset($_GET["delete"])) {
    $fileToDelete = $_GET["delete"];
    $filePathToDelete = $current_dir . '/' . $fileToDelete;
    if (file_exists($filePathToDelete)) {
        if (unlink($filePathToDelete)) {
            $message = "File deleted successfully!";
        } else {
            $message = "Failed to delete file.";
        }
    } else {
        $message = "File not found.";
    }
}

// Handle delete folder
if (isset($_GET["delete_folder"])) {
    $folderToDelete = $_GET["delete_folder"];
    $folderPathToDelete = $current_dir . '/' . $folderToDelete;
    if (is_dir($folderPathToDelete)) {
        if (deleteDirectory($folderPathToDelete)) {
            $message = "Folder deleted successfully!";
        } else {
            $message = "Failed to delete folder.";
        }
    } else {
        $message = "Folder not found.";
    }
}

//  delete folder 
function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }

    return rmdir($dir);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["old_name"]) && isset($_POST["new_name"])) {
    $oldName = $_POST["old_name"];
    $newName = $_POST["new_name"];
    $oldPath = $current_dir . '/' . $oldName;
    $newPath = $current_dir . '/' . $newName;
    if (file_exists($oldPath)) {
        if (rename($oldPath, $newPath)) {
            $message = "Renamed successfully!";
        } else {
            $message = "Failed to rename.";
        }
    } else {
        $message = "File or folder not found.";
    }
}

// Handle download file
if (isset($_GET["download"])) {
    $fileToDownload = $_GET["download"];
    $filePathToDownload = $current_dir . '/' . $fileToDownload;
    $message = downloadFile($filePathToDownload, $fileToDownload);
}

$files = scandir($current_dir);
?>
<!DOCTYPE html>
<html>

<head>
    <title>A - Pannel store files online in orgainzed way up to 1 gb per user</title>
    <link rel="icon" href="./assets/favicon.png" type="image/png">
    <meta name="description" content="A - Pannel store files online in orgainzed way up to 1 gb per user">
    <meta name="keywords" content="A - Pannel store files online in orgainzed way up to 1 gb per user">
    <meta name="author" content="A - Pannel store files online in orgainzed way up to 1 gb per user">
    <meta name="robots" content="index, follow">
    <meta name="revisit-after" content="1 month">
    <meta name="language" content="EN">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Your styles remain the same -->
</head>

<body class="bg-light">
    <div class="container py-4">
        <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
            <h2 class="m-0 mb-3 mb-md-0"><i class="fas fa-file-archive"></i> A Pannel</h2>
            <div class="user-info d-flex flex-column flex-md-row align-items-center mb-5 mb-md-0">
            <?php
            // Calculate storage used
            $totalSize = 0;
            function getFolderSize($folder) {
                $size = 0;
                foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder)) as $file) {
                if ($file->isFile()) {
                    $size += $file->getSize();
                }
                }
                return $size;
            }
                   
            // Get user's plan from database (placeholder - replace with actual query)
            $plan = $_SESSION["plan"];

            $planDetails = [
                "basic" => 100,
                "student"=> 500,
                "pro"=> 1024,
                "ultra"=> 1000000
            ];
            $maxMB = $planDetails[$plan];
            // Get user's storage info
            $totalSize = getFolderSize($user_folder);
            $usedMB = round($totalSize / (1024 * 1024), 2);
            $percentUsed = round(($usedMB / $maxMB ) * 100, 1);
         
            ?>
            
            <div class="d-flex flex-column align-items-center mx-2 mx-md-4 w-100" style="max-width: 250px;">
                <div class="d-flex align-items-center mb-1 flex-wrap justify-content-center">
                <span class="badge bg-primary me-2 mb-1"><?php echo $plan; ?> Plan</span>
                <span class="small"><?php echo $usedMB; ?> MB / <?php echo $maxMB; ?> MB</span>
                </div>
                <div class="progress w-100" style="height: 6px">
                <div class="progress-bar <?php echo ($percentUsed > 90) ? 'bg-danger' : 'bg-success'; ?>" 
                     role="progressbar" 
                     style="width: <?php echo $percentUsed; ?>%" 
                     aria-valuenow="<?php echo $percentUsed; ?>" 
                     aria-valuemin="0" 
                     aria-valuemax="100"></div>
                </div>
            </div>
            </div>
            <div class="top-actions d-flex flex-wrap justify-content-sm-between w-sm-100 justify-content-md-end">
            <a class="btn btn-outline-primary btn-sm me-2 mb-2 mb-md-0">
                <i class="fas fa-user me-1"></i> <?php echo $_SESSION["username"]; ?>
            </a>
            <a href="logout.php" class="btn btn-outline-danger btn-sm me-2 mb-2 mb-md-0">
                <i class="fas fa-sign-out-alt me-1"></i> Logout
            </a>
            </div>
        </div>

        <!-- Breadcrumb navigation -->
        <nav aria-label="breadcrumb" class="bg-white p-2 rounded shadow-sm mb-3">
            <ol class="breadcrumb m-0">
                <?php echo generateBreadcrumbs($current_path); ?>
            </ol>
        </nav>

        <?php if ($message): ?>
            <div class="alert alert-info alert-dismissible fade show shadow-sm">
                <i class="fas fa-info-circle me-2"></i><?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row mb-4 g-3">
            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-header">Upload File</div>
                    <div class="card-body">
                        <form action="dashboard.php?folder=<?php echo urlencode($current_path); ?>" method="POST" enctype="multipart/form-data">
                            <div class="input-group">
                                <input type="file" name="file" class="form-control">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload"></i> Upload
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Create Folder</div>
                    <div class="card-body">
                        <form method="POST" action="dashboard.php?folder=<?php echo urlencode($current_path); ?>">
                            <div class="input-group">
                                <input type="text" name="new_folder_name" class="form-control" placeholder="New folder name" required>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-folder-plus"></i> Create
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Your Files and Folders</h5>
            </div>
            <div class="card-body">
                <ul class="file-list">
                    <?php if($current_path != "\\" || $current_path != " "): ?>
                    <li class="list-group-item">
                        <div class="file-item">
                            <a href="dashboard.php?folder=<?php echo urlencode(dirname($current_path)); ?>" class="file-name">
                                <i class="fas fa-level-up-alt file-icon"></i> Parent Directory
                            </a>
                            <hr/>

                        </div>
                    </li>
                    <?php endif; ?>
                    
                    <?php foreach ($files as $file): ?>
                        <?php if ($file != "." && $file != ".."): ?>
                            <?php $isDir = is_dir($current_dir . '/' . $file); ?>
                            <li class="list-group-item">
                                <div class="file-item d-flex justify-content-between align-items-center">
                                    <?php if ($isDir): ?>
                                        <a href="dashboard.php?folder=<?php echo urlencode($current_path . '/' . $file); ?>" class="file-name">
                                            <i class="fas fa-folder folder-icon file-icon"></i> <?php echo htmlspecialchars($file); ?>
                                        </a>
                                        <div class="actions-group">
                                            <a href="dashboard.php?folder=<?php echo urlencode($current_path); ?>&delete_folder=<?php echo urlencode($file); ?>" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete the folder \'<?php echo $file; ?>\' and all its contents?')">
                                                <i class="fas fa-trash"></i> <span class="d-none d-sm-inline">Delete</span>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#renameModal<?php echo str_replace(array('.', '/'), '_', $file); ?>">
                                                <i class="fas fa-edit"></i>  <span class="d-none d-sm-inline">Rename</span>
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div>

                                       
                                            <i class="fas fa-file file-icon"></i> <?php echo htmlspecialchars($file); ?>
                                            </div>
                                        <div class="actions-group">
                                            <!-- add share  -->
                                            <a onclick="triggerShare('<?php echo urlencode($user_folder).urlencode($current_path) . '/' . urlencode($file); ?>')" class="btn btn-sm btn-primary">
                                                <i class="fas fa-share"></i> <span class="d-none d-sm-inline">Share</span>
                                            </a>
                                            <a href="dashboard.php?folder=<?php echo urlencode($current_path); ?>&download=<?php echo urlencode($file); ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-download"></i> <span class="d-none d-sm-inline">Download</span>
                                            </a>
                                            <a href="dashboard.php?folder=<?php echo urlencode($current_path); ?>&delete=<?php echo urlencode($file); ?>" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete <?php echo $file; ?>?')">
                                                <i class="fas fa-trash"></i>  <span class="d-none d-sm-inline">Delete</span>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#renameModal<?php echo str_replace(array('.', '/'), '_', $file); ?>">
                                                <i class="fas fa-edit"></i> <span class="d-none d-sm-inline">Rename </span>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Rename Modal -->
                                <div class="modal fade" id="renameModal<?php echo str_replace(array('.', '/'), '_', $file); ?>" tabindex="-1"
                                    aria-labelledby="renameModalLabel<?php echo str_replace(array('.', '/'), '_', $file); ?>"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="renameModalLabel<?php echo str_replace(array('.', '/'), '_', $file); ?>">Rename <?php echo $isDir ? 'Folder' : 'File'; ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST" action="dashboard.php?folder=<?php echo urlencode($current_path); ?>">
                                                    <input type="hidden" name="old_name" value="<?php echo htmlspecialchars($file); ?>">
                                                    <div class="mb-3">
                                                        <label for="new_name" class="form-label">New Name:</label>
                                                        <input type="text" class="form-control" id="new_name" name="new_name"
                                                            value="<?php echo htmlspecialchars($file); ?>" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                             
                            </li>
                            <hr/>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function triggerShare(path) {
            fetch('share.php?path=' + encodeURIComponent(path))
            .then(response => {
            if (!response.ok) {
                throw new Error('Failed to generate share link.');
            }
            return response.json();
            })
            .then(data => {
            if (data.shid) {
                const shareLink = window.location.origin + '/files.php?shid=' + data.shid;
                if (navigator.share) {
                navigator.share({
                    title: 'File Share',
                    text: 'Check out this file:',
                    url: shareLink
                }).catch(error => {
                    console.error('Error sharing:', error);
                    alert('Sharing failed. Please copy the link manually: ' + shareLink);
                });
                } else {
                alert('Share this link: ' + shareLink);
                }
            } else {
                alert('Error: Unable to generate share link.');
            }
            })
            .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while generating the share link.');
            });
        }
    </script>
</body>

</html>
