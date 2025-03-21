<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
$user_id  = $_SESSION["user_id"];
require_once 'config/db_connect.php';
global $conn;

if(isset($_GET['shid'])){
    $shid = $_GET['shid'];
    $stmt = $conn->prepare("DELETE FROM share_stats WHERE shid = ? AND user_id = ?");
    $stmt->bind_param("si", $shid, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: profile.php");
    exit();
}

$user_folder = "uploads/user_" . $_SESSION["user_id"] . "/";
if (!is_dir($user_folder)) {
    mkdir($user_folder, 0777, true);
}

$message = ""; // Initialize message variable
$current_path = $user_folder;

function getUserSharedFiles($conn, $user_id)
{
    $query = "SELECT * FROM share_stats WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $shared_files = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $shared_files;
}

$shared_files = getUserSharedFiles($conn, $user_id);

function getUserProfile($conn, $user_id)
{
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

$user = getUserProfile($conn, $user_id);







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


            </div>
            <div class="top-actions d-flex flex-wrap justify-content-sm-between w-sm-100 justify-content-md-end">
                <a href="dashboard.php" class="btn btn-outline-primary btn-sm me-2 mb-2 mb-md-0">
                    <i class="fas fa-home me-1"></i> Dashboard
                </a>

                <a class="btn btn-outline-primary btn-sm me-2 mb-2 mb-md-0">
                    <i class="fas fa-user me-1"></i> <?php echo $_SESSION["username"]; ?>
                </a>
                <a href="logout.php" class="btn btn-outline-danger btn-sm me-2 mb-2 mb-md-0">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>
        </div>




        <div class="row mb-4 g-3">
            <div class="col-md-12">
                <div class="card h-100 shadow-sm">
                    <div class="card-header">Your Profile</div>
                    <div class="card-body d-flex justify-content-around align-items-center">
                        <!-- display user profile details like username, email details  -->
                        <div class="mb-3">
                            <label for="username" class="form-label">Username:</label>
                            <input type="text" class="form-control" id="username" value="<?php echo $user['username']; ?>"
                                readonly>

                        </div>
                        <!-- display email also -->
                        <div class="mb-3">

                            <label for="username" class="form-label">Email :</label>
                            <input type="text" class="form-control" id="username" value="<?php echo $user['email']; ?>"
                                readonly>



                        </div>






                    </div>
                </div>
            </div>

        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Your Shared Files and Folders</h5>
            </div>
            <div class="card-body">
                <ul class="file-list">
                 
                <!-- display share files in table  -->
                    <?php foreach ($shared_files as $file) : ?>
                        <li class="file-item d-flex justify-content-between align-items-center m-2 hr">
                            <div class="file-info">
                                <h6 class="file-name m-0"><?php echo basename($file['path']); ?></h6>
                                <small class="text-muted file-path"></small>
                                    <?php echo preg_replace('#^uploads/user_\d+/#', '', $file['path']); ?>
                                </small>
                            </div>
                            <div class="file-actions">

                                <!-- display view count oc -->
                                <span class="badge bg-secondary rounded-pill"><?php echo $file['oc']; ?> Opens</span>
                                <?php
                                $current_path =  preg_replace('#^uploads/user_\d+/#', '', $file['path']);

                                ?>
                                <a href="dashboard.php?folder=<?php echo urlencode(dirname($current_path)); ?>" class="btn btn-sm btn-primary"
                                    target="_blank">
                                    <i class="fas fa-eye

                                        me-1"></i> View
                                </a>
                                <button class="btn btn-sm btn-success" onclick="triggerShare('<?php echo $file['shid']; ?>')">
                                    <i class="fas fa-share-alt

                                        me-1"></i> Share
                                </button>
                                <!-- handle delete req to this page with shid-->
                                <a href="profile.php?shid=<?php echo $file['shid']; ?>" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash

                                        me-1"></i> Delete
                                </a>



                            </div>
                           
                        </li>
                        <hr/>
                    <?php endforeach; ?>


                   
                </ul>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function triggerShare(shid) {
         
                        const shareLink = window.location.origin + '/files.php?shid=' + shid;
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
                   
              
        }
    </script>
</body>

</html>