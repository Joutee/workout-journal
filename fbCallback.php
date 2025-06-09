<?php
session_start();
require_once __DIR__ . '/inc/db.php';

$appId = '737208485413000';
$appSecret = '5409ec2568ebd083fa9dc4bcae7fbaa4';
$redirectUri = 'https://eso.vse.cz/~kovp07/semestralka/fbCallback.php';

// Ověření CSRF
if (!isset($_GET['state']) || $_GET['state'] !== ($_SESSION['fb_state'] ?? '')) {
    exit('Neplatný stav (state).');
}

// Získání access tokenu
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $tokenUrl = "https://graph.facebook.com/v19.0/oauth/access_token?"
        . "client_id=$appId"
        . "&redirect_uri=" . urlencode($redirectUri)
        . "&client_secret=$appSecret"
        . "&code=$code";

    $response = file_get_contents($tokenUrl);
    $tokenData = json_decode($response, true);

    if (isset($tokenData['access_token'])) {
        $accessToken = $tokenData['access_token'];

        // Získání údajů o uživateli
        $userUrl = "https://graph.facebook.com/me?fields=id,name,email&access_token=$accessToken";
        $userResponse = file_get_contents($userUrl);
        $userDataFB = json_decode($userResponse, true);

        if (isset($userDataFB['id'])) {
            // Najdi uživatele podle facebook_id nebo emailu
            $query = $db->prepare('SELECT * FROM user WHERE facebook_id = :facebook_id OR email = :email LIMIT 1');
            $query->execute([

                ':facebook_id' => $userDataFB['id'],
                ':email' => $userDataFB['email'] ?? ''
            ]);
            $user = $query->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Pokud uživatel nemá uložené facebook_id, aktualizuj ho
                if (empty($user['facebook_id'])) {
                    $update = $db->prepare('UPDATE user SET facebook_id = :facebook_id WHERE user_id = :user_id');
                    $update->execute([
                        ':facebook_id' => $userDataFB['id'],
                        ':user_id' => $user['user_id']
                    ]);
                }
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_full_name'] = [$user['name'], $user['surname']];
            } else {
                // Vytvoř nového uživatele
                $nameParts = explode(' ', $userDataFB['name'], 2);
                $name = $nameParts[0] ?? '';
                $surname = $nameParts[1] ?? '';
                $insert = $db->prepare('INSERT INTO user (email, name, surname, facebook_id) VALUES (?, ?, ?, ?)');
                $insert->execute([
                    $userDataFB['email'] ?? null,
                    $name,
                    $surname,
                    $userDataFB['id']
                ]);
                $_SESSION['user_id'] = $db->lastInsertId();
                $_SESSION['user_full_name'] = [$name, $surname];
                require_once __DIR__ . '/createDefaultExcercise.php';

            }

            // Přesměrování do aplikace
            header('Location: index.php');
            exit;
        } else {
            echo 'Nepodařilo se získat údaje z Facebooku.';
        }
    } else {
        echo 'Chyba při získávání access tokenu.';
    }
} else {
    echo 'Přihlášení přes Facebook selhalo.';
}
?>