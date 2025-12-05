<?php
// PHP logic to fetch messages from the database
$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "CampusConnect";

$conn = new mysqli($servername, $username, $password, $dbname);
$messages = [];


if (!$conn->connect_error) {
    // Fetch all anonymous messages, newest first
    $sql = "SELECT message_text, tags, attachment_link, created_at FROM AnonymousMessages ORDER BY message_id DESC";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
    }
    $conn->close();
}

// ---------------------------------------------------------------
// FIX: Wrap function definition in a check to prevent redeclaration error
// ---------------------------------------------------------------
if (!function_exists('isImageFile')) {
    function isImageFile($filename) {
        // List of common image extensions to display directly
        $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        // Use pathinfo to safely extract the extension
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION)); 
        return in_array($ext, $image_extensions);
    }
}
// ---------------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SG Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* --- CSS STYLES (ORIGINAL - UNTOUCHED) --- */
        :root {
            --primary-red: #960b15;
            --cream: #fdfcd7;
            --orange-btn: #cf6828;
            --text-color: #333;
            --sidebar-width: 350px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

        body {
            background-color: #f4f4f4;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* --- HEADER --- */
        header {
            background: linear-gradient(90deg, #960b15 0%, #70060d 100%);
            height: 70px;
            display: flex;
            align-items: center;
            padding: 0 30px;
            color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            position: fixed;
            top: 0; width: 100%; z-index: 100;
        }

        .header-logo {
            width: 45px; height: 45px;
            background-color: white; border-radius: 50%;
            border: 2px solid #d4af37;
            margin-right: 15px; overflow: hidden;
            display: flex; justify-content: center; align-items: center;
        }
        .header-logo img { width: 100%; height: 100%; object-fit: cover; }
        .header-title { font-weight: 600; font-size: 18px; letter-spacing: 0.5px; }

        /* --- MAIN LAYOUT --- */
        .main-container {
            display: flex;
            margin-top: 70px; /* Push down below header */
            height: calc(100vh - 70px);
        }

        /* --- LEFT SIDEBAR --- */
        .sidebar {
            width: var(--sidebar-width);
            background-color: white;
            border-right: 1px solid #ddd;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        /* Calendar Section */
        .calendar-section {
            padding: 20px;
            background-color: white;
        }
        .cal-header {
            color: var(--primary-red);
            font-weight: bold; font-size: 20px; text-align: center;
            margin-bottom: 20px; text-transform: uppercase;
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
            text-align: center;
            font-size: 12px; color: #666;
        }
        .day-name { font-weight: bold; margin-bottom: 10px; color: #333; }
        .date {
            padding: 8px; border-radius: 5px; cursor: pointer;
        }
        .date:hover { background-color: #f0f0f0; }
        
        /* Highlighted Event Dates */
        .date.event-date {
            background-color: var(--primary-red);
            color: white;
            font-weight: bold;
        }

        .cal-nav {
            display: flex; justify-content: center; gap: 15px;
            margin-top: 20px; color: var(--primary-red); cursor: pointer; font-size: 18px;
        }

        /* Events Section */
        .events-header {
            background-color: var(--primary-red);
            color: white; padding: 10px 20px;
            display: flex; justify-content: space-between; align-items: center;
            font-weight: bold;
        }
        .events-list {
            background-color: var(--cream);
            flex-grow: 1; /* Fills remaining height */
            padding: 15px;
            display: flex; flex-direction: column; gap: 10px;
        }
        .event-card {
            background-color: var(--primary-red);
            color: white;
            padding: 12px 15px;
            border-radius: 8px;
            display: flex; justify-content: space-between; align-items: center;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .event-card span { font-weight: 500; }
        .event-card i { cursor: pointer; opacity: 0.8; }
        .event-card i:hover { opacity: 1; }

        /* --- RIGHT FEED --- */
        .feed-container {
            flex-grow: 1;
            background-color: #f4f4f4;
            padding: 30px;
            overflow-y: auto;
        }

        /* Post Card */
        .post-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            max-width: 800px;
        }

        .post-header {
            display: flex; justify-content: space-between; align-items: flex-start;
            margin-bottom: 15px;
        }
        .user-info { display: flex; align-items: center; gap: 12px; }
        .avatar {
            width: 45px; height: 45px;
            background-color: #dcdcdc; border-radius: 50%;
        }
        .user-name { font-weight: bold; font-size: 16px; color: #333; }
        .post-menu { color: #333; font-size: 20px; cursor: pointer; letter-spacing: 2px; }

        .post-content {
            font-size: 16px; line-height: 1.5; color: #222; margin-bottom: 15px; font-weight: 500;
        }

        /* Tags */
        .tags-row { display: flex; gap: 10px; margin-bottom: 15px; }
        .tag {
            background-color: #e6e6e6;
            padding: 5px 12px; border-radius: 15px;
            font-size: 12px; color: #444; font-weight: 600;
            display: flex; align-items: center; gap: 6px;
        }
        .tag-dot { width: 6px; height: 6px; background-color: var(--primary-red); border-radius: 50%; } 

        /* Image Attachment Display */
        .attachment-link a {
            display: inline-block;
            background-color: var(--orange-btn);
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        .attachment-link a:hover {
            background-color: #b5561e;
        }

        .post-image {
            width: 100%;
            max-height: 400px; 
            border-radius: 4px;
            overflow: hidden;
            margin-top: 10px;
            background-color: #f0f0f0; 
        }
        /* Ensures the image/media fills the container */
        .post-image img { width: 100%; height: auto; object-fit: contain; } 
        
        .no-image { display: none; }
    </style>
</head>
<body>

    <header>
        <div class="header-logo">
            <img src="logo.png" alt="Logo" onerror="this.style.display='none'">
        </div>
        <div class="header-title">Supreme Student Government</div>
    </header>

    <div class="main-container">
        
        <aside class="sidebar">
            <div class="calendar-section">
                <div class="cal-header">September</div>
                <div class="calendar-grid">
                    <div class="day-name">SUN</div><div class="day-name">MON</div><div class="day-name">TUE</div><div class="day-name">WED</div><div class="day-name">THU</div><div class="day-name">FRI</div><div class="day-name">SAT</div>
                    
                    <div></div> 
                    
                    <div class="date">1</div><div class="date">2</div><div class="date">3</div><div class="date">4</div><div class="date">5</div><div class="date">6</div>
                    <div class="date">7</div><div class="date">8</div><div class="date">9</div><div class="date">10</div><div class="date">11</div><div class="date">12</div><div class="date">13</div>
                    <div class="date">14</div><div class="date">15</div><div class="date">16</div><div class="date">17</div><div class="date">18</div>
                    <div class="date event-date">19</div> <div class="date">20</div>
                    <div class="date">21</div><div class="date">22</div><div class="date">23</div><div class="date">24</div><div class="date">25</div>
                    <div class="date event-date">26</div> <div class="date">27</div>
                    <div class="date">28</div><div class="date">29</div><div class="date">30</div>
                </div>
                <div class="cal-nav">
                    <i class="fa-solid fa-chevron-left"></i>
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </div>

            <div class="events-header">
                <div style="display:flex; align-items:center; gap:8px;">
                    <i class="fa-regular fa-calendar"></i> Events
                </div>
                <i class="fa-solid fa-plus" style="cursor:pointer;"></i>
            </div>
            
            <div class="events-list">
                <div class="event-card">
                    <span>19 CTU Nite 2025</span>
                    <i class="fa-solid fa-xmark"></i>
                </div>
                <div class="event-card">
                    <span>26 Acquaintance Party</span>
                    <i class="fa-solid fa-xmark"></i>
                </div>
            </div>
        </aside>

        <main class="feed-container" id="feedContainer">
            <?php
            // File extension helper function for PHP (to determine if it's an image)
            function isImageFile($filename) {
                $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                // Use pathinfo to safely extract the extension
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION)); 
                return in_array($ext, $image_extensions);
            }

            if (!empty($messages)):
                foreach ($messages as $msg):
                    $tags = explode(',', $msg['tags']);
                    $tagsHtml = '';
                    
                    if (!empty($msg['tags'])):
                        $tagsHtml = '<div class="tags-row">';
                        foreach (array_unique($tags) as $tag) {
                            $tagsHtml .= '<div class="tag"><div class="tag-dot"></div>' . htmlspecialchars(trim($tag)) . '</div>';
                        }
                        $tagsHtml .= '</div>';
                    endif;

                    // Remove tags from the display text
                    $displayText = $msg['message_text'];
                    $displayText = str_replace(array('#Concern', '#Complaint', '#Query'), '', $displayText);
                    $displayText = trim($displayText);

                    // --- ENHANCED ATTACHMENT DISPLAY LOGIC ---
                    $attachment_link = $msg['attachment_link'] ?? null;
                    $mediaHtml = '';

                    if (!empty($attachment_link)) {
                        
                        if (isImageFile($attachment_link)) {
                            // If it's an image, display it directly inside the post-image container
                            $mediaHtml = '<div class="post-image">
                                <img src="' . htmlspecialchars($attachment_link) . '" alt="Attached Image">
                            </div>';
                        } else {
                            // If it's another file type, display a clickable link
                            $mediaHtml = '<div class="attachment-link">
                                <a href="' . htmlspecialchars($attachment_link) . '" target="_blank">
                                    <i class="fa-solid fa-file-arrow-down"></i> View/Download Attachment
                                </a>
                            </div>';
                        }
                    }
                    // --- END ATTACHMENT LOGIC ---
                    
                    $postHTML = '
                    <div class="post-card">
                        <div class="post-header">
                            <div class="user-info">
                                <div class="avatar"></div>
                                <div class="user-name">Anonymous</div>
                            </div>
                            <div class="post-menu" title="Received: ' . htmlspecialchars($msg['created_at']) . '"><i class="fa-solid fa-ellipsis"></i></div>
                        </div>
                        <div class="post-content">' . htmlspecialchars($displayText) . '</div>
                        ' . $tagsHtml . $mediaHtml . '
                    </div>';
                    
                    echo $postHTML;
                endforeach;
            else:
                echo '<p style="text-align: center; color: #999; padding-top: 50px;">No anonymous messages received yet.</p>';
            endif;
            ?>
        </main>
    </div>

    <script>
        // No client-side message fetching needed, PHP handles message display.
        // This script block is here only for structure preservation.
    </script>

</body>
</html>

The dashboard.html file has errors in the php code, please fix it and enhance it please