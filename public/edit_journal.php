<?php
include "service/database.php";
session_start();
$edit_massage = "";
$image_path = "";
$show_updated_popup = false;

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$id_journal = $_GET['id_journal'] ?? null;

if (!$id_journal) {
    echo "Journal ID is missing.";
    exit();
}

try {
    $sql = "SELECT * FROM journals WHERE id_journal = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $id_journal);
    $stmt->execute();
    $result = $stmt->get_result();
    $journal = $result->fetch_assoc();

    if (!$journal) {
        echo "Journal not found.";
        exit();
    }
} catch (mysqli_sql_exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

if (isset($_POST['save-button'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $username = $_SESSION['username'];

    if (isset($_FILES['images']) && $_FILES['images']['error'] == 0) {
        $image = $_FILES['images'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image["name"]);
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            $image_path = $target_file;
        } else {
            $edit_massage = "Failed to upload image.";
        }
    } else {
        $image_path = $journal['image'];
    }

    try {
    $sql = "UPDATE journals 
        SET title = ?, description = ?, date_created = ?, user_name = ?, image = ?
        WHERE id_journal = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("sssssi", $title, $description, $date, $username, $image_path, $id_journal);

    if ($stmt->execute()) {
            $show_updated_popup = true;
        } else {
            $edit_massage = "Failed to save journal.";
        }
    } catch (mysqli_sql_exception $e) {
        $edit_massage = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Journal</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="mx-auto max-w-4xl p-8">
<i><?= $edit_massage ?></i>
    <div class="flex justify-between">
        <button id="back-button" onclick="location.href='home.php'" class="block hover:text-customPink3">
            <div class="flex" style="margin-top: 12px; margin-bottom: 12px;">
                <img src="img/icon_arrow_left.png" style="width: 32px; height: 32px; padding: 4px; margin-left: 8px; margin-right: 8px;">
                <h1 class="text-left text-2xl" style="margin-top: 0px;">Back</h1>
            </div>
        </button>
        <button id="save-button" name="save-button" type="submit" form="journal-form" class="hidden">
            <img src="img/icon_save.png" style="width: 40px; height: 40px; padding: 4px; margin-left: 8px; margin-right: 8px;">
        </button>   
        <button id="edit-button" type="button" onclick="toggleEditMode(true)">
            <img src="img/icon_edit.png" style="width: 40px; height: 40px; padding: 4px; margin-left: 8px; margin-right: 8px;">
        </button>   
    </div>
    
    <div>
        <form id="journal-form" action="edit_journal.php?id_journal=<?= $id_journal ?>" method="POST" enctype="multipart/form-data">
            <input id="journal-title" name="title" required type="text" value="<?= htmlspecialchars($journal['title']) ?>" class="appearance-none focus:outline-none border-none w-full py-2 px-3 text-customGray3 text-4xl font-bold leading-tight" placeholder="Put your title here">
            <textarea id="journal-description" name="description" required rows="18" class="w-full text-gray-700 border-none p-4 focus:outline-none" placeholder="How's your feeling today?"><?= htmlspecialchars($journal['description']) ?></textarea>
            
            <div id="gallery-preview" class="flex space-x-4">
                <?php if ($journal['image']): ?>
                    <img src="<?= htmlspecialchars($journal['image']) ?>" alt="Journal Image" class="rounded-lg max-h-24 object-cover">
                <?php endif; ?>
            </div>

            <div id="edit-tools" class="flex flex-col items-center hidden">
                <div class="border-none shadow-lg rounded-lg flex justify-center" style="width: 240px;">
                    <label id="gallery-button" class="cursor-pointer">
                        <img src="img/icon_album.png" style="width: 40px; height: 40px; padding: 4px; margin-left: 8px; margin-right: 8px;"> 
                        <input type="file" name="images" id="journal-images" accept="image/*" class="hidden">
                    </label>
                    <input id="journal-date" name="date" class="text-gray-700 pb-2 focus:outline-none" type="date">
                </div>
            </div>
        </form>
    </div>

    <div id="popup-updated" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-4 rounded-2xl shadow-lg w-1/2 text-center">
            <p class="text-xl font-semibold">Journal Updated</p>
            <button id="ok-button" class="bg-customPink2 hover:bg-customPink3 text-white w-3/4 py-2 px-4 rounded-full focus:outline-none focus:shadow-outline" style="margin-top: 16px">OK</button>
        </div>
    </div>

    <div id="popup-max-image" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-4 rounded-2xl shadow-lg w-1/2 text-center">
            <p class="text-xl font-semibold">Max 1 image only</p>
            <button id="close-button" class="bg-customPink2 hover:bg-customPink3 text-white w-3/4 py-2 px-4 rounded-full focus:outline-none focus:shadow-outline" style="margin-top: 16px">Close</button>
        </div>
    </div>

    <script>
        function toggleEditMode(isEdit) {
            const inputs = document.querySelectorAll('#journal-title, #journal-description, #journal-images, #journal-date');
            const saveButton = document.getElementById('save-button');
            const editButton = document.getElementById('edit-button');
            const editTools = document.getElementById('edit-tools');

            inputs.forEach(input => input.disabled = !isEdit);
            editTools.style.display = isEdit ? 'block' : 'none';
            saveButton.style.display = isEdit ? 'inline-block' : 'none';
            editButton.style.display = isEdit ? 'none' : 'inline-block';
        }

        document.addEventListener('DOMContentLoaded', () => {
            toggleEditMode(false);
        });

        const today = new Date().toISOString().split('T')[0];
        document.getElementById('journal-date').value = today;

        document.getElementById('ok-button').addEventListener('click', () => {
            window.location.href = 'home.php';
        });
        
        document.getElementById('close-button').addEventListener('click', () => { 
            document.getElementById('popup-max-image').classList.add('hidden');
        });

        <?php if($show_updated_popup): ?>
        document.getElementById('popup-updated').classList.remove('hidden');
        <?php endif ?>

        let imageAdded = false;

        document.getElementById('journal-images').addEventListener('change', (e) => {
            const fileInput = e.target;
            const file = fileInput.files[0];
            const galleryPreview = document.getElementById('gallery-preview');

            if (file) {
                if (imageAdded) {
                    document.getElementById('popup-max-image').classList.remove('hidden');
                    return;
                }

                const imageUrl = URL.createObjectURL(file);
                const container = document.createElement('div');
                container.classList.add('relative', 'flex', 'items-start');
                container.innerHTML = `
                    <img src="${imageUrl}" alt="Image preview" class="w-4/5 h-24 object-cover">
                    <button type="button" class="absolute top-0 right-0 text-black" onclick="removeImage(this)">
                        <img src='img/icon_cross.png' style="width: 24px; height: 24px" class="flex items-start">
                    </button>
                `;
                galleryPreview.innerHTML = '';
                galleryPreview.appendChild(container);
                imageAdded = true;
            }
        });

        function removeImage(button) {
            const previewContainer = document.getElementById('gallery-preview');
            previewContainer.innerHTML = '';
            document.getElementById('journal-images').value = '';
            imageAdded = false;
        }
    </script>
</body>
</html>