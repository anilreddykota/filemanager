<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login");
    exit();
}
$user_id  = $_SESSION["user_id"];
require_once 'config/db_connect.php';
require_once 'coding/encode.php';  // Include the encoding functions
global $conn;




if (isset($_GET['shid'])) {
    $shid = $_GET['shid'];
    $stmt = $conn->prepare("DELETE FROM share_stats WHERE shid = ? AND user_id = ?");
    $stmt->bind_param("si", $shid, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: profile");
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





$domains = [];
    $env_domains = $_ENV['STUDENT_PLAN_MAIL_DOMAINS'];
    if ($env_domains) {
        $domains = array_map('trim', explode(',', $env_domains));
}

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
                <a href="dashboard" class="btn btn-outline-primary btn-sm me-2 mb-2 mb-md-0">
                    <i class="fas fa-home me-1"></i> Dashboard
                </a>

                <a class="btn btn-outline-primary btn-sm me-2 mb-2 mb-md-0">
                    <i class="fas fa-user me-1"></i> <?php echo $_SESSION["username"]; ?>
                </a>
                <a href="logout" class="btn btn-outline-danger btn-sm me-2 mb-2 mb-md-0">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>
        </div>




        <div class="row mb-4 g-3">
            <div class="col-md-12">
                <div class="card h-100 shadow-sm">
                    <div class="card-header">Your Profile</div>
                    <div class="card-body">
                        <div class="row g-4 align-items-center">
                            <div class="col-md-4 col-12">
                                <div class="border rounded p-3 h-100 bg-light">
                                    <h6 class="mb-1 text-muted"><i class="fas fa-user me-2"></i>Username</h6>
                                    <p class="mb-0 fw-bold fs-5"><?php echo htmlspecialchars($user['username']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="border rounded p-3 h-100 bg-light">
                                    <h6 class="mb-1 text-muted"><i class="fas fa-envelope me-2"></i>Email</h6>
                                    <p class="mb-0 fw-bold fs-5"><?php echo htmlspecialchars($user['email']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="border rounded p-3 h-100 bg-light">
                                    <h6 class="mb-1 text-muted"><i class="fas fa-crown me-2"></i>Plan</h6>
                                    <p class="mb-0 fw-bold fs-5"><?php echo htmlspecialchars($user['plan']); ?> (<small class="form-text text-muted">Your current plan</small>)</p>
                                    
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="border rounded p-3 h-100 bg-light">
                                    <h6 class="mb-1 text-muted"><i class="fas fa-university me-2"></i>College Email</h6>
                                    <p class="mb-0 fw-bold fs-6">
                                        <?php echo $user['college_email'] ? htmlspecialchars($user['college_email']) : 'Not provided'; ?>
                                        <?php if ($user['college_email_status']): ?>
                                            <span class="badge bg-success ms-2">Verified</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary ms-2">Not Verified</span>
                                            <button type="button" class="btn btn-sm btn-warning ms-2" data-bs-toggle="modal" data-bs-target="#collegeEmailModal" onclick="fillVerifyEmail('<?php echo htmlspecialchars($user['college_email']); ?>')">
                                                Verify Now
                                            </button>
                                        <?php endif; ?>
                                    </p>
                                    <small class="form-text text-muted">Your college email for student plan verification</small>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="border rounded p-3 h-100 bg-light">
                                    <h6 class="mb-1 text-muted"><i class="fas fa-check-circle me-2"></i>Email Status</h6>
                                    <p class="mb-0 fw-bold fs-6">
                                        <?php echo $user['email_status'] ? 'Verified' : 'Not Verified'; ?>
                                        <?php if ($user['email_status']): ?>
                                            <span class="badge bg-success ms-2">Verified</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary ms-2">Not Verified</span>
                                        <?php endif; ?>
                                    </p>
                                    <small class="form-text text-muted">Your email verification status</small>
                                </div>
                            </div>
                        </div>
                        <!-- if pro user show api access  -->
                        <?php if ($user['plan'] === 'pro') : ?>
                            <div class="col-md-12 mt-3">
                                <div class="border rounded p-3 h-100 bg-light">
                                    <h6 class="mb-1 text-muted">
                                        <i class="fas fa-key me-2"></i>API Access
                                        <span class="badge bg-success ms-2">Enabled</span>
                                    </h6>
                                    <small class="form-text text-muted mb-2">You can access your files via API.</small>
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="fw-bold me-2">API Key:</span>
                                        <span class="bg-white border rounded px-2 py-1 text-monospace overflow-auto" id="apiKeyText"><?php echo htmlspecialchars(encodeString($user['id'])); ?></span>
                                        <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('<?php echo htmlspecialchars(encodeString($user['id'])); ?>')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    <small class="form-text text-muted">Keep your API key secret. Use it to authenticate API requests.</small>
                                </div>
                                 
                                </div>
                        <?php endif; ?>
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
                                    <a href="dashboard?folder=<?php echo urlencode(dirname($current_path)); ?>" class="btn btn-sm btn-primary"
                                        target="_blank">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                    <button class="btn btn-sm btn-success" onclick="triggerShare('<?php echo $file['shid']; ?>')">
                                        <i class="fas fa-share-alt

                                        me-1"></i> Share
                                    </button>
                                    <!-- handle delete req to this page with shid-->
                                    <a href="profile?shid=<?php echo $file['shid']; ?>" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash

                                        me-1"></i> Delete
                                    </a>



                                </div>

                            </li>
                            <hr />
                        <?php endforeach; ?>



                    </ul>
                </div>
            </div>
        </div>
        <div class="modal fade" id="collegeEmailModal" tabindex="-1" aria-labelledby="collegeEmailModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post" action="college-verification">
                        <div class="modal-header">
                            <h5 class="modal-title" id="collegeEmailModalLabel">Verify College Email</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <?php if (!empty($domains)): ?>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <div style="display: flex; gap: 8px;">
                                        <input type="text" id="email_local" name="email_local" placeholder="Enter your email username" required style="flex:2;">
                                        <select id="email_domain" name="email_domain" required style="flex:1;">
                                            <?php foreach ($domains as $domain): ?>
                                                <option value="<?php echo htmlspecialchars($domain) ?>"><?php echo htmlspecialchars($domain) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <script>
                                    document.querySelector('form').addEventListener('submit', function(e) {
                                        var local = document.getElementById('email_local').value.trim();
                                        var domain = document.getElementById('email_domain').value;
                                        // Remove any @domain from local part
                                        if (local.includes('@')) {
                                            local = local.split('@')[0];
                                            document.getElementById('email_local').value = local; // Optionally update input
                                        }
                                        if (local && domain) {
                                            let emailInput = document.getElementById('email');
                                            if (!emailInput) {
                                                emailInput = document.createElement('input');
                                                emailInput.type = 'hidden';
                                                emailInput.name = 'email';
                                                emailInput.id = 'email';
                                                this.appendChild(emailInput);
                                            }
                                            emailInput.value = local + '@' + domain;
                                        }
                                    });
                                </script>
                    
                            <?php endif; ?>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Send Verification Link</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
                // Optionally, trigger modal if college email not verified
                <?php if (!$user['college_email_status']) : ?>
                document.addEventListener('DOMContentLoaded', function() {
                        var modal = new bootstrap.Modal(document.getElementById('collegeEmailModal'));
                        // Uncomment below to auto-show modal if not verified
                        // modal.show();
                });
                <?php endif; ?>
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            function triggerShare(shid) {
                const shareLink = window.location.origin + '/files/' + shid;
                if (navigator.share) {
                    navigator.share({
                        title: 'File Share',
                        text: 'Check out this file:',
                        url: shareLink
                    }).catch(error => {
                        console.error('Error sharing:', error);
                        copyToClipboard(shareLink);
                        alert('Sharing failed. Link copied to clipboard: ' + shareLink);
                    }).finally(() => {
                        copyToClipboard(shareLink);
                    });
                } else {
                    copyToClipboard(shareLink);
                }
            }

            function copyToClipboard(text) {
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).catch(function(err) {
                        fallbackCopyTextToClipboard(text);
                    });
                } else {
                    fallbackCopyTextToClipboard(text);
                }
            }

            function fallbackCopyTextToClipboard(text) {
                const textArea = document.createElement("textarea");
                textArea.value = text;
                // Avoid scrolling to bottom
                textArea.style.position = "fixed";
                textArea.style.top = 0;
                textArea.style.left = 0;
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                } catch (err) {
                    // Ignore
                }
                document.body.removeChild(textArea);
            }
            function fillVerifyEmail(email) {
                const emailInput = document.getElementById('email_local');

                if (emailInput) {
                    if (email && email.includes('@')) {
                        const [local] = email.split('@');
                        emailInput.value = local;
                    } else {
                        emailInput.value = email;
                    }
                }
            }
        </script>
</body>

</html>