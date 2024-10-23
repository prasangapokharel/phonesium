<?php
session_start();
include 'db_connect.php'; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "You need to log in to use the code editor.";
    exit;
}

// Get the logged-in username
$username = $_SESSION['username'];

// Fetch user's code entries
$stmt = $pdo->prepare("SELECT * FROM code_entries WHERE username = :username");
$stmt->bindParam(':username', $username);
$stmt->execute();
$userCodeEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Phonesium | HTML Compiler</title>
<meta name="description" content="Create and run HTML, CSS, and JavaScript code easily with Phonesium's HTML Compiler. Join our community of developers.">
<meta name="keywords" content="HTML Compiler, Phonesium, web development, coding, HTML, CSS, JavaScript, code editor, online compiler">
<meta name="author" content="Phonesium Team">
<meta property="og:title" content="Phonesium | HTML Compiler">
<meta property="og:description" content="Discover the features of Phonesium's HTML Compiler, where you can easily compile and execute your web code.">
<meta property="og:image" content="ggg.svg"> <!-- Replace with your image path -->
<meta property="og:url" content="https://phonesium.space/platform/htmlcompiler.php"> <!-- Replace with your actual URL -->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preload" href="mine.gif" as="image"> <!-- Preload the image -->
    <link rel="icon" href="ggg.png" type="image/x-icon">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<meta name="cryptomus" content="195c15c6" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Teko:wght@300..700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: 'Teko', sans-serif; /* General font for the body */
        }
        .code-panel {
            height: calc(100% - 50px);
        }
        .ace_gutter-cell {
            padding: auto 5px;
            background-color: transparent; /* Darker gutter for better visibility */
            color:white;
        }
        .editor {
            font-size: 16px; /* Increase the size for better readability */
            font-family: 'Source Code Pro', monospace;
            background-color: black; /* Dark theme */
            border: 2px solid #333;
            color: #dcdcdc; /* Light gray text for better readability */
            height: 600px;
            width: 100%;
        }
        .message {
            display: none; /* Hide the message by default */
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.14/ace.js"></script>
</head>
<body class="bg-black text-white">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="w-1/5 bg-black p-4 flex flex-col">
            <h1 class="flex text-2xl mb-4 font-bold">HTML Compiler</h1>
            <div class="flex flex-col">
                <button id="show-html" class="text-left mb-2 p-3 bg-gray-800 rounded"><i class="fa-brands fa-html5" style="color: #da8610;"></i> index.html</button>
                <button id="show-css" class="text-left mb-2 p-3 bg-gray-800 rounded"><i class="fa-brands fa-css3" style="color: #74C0FC;"></i> style.css</button>
                <button id="show-js" class="text-left mb-2 p-3 bg-gray-800 rounded"><i class="fa-brands fa-js" style="color: #FFD43B;"></i> main.js</button>
            </div>
            <div class="">
                <i class="fa-solid fa-folder fa-sm" style="color: #FFD43B;"></i>
                <input type="text" id="folder-name" class="mt-4 p-2 bg-black rounded" placeholder="Enter Folder Name" />
            </div>
            <button id="save-code" class="mt-4 p-2 bg-white text-black rounded">Save Code</button>
            <button id="run-code" class="mt-4 p-2 bg-blue-600 hover:bg-blue-500 text-white rounded">Run Code</button>
            <div id="message" class="message bg-green-500 text-white"></div> <!-- Message div -->
            

<p class="text-gray-500 dark:text-gray-400 mt-10">
    Please press <kbd class="px-2 py-1.5 text-xs font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-100 dark:border-gray-500"> Ctrl</kbd> + <kbd class="px-2 py-1.5 text-xs font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-100 dark:border-gray-500"> R</kbd> to run the code.
</p>
<p class="text-gray-500 dark:text-gray-400 mt-10">
    Please press <kbd class="px-2 py-1.5 text-xs font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-100 dark:border-gray-500">Ctrl</kbd> + <kbd class="px-2 py-1.5 text-xs font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-100 dark:border-gray-500">S</kbd> to save the code.
</p>

        </div>

        <!-- Code Panels -->
        <div id="editor-container" class="w-3/4 p-4">
            <div id="html-panel" class="code-panel">
                <label class="block mb-2 text-2xl" for="html-editor">HTML</label>
                <div id="html-editor" class="editor"></div>
            </div>
            <div id="css-panel" class="code-panel hidden">
                <label class="block mb-2 text-2xl" for="css-editor">CSS</label>
                <div id="css-editor" class="editor"></div>
            </div>
            <div id="js-panel" class="code-panel hidden">
                <label class="block mb-2 text-2xl" for="js-editor">JavaScript</label>
                <div id="js-editor" class="editor"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Initialize Ace editors
        var htmlEditor = ace.edit("html-editor");
        htmlEditor.setTheme("ace/theme/monokai");
        htmlEditor.session.setMode("ace/mode/html");

        var cssEditor = ace.edit("css-editor");
        cssEditor.setTheme("ace/theme/monokai");
        cssEditor.session.setMode("ace/mode/css");

        var jsEditor = ace.edit("js-editor");
        jsEditor.setTheme("ace/theme/monokai");
        jsEditor.session.setMode("ace/mode/javascript");

        // Handle tab visibility
        $('#show-html').click(function() {
            $('#html-panel').removeClass('hidden').addClass('active');
            $('#css-panel, #js-panel').addClass('hidden').removeClass('active');
        });
        $('#show-css').click(function() {
            $('#css-panel').removeClass('hidden').addClass('active');
            $('#html-panel, #js-panel').addClass('hidden').removeClass('active');
        });
        $('#show-js').click(function() {
            $('#js-panel').removeClass('hidden').addClass('active');
            $('#html-panel, #css-panel').addClass('hidden').removeClass('active');
        });

        // Save code button functionality
        $('#save-code').click(function() {
            saveCode();
        });

        // Run code button functionality
        $('#run-code').click(function() {
            runCode();
        });

        // Function to save code
        function saveCode() {
            var folderName = $('#folder-name').val().trim(); // Get folder name from input
            var username = '<?php echo $username; ?>'; // Use the session username
            var htmlCode = htmlEditor.getValue();
            var cssCode = cssEditor.getValue() || ""; // Use empty string if CSS is not provided
            var jsCode = jsEditor.getValue() || ""; // Use empty string if JS is not provided

            // Validate inputs
            if (!folderName || !htmlCode) { // Only check folder name and HTML code
                showMessage("Please fill in the folder name and HTML code before saving.", "red");
                return;
            }

            // AJAX call to save the code
            $.post('save_code.php', {
                folder_name: folderName,
                username: username,
                html_code: htmlCode,
                css_code: cssCode, // This can be empty
                js_code: jsCode // This can be empty
            }, function(response) {
                console.log("Response from save_code.php:", response);
                showMessage("Code saved successfully! You can now run it.", "green");
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error saving code:", textStatus, errorThrown);
                showMessage("An error occurred while saving your code. Please try again.", "red");
            });
        }

        // Function to run code
        function runCode() {
            var folderName = $('#folder-name').val().trim(); // Get folder name from input

            // Validate folder name
            if (!folderName) {
                showMessage("Please enter a folder name before running the code.", "red");
                return;
            }

            // Open the generated HTML file in a new tab
            window.open(`view_code.php?foldername=${folderName}`, '_blank');
        }

        // Load user's saved code entries
        $(document).ready(function() {
            var userCodeEntries = <?php echo json_encode($userCodeEntries); ?>;

            if (userCodeEntries.length > 0) {
                // Load the first entry for demonstration
                var firstEntry = userCodeEntries[0];
                $('#folder-name').val(firstEntry.folder_name);
                htmlEditor.setValue(firstEntry.html_code);
                cssEditor.setValue(firstEntry.css_code);
                jsEditor.setValue(firstEntry.js_code);
            }
        });

        // Shortcut keys functionality
        $(document).keydown(function(e) {
            // Ctrl + S for Save
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                saveCode();
            }
            // Ctrl + R for Run
            if (e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                runCode();
            }
        });

        // Function to show messages
        function showMessage(message, color) {
            var messageDiv = $('#message');
            messageDiv.text(message);
            messageDiv.css("background-color", color);
            messageDiv.show().fadeOut(5000); // Show and fade out after 5 seconds
        }
    </script>
</body>
</html>
