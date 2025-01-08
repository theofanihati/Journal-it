<?php
include "service/database.php";
session_start();

$show_deleted_popup = false;

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$logout_messages = "";

if (isset($_POST["logout-button"])) {
    $username = $_SESSION["username"];

    try {
        $sql = "UPDATE session_list SET waktu_keluar = NOW() WHERE user_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $username);
        if ($stmt->execute()) {
            $logout_messages = "LOG OUT succeeded!";
            session_unset();
            session_destroy();
            header("Location: register.php");
            exit();
        } else {
            $logout_messages = "LOG OUT failed, try again.";
        }
    } catch (mysqli_sql_exception $e) {
        $logout_messages = $e->getMessage();
    }
}

$username = $_SESSION["username"];
$journals = [];

try {
    $sql = "SELECT id_journal, title, description, date_created, image FROM journals WHERE user_name = ? ORDER BY date_created DESC";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $journals[] = $row;
    }
} catch (mysqli_sql_exception $e) {
    $logout_messages = $e->getMessage();
}

if (isset($_POST["delete-button"])) {
    $journalId = $_POST["journal-id"];
    try {
        $stmt = $db->prepare("DELETE FROM journals WHERE id_journal = ?");
        $stmt->bind_param("i", $journalId);
        if ($stmt->execute()) {
            $show_deleted_popup = true;
        } else {
            $logout_messages = "Delete failed!";
        }
    } catch (mysqli_sql_exception $e) {
        $logout_messages = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal It - Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="bg-customPink1 flex min-h-screen">
    <!-- Sidebar -->
    <div class="w-1/5 min-w-40 bg-white text-black flex flex-col items-center" style="padding: 20px;">
        <img src="img/logo_Journalit_square.png" class="my-4" style="width: 80px; height: 80px;">
        <nav class="flex-1 w-full">
            <ul class="w-full">
                <li class="border-b border-customPink3">
                    <a href="#" class="block px-4 py-2 font-bold hover:text-customPink3">Home</a>
                </li>
                <li class="border-b border-customPink3">
                    <a href="#journal" class="block px-4 py-2 font-bold hover:text-customPink3">Journal</a>
                </li>
            </ul>
        </nav>
        <div class="w-full mb-4">
            <form action="" method="POST">
                <button type="submit" name="logout-button" class="block px-4 py-2 font-bold text-center text-red hover:bg-customGray3">Logout</button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="w-4/5 p-8">
        <i><?= htmlspecialchars($logout_messages) ?></i>
        <div>
            <h1 class="text-4xl font-bold mb-2">Hello, <span><?= htmlspecialchars($username) ?></span>!</h1>
            <h2 id="date-display" class="mb-6"></h2>

            <!--HOWSSS-->
            <div class="bg-white rounded-2xl border-sm shadow-md">
                <div class="img-fluid w-full" style="height: 24px;"></div>
                <label class="block text-customGray2 text-center mb-2 p-2">How's your feeling today?</label>
                <div class="img-fluid w-full" style="height: 24px;"></div>
                <hr class="border-t border-customPink3">
                <div class="p-2 flex items-center">
                    <img src="img/icon_pen.png" style="width: 24px; height: 24px; margin-right: 8px;">
                    <h2 class="flex-1 bg-white text-black rounded-lg">Tell us your story today</h2>
                    <button onclick="location.href='add_journal.php'" class="p-2">
                        <img src="img/icon_arrow_right.png" style="width: 24px; height: 24px;">
                    </button>
                </div>
            </div>
   
            <div class="img-fluid w-4/5" style="height: 32px;"></div>

            <!-- journal created -->
            <div class="bg-white rounded-2xl border-sm shadow-md p-4 text-center mb-6">
                <p class="text-customGray2 text-4xl mb-2" id="journal-count"><?= count($journals) ?></p>
                <h2 class="text-customGray2">journals created</h2>
            </div>


            <!-- Search -->
            <div class="flex bg-white rounded-2xl border-sm shadow-md mb-4">
                <input id="search-input" type="text" placeholder="Search" class="w-full p-2" style="margin-left: 16px;">
                <button onclick="searchJournal()" class="p-2">
                    <img src="img/icon_search.png" style="width: 24px; height: 24px;">
                </button>
            </div>

            <!-- Popup Menu -->
            <div id="popup-menu" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
                <div class="bg-white p-4 rounded-2xl shadow-lg w-1/2">
                    <ul>
                        <li>
                            <div class="flex" style="margin-top: 12px; margin-bottom: 12px;">
                                <img src="img/icon_calendar.png" style="width: 40px; height: 40px; padding: 4px;">
                                <p id="journal-date" class="px-4 py-2 text-gray-600"></p>
                            </div>
                        </li>
                        <hr class="border-t border-customPink3">
                        <li>
                            <div class="flex" style="margin-top: 12px; margin-bottom: 12px;">
                                <img src="img/icon_pen.png" style="width: 40px; height: 40px; padding: 4px;">
                                <button 
                                    id="edit-button"
                                    class="block w-full text-left px-4 py-2 hover:text-customPink3">
                                    Edit Journal
                                </button>
                        </li>
                        <hr class="border-t border-customPink3">
                        <li>
                            <div class="flex" style="margin-top: 12px; margin-bottom: 12px;">
                                <img src="img/icon_trash.png" style="width: 40px; height: 40px; padding: 4px;">
                                <form action="" method="POST">
                                    <input type="hidden" name="journal-id" id="journal-id">
                                    <button id="delete-button" name="delete-button" type="submit" 
                                        class="block w-full text-left text-black px-4 py-2 hover:text-customPink3">
                                        Delete Journal
                                    </button>
                                </form>
                            </div>
                        </li>
                        <hr class="border-t border-customPink3">
                    </ul>
                    <button id="close-popup" 
                        class="mt-4 bg-red-500 text-black px-4 py-2 rounded hover:text-customPink3">
                        Close
                    </button>
                </div>
            </div>

            <!-- List journal -->
            <div id="journal" class="space-y-4">
                <?php 
                    $username = $_SESSION["username"];
                    $sql = "SELECT id_journal, user_name, title, description, date_created, image FROM journals WHERE user_name = '$username'";
                    $result = $db->query($sql);
                    $journals = $result->fetch_all(MYSQLI_ASSOC);
                    foreach ($journals as $journal):                    
                ?>
                <div class="bg-white p-6 rounded-lg shadow-md overflow-hidden">
                    <div class="flex items-start mb-4 justify-center">
                        <div class="text-center mr-4">
                            <?php 
                                $date = new DateTime($journal['date_created']); 
                                $imageSrc = !empty($journal['image']) ? htmlspecialchars($journal['image']) : null;
                            ?>
                            <p class="text-gray-700"><?= $date->format('D') ?></p>
                            <p class="text-2xl font-bold"><?= $date->format('d') ?></p>
                            <p class="text-gray-700"><?= $date->format('Y') ?></p>
                        </div>
                        <div class="flex-1 w-full">
                            <?php if ($imageSrc): ?>
                                <img src="<?= $imageSrc ?>" class="rounded-lg max-h-24 w-full object-cover">
                            <?php endif; ?>
                            <h2 class="text-xl font-bold mb-2"><?= htmlspecialchars($journal['title']) ?></h2>
                            <p class="w-full"><?= htmlspecialchars($journal['description']) ?></p>
                        </div>
                        <button onclick="showPopup(<?= $journal['id_journal'] ?>)" class="p-2">
                            <img src="img/icon_more.png" style="width: 24px; height: 24px; margin-right: 8px;">
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div id="popup-deleted" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
    <div class="bg-white p-4 rounded-2xl shadow-lg w-1/2 text-center">
        <p class="text-xl font-semibold">Journal Deleted</p>
        <button id="ok-button" class="bg-customPink2 hover:bg-customPink3 text-white w-3/4 py-2 px-4 rounded-full focus:outline-none focus:shadow-outline" style="margin-top: 16px">OK</button>
    </div>
</div>

<script>
    document.getElementById("date-display").innerText = new Date().toLocaleDateString('en-US', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    });

    document.getElementById("search-input").addEventListener("input", function () {
        const keyword = this.value.toLowerCase();
        const journals = document.querySelectorAll("#journal .bg-white");

        journals.forEach(journal => {
            const text = journal.innerText.toLowerCase();
            journal.style.display = text.includes(keyword) ? "block" : "none";
        });
    });

    let currentJournalId = null;

    function showPopup(journalId) {
        currentJournalId = journalId;
        document.getElementById('journal-id').value = journalId;

        const journalDateElement = document.getElementById("journal-date");
        const journal = <?= json_encode($journals) ?>.find(j => j.id_journal == journalId);
        if (journalDateElement && journal) {
            const date = new Date(journal.date_created);
            journalDateElement.textContent = date.toLocaleString('en-US', {
                weekday: 'long', year: 'numeric', month: 'numeric', day: 'numeric'
            });
        }

        const editButton = document.getElementById('edit-button');
        editButton.setAttribute("onclick", `location.href='edit_journal.php?id_journal=${journalId}'`);

        document.getElementById('popup-menu').classList.remove('hidden');
        
    }
    <?php if($show_deleted_popup): ?>
        document.getElementById('popup-deleted').classList.remove('hidden');
        <?php endif ?>

    document.getElementById('ok-button').addEventListener('click', () => {
        document.getElementById('popup-deleted').classList.add('hidden');
        window.location.href = 'home.php';
        });
        
    document.getElementById('close-popup').addEventListener('click', () => {
        document.getElementById('popup-menu').classList.add('hidden');
    });
</script>
</body>
</html>
