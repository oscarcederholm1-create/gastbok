
<?php
$fil = __DIR__ . "/inlägg.json";
$status = "";

// Skapa JSON-filen om den inte finns
if (!file_exists($fil)) {
    file_put_contents($fil, "[]");
}

// Läs tidigare inlägg
$json = file_get_contents($fil);
$inlagg = json_decode($json, true);

if (!is_array($inlagg)) {
    $inlagg = [];
}

// Hantera formuläret när besökaren trycker på skicka
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $homepage = trim($_POST["homepage"] ?? "");
    $comment = trim($_POST["comment"] ?? "");

    if ($name === "" || $comment === "") {
        $status = "Du måste fylla i namn och meddelande.";
    } else {
        $nyttInlagg = [
            "name" => $name,
            "email" => $email,
            "homepage" => $homepage,
            "comment" => $comment,
            "time" => date("Y-m-d H:i:s")
        ];

        // Lägg till det nya inlägget
        $inlagg[] = $nyttInlagg;

        // Gör om alla inlägg till JSON
        $nyJson = json_encode(
            $inlagg,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );

        // Spara JSON till fil
        if ($nyJson !== false &&
            file_put_contents($fil, $nyJson, LOCK_EX) !== false) {
            $status = "Inlägget har sparats!";
        } else {
            $status = "Fel: kunde inte spara inlägget.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oscars bok</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 700px;
            margin: 40px auto;
            padding: 20px;
            background: #f4f4f4;
        }

        h1 {
            text-align: center;
        }

        form, .inlagg {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        label {
            display: block;
            margin-top: 15px;
        }

        input, textarea, button {
            box-sizing: border-box;
            width: 100%;
            padding: 10px;
            margin-top: 5px;
        }

        textarea {
            min-height: 120px;
        }

        button {
            margin-top: 20px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .status {
            padding: 10px;
            background: #e0f2fe;
            border-radius: 5px;
        }

        .tid {
            color: #666;
            font-size: 0.9em;
        }

        .meddelande {
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }
    </style>
</head>
<body>

    <h1>Oscars bok</h1>
    <p>Välkommen! Skriv en hälsning i min gästbok.</p>

    <?php if ($status !== ""): ?>
        <p class="status">
            <?= htmlspecialchars($status, ENT_QUOTES, "UTF-8") ?>
        </p>
    <?php endif; ?>

    <form action="index.php" method="post">
        <label for="name">Namn:</label>
        <input type="text" id="name" name="name"
               maxlength="60" required>

        <label for="email">E-post:</label>
        <input type="email" id="email" name="email"
               maxlength="50">

        <label for="homepage">Hemsida (valfritt):</label>
        <input type="url" id="homepage" name="homepage"
               maxlength="200">

        <label for="comment">Meddelande:</label>
        <textarea id="comment" name="comment"
                  maxlength="500" required></textarea>

        <button type="submit">Skriv i gästboken</button>
    </form>

    <h2>Tidigare inlägg</h2>

    <?php foreach (array_reverse($inlagg) as $post): ?>
        <article class="inlagg">
            <h3>
                <?= htmlspecialchars($post["name"] ?? "", ENT_QUOTES, "UTF-8") ?>
            </h3>

            <p class="tid">
                <?= htmlspecialchars($post["time"] ?? "", ENT_QUOTES, "UTF-8") ?>
            </p>

            <p class="meddelande"><?=
                htmlspecialchars($post["comment"] ?? "", ENT_QUOTES, "UTF-8")
            ?></p>
        </article>
    <?php endforeach; ?>

</body>
</html>
