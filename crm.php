<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<h1>Moduł CRM</h1>

<h2>Operacje na Klientach</h2>
    
    <h3>Dodaj Klienta</h3>
    <form action="crm.php" method="get">
        <input type="hidden" name="action" value="add">
        Imię: <input type="text" name="imie" required><br>
        Adres e-mail: <input type="email" name="email" required><br>
        Status subskrypcji: <select name="status_subskrypcji" id="status" required>
            <option value="AKTYWNA">aktywna</option>
            <option value="NIEAKTYWNA">nieaktywna</option>
        </select><br>
        <button type="submit">Dodaj Klienta</button>
    </form>
    <?php
function generujIdentyfikator() {
    return uniqid();
}

if (isset($_GET["action"]) && $_GET["action"] == "add") {
    if (isset($_GET["imie"]) && isset($_GET["email"]) && isset($_GET["status_subskrypcji"])) {
        $imie = $_GET["imie"];
        $email = $_GET["email"];
        $status_subskrypcji = $_GET["status_subskrypcji"];
        $klient = generujIdentyfikator() . ",$imie,$email,$status_subskrypcji" . PHP_EOL;
        file_put_contents("klienci.txt", $klient, FILE_APPEND);
    }
}
?>
<h3>Wyświetlanie klientów</h3>
<form action="crm.php" method="get">
    <input type="hidden" name="action" value="show_list">
    <button type="submit">Wyświetl Listę Klientów</button>
</form>
<?php

if (isset($_GET["action"]) && $_GET["action"] == "show_list") {
    if (file_exists("klienci.txt")) {
        $klienci = file("klienci.txt", FILE_IGNORE_NEW_LINES);
        echo "<h2>Lista Klientów</h2>";
        if (!empty($klienci)) {
            echo "<ul>";
            foreach ($klienci as $klient) {
                $dane = explode(",", $klient);
                echo "<li>ID: {$dane[0]} | Imię: {$dane[1]} | E-mail: {$dane[2]} | Status Subskrypcji: {$dane[3]}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Brak klientów.</p>";
        }
    } else {
        echo "<p>Brak klientów.</p>";
    }
}
?>
<h3>Aktualizuj Klienta</h3>
    <form action="crm.php" method="get">
        <input type="hidden" name="action" value="update">
        Identyfikator klienta: <input type="text" name="identyfikator" required><br>
        Nowe Imię: <input type="text" name="nowe_imie" required><br>
        Nowy adres e-mail: <input type="email" name="nowy_email" required><br>
        Nowy status subskrypcji:<select name="nowy_status_subskrypcji" id="status" required>
            <option value="AKTYWNA">aktywna</option>
            <option value="NIEAKTYWNA">nieaktywna</option>
        </select><br>
        <button type="submit">Aktualizuj Klienta</button>
    </form>
<?php

if (isset($_GET["action"]) && $_GET["action"] == "update") {
    if (isset($_GET["identyfikator"]) && isset($_GET["nowe_imie"]) && isset($_GET["nowy_email"]) && isset($_GET["nowy_status_subskrypcji"])) {
        $identyfikator = $_GET["identyfikator"];
        $nowe_imie = $_GET["nowe_imie"];
        $nowy_email = $_GET["nowy_email"];
        $nowy_status_subskrypcji = $_GET["nowy_status_subskrypcji"];

        if (file_exists("klienci.txt")) {
            $klienci = file("klienci.txt", FILE_IGNORE_NEW_LINES);
            $zaktualizowany_klient = false;

            foreach ($klienci as &$klient) {
                $dane = explode(",", $klient);
                if ($dane[0] == $identyfikator) {
                    $klient = "$identyfikator,$nowe_imie,$nowy_email,$nowy_status_subskrypcji";
                    $zaktualizowany_klient = true;
                    break;
                }
            }

            if ($zaktualizowany_klient) {
                file_put_contents("klienci.txt", implode(PHP_EOL, $klienci));
                echo "<p>Klient został zaktualizowany.</p>";
            } else {
                echo "<p>Nie znaleziono klienta o podanym identyfikatorze.</p>";
            }
        } else {
            echo "<p>Brak klientów.</p>";
        }
    }
}
?>
    <h3>Usuń klienta:</h3>
    <form action="crm.php" method="get">
        <input type="hidden" name="action" value="delete">
        Identyfikator klienta: <input type="text" name="identyfikator" required><br>
        <button type="submit">Usuń klienta</button>
    </form>
<?php

if (isset($_GET["action"]) && $_GET["action"] == "delete") {
    if (isset($_GET["identyfikator"])) {
        $identyfikator = $_GET["identyfikator"];

        if (file_exists("klienci.txt")) {
            $klienci = file("klienci.txt", FILE_IGNORE_NEW_LINES);
            $usuniety_klient = false;

            foreach ($klienci as $key => $klient) {
                $dane = explode(",", $klient);
                if ($dane[0] == $identyfikator) {
                    unset($klienci[$key]);
                    $usuniety_klient = true;
                    break;
                }
            }

            if ($usuniety_klient) {
                file_put_contents("klienci.txt", implode(PHP_EOL, $klienci));
                echo "<p>Klient został usunięty.</p>";
            } else {
                echo "<p>Nie znaleziono klienta o podanym identyfikatorze.</p>";
            }
        } else {
            echo "<p>Brak klientów.</p>";
        }
    }
}
?>

<h3>Pobierz adresy klientów do pliku:</h3>
    <form action="crm.php" method="get">
    <input type="hidden" name="action" value="get_emails">
        <button type="submit">Pobierz adresy e-mail</button>
    </form>

<?php

if (isset($_GET["action"]) && $_GET["action"] == "get_emails") {
    if (file_exists("klienci.txt")) {
        $klienci = file("klienci.txt", FILE_IGNORE_NEW_LINES);
        $adresy_email = array();

        foreach ($klienci as $klient) {
            $dane = explode(",", $klient);
            $adresy_email[] = $dane[2]; 
        }

        
        if (!empty($adresy_email)) {
            file_put_contents("adresy.txt", implode(PHP_EOL, $adresy_email));
            echo "<p>Adresy e-mail klientów zostały zapisane do pliku adresy.txt.</p>";
        } else {
            echo "<p>Brak klientów lub brak adresów e-mail.</p>";
        }
    } else {
        echo "<p>Brak klientów.</p>";
    }
}
?>
</body>
</html>

