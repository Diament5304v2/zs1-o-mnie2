<?php
// Wyniki zawodów biegowych. Czasy okrążeń w sekundach, po jednym na ukończone okrążenie.
$zawodnicy = [
    ['nazwisko' => 'Anna Kowalska',   'okrazenia' => [312, 298, 305]],
    ['nazwisko' => 'Piotr Nowak',     'okrazenia' => [355, 430, 341]],
    ['nazwisko' => 'Marek Zieliński', 'okrazenia' => []],
    ['nazwisko' => 'Ewa Wiśniewska',  'okrazenia' => [402, 377, 395]],
];

// Liczniki żyją poza pętlą
$liczbaZawodnikow = 0;
$liczbaElita = 0;
$najlepszeOkrążenieZawodów = null;
$liczbaOkrążeńŁącznie = 0;
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="utf-8">
<title>Wyniki zawodów</title>
<style>
    body { font-family: sans-serif; margin: 2rem; }
    table { border-collapse: collapse; }
    th, td { border: 1px solid #999; padding: 0.4rem 0.8rem; text-align: left; }
    caption { font-weight: bold; margin-bottom: 0.5rem; }
    tfoot td { font-weight: bold; }
    .elita        { background: #d4f5d4; }
    .zaawansowany { background: #fff3c4; }
    .amator       { background: #f5d4d4; }
</style>
</head>

<body>

<h1>Zawody biegowe: czasy okrążeń</h1>

<table>
    <caption>Wyniki zawodników</caption>

    <thead>
        <tr>
            <th scope="col">Zawodnik</th>
            <th scope="col">Okrążenia</th>
            <th scope="col">Najlepsze</th>
            <th scope="col">Średnie</th>
            <th scope="col">Kategoria</th>
            <th scope="col">Uwagi</th>
        </tr>
    </thead>

    <tbody>
        <?php
        foreach ($zawodnicy as $zawodnik) {

            $liczbaZawodnikow++;

            // Obsługa zawodnika bez ukończonych okrążeń
            if (count($zawodnik['okrazenia']) == 0) {
                echo "<tr>";
                echo "<td>" . $zawodnik['nazwisko'] . "</td>";
                echo "<td colspan='5'>brak ukończonych okrążeń</td>";
                echo "</tr>";

                continue;
            }

            $okrazenia = $zawodnik['okrazenia'];

            // Aktualizacja liczby wszystkich okrążeń
            $liczbaOkrążeńŁącznie += count($okrazenia);

            // Najlepsze okrążenie zawodnika
            $najlepsze = min($okrazenia);

            // Aktualizacja najlepszego okrążenia zawodów
            if (
                $najlepszeOkrążenieZawodów === null ||
                $najlepsze < $najlepszeOkrążenieZawodów
            ) {
                $najlepszeOkrążenieZawodów = $najlepsze;
            }

            // Średnia zaokrąglona do pełnych sekund
            $średnia = round(array_sum($okrazenia) / count($okrazenia));

            // Kategoria
            if ($najlepsze < 300) {
                $kategoria = "elita";
                $liczbaElita++;
            } elseif ($najlepsze < 360) {
                $kategoria = "zaawansowany";
            } else {
                $kategoria = "amator";
            }

            // Zamiana czasów na format m:ss
            $czasy = [];

            foreach ($okrazenia as $okrążenie) {
                $minuty = floor($okrążenie / 60);
                $sekundy = $okrążenie % 60;

                $czasy[] = $minuty . ":" . str_pad(
                    $sekundy,
                    2,
                    "0",
                    STR_PAD_LEFT
                );
            }

            // Uwagi
            $uwagi = "";

            // Czy którekolwiek okrążenie trwało 7 minut lub więcej?
            foreach ($okrazenia as $okrążenie) {
                if ($okrążenie >= 420) {
                    $uwagi = "słabe okrążenie";
                    break;
                }
            }

            // Jeżeli nie ma słabego okrążenia, sprawdzamy równe tempo
            if ($uwagi == "") {
                $równeTempo = true;

                foreach ($okrazenia as $okrążenie) {
                    if ($okrążenie > $najlepsze + 15) {
                        $równeTempo = false;
                        break;
                    }
                }

                if ($równeTempo) {
                    $uwagi = "równe tempo";
                }
            }

            // Konwersja najlepszego czasu
            $najlepszeMinuty = floor($najlepsze / 60);
            $najlepszeSekundy = $najlepsze % 60;

            $najlepszeFormat = $najlepszeMinuty . ":" . str_pad(
                $najlepszeSekundy,
                2,
                "0",
                STR_PAD_LEFT
            );

            // Konwersja średniej
            $średniaMinuty = floor($średnia / 60);
            $średniaSekundy = $średnia % 60;

            $średniaFormat = $średniaMinuty . ":" . str_pad(
                $średniaSekundy,
                2,
                "0",
                STR_PAD_LEFT
            );

            echo "<tr class='$kategoria'>";
            echo "<td>" . $zawodnik['nazwisko'] . "</td>";
            echo "<td>" . implode(", ", $czasy) . "</td>";
            echo "<td>" . $najlepszeFormat . "</td>";
            echo "<td>" . $średniaFormat . "</td>";
            echo "<td>" . $kategoria . "</td>";
            echo "<td>" . $uwagi . "</td>";
            echo "</tr>";
        }
        ?>
    </tbody>

    <tfoot>
        <?php
        $najlepszeMinuty = floor($najlepszeOkrążenieZawodów / 60);
        $najlepszeSekundy = $najlepszeOkrążenieZawodów % 60;

        $najlepszeZawodówFormat = $najlepszeMinuty . ":" . str_pad(
            $najlepszeSekundy,
            2,
            "0",
            STR_PAD_LEFT
        );
        ?>

        <tr>
            <td colspan="6">
                Zawodników: <?= $liczbaZawodnikow ?> |
                Elita: <?= $liczbaElita ?> |
                Najlepsze okrążenie zawodów: <?= $najlepszeZawodówFormat ?> |
                Okrążeń łącznie: <?= $liczbaOkrążeńŁącznie ?>
            </td>
        </tr>
    </tfoot>
</table>

</body>
</html>
