<?php

// ==========================
// FUNKCJE
// ==========================

// Zamienia sekundy na format m:ss
function formatujCzas($sekundy)
{
    $minuty = floor($sekundy / 60);
    $sekundy = $sekundy % 60;

    return $minuty . ":" . str_pad($sekundy, 2, "0", STR_PAD_LEFT);
}


// Zwraca najlepsze (najkrótsze) okrążenie
function najlepszeOkrazenie($okrazenia)
{
    return min($okrazenia);
}


// Oblicza średni czas okrążenia
function srednia($okrazenia)
{
    return round(array_sum($okrazenia) / count($okrazenia));
}


// Określa kategorię zawodnika
function okreslKategorie($najlepsze)
{
    if ($najlepsze < 300) {
        return "elita";
    } elseif ($najlepsze < 360) {
        return "zaawansowany";
    } else {
        return "amator";
    }
}


// Sprawdza, czy zawodnik ma słabe okrążenie lub równe tempo
function okreslUwagi($okrazenia, $najlepsze)
{
    // Sprawdzamy słabe okrążenie
    foreach ($okrazenia as $okrążenie) {
        if ($okrążenie >= 420) {
            return "słabe okrążenie";
        }
    }

    // Sprawdzamy równe tempo
    $równeTempo = true;

    foreach ($okrazenia as $okrążenie) {
        if ($okrążenie > $najlepsze + 15) {
            $równeTempo = false;
            break;
        }
    }

    if ($równeTempo) {
        return "równe tempo";
    }

    return "";
}


// Zamienia wszystkie czasy okrążeń na format m:ss
function formatujOkrazenia($okrazenia)
{
    $czasy = [];

    foreach ($okrazenia as $okrążenie) {
        $czasy[] = formatujCzas($okrążenie);
    }

    return implode(", ", $czasy);
}


// Zwraca wszystkie informacje potrzebne do wyświetlenia zawodnika
function przygotujWynik($zawodnik)
{
    $okrazenia = $zawodnik['okrazenia'];

    if (count($okrazenia) == 0) {
        return [
            'nazwisko' => $zawodnik['nazwisko'],
            'brakOkrążeń' => true
        ];
    }

    $najlepsze = najlepszeOkrazenie($okrazenia);
    $średnia = srednia($okrazenia);
    $kategoria = okreslKategorie($najlepsze);
    $uwagi = okreslUwagi($okrazenia, $najlepsze);

    return [
        'nazwisko' => $zawodnik['nazwisko'],
        'brakOkrążeń' => false,
        'okrazenia' => formatujOkrazenia($okrazenia),
        'najlepsze' => formatujCzas($najlepsze),
        'średnia' => formatujCzas($średnia),
        'kategoria' => $kategoria,
        'uwagi' => $uwagi,
        'liczbaOkrążeń' => count($okrazenia),
        'najlepszeSekundy' => $najlepsze
    ];
}


// Dane zawodników
$zawodnicy = [
    ['nazwisko' => 'Anna Kowalska',   'okrazenia' => [312, 298, 305]],
    ['nazwisko' => 'Piotr Nowak',     'okrazenia' => [355, 430, 341]],
    ['nazwisko' => 'Marek Zieliński', 'okrazenia' => []],
    ['nazwisko' => 'Ewa Wiśniewska',  'okrazenia' => [402, 377, 395]],
];


// Liczniki
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
        body {
            font-family: sans-serif;
            margin: 2rem;
        }

        table {
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 0.4rem 0.8rem;
            text-align: left;
        }

        caption {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        tfoot td {
            font-weight: bold;
        }

        .elita {
            background: #d4f5d4;
        }

        .zaawansowany {
            background: #fff3c4;
        }

        .amator {
            background: #f5d4d4;
        }
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

            // Przygotowanie danych za pomocą funkcji
            $wynik = przygotujWynik($zawodnik);

            // Zawodnik bez ukończonych okrążeń
            if ($wynik['brakOkrążeń']) {

                echo "<tr>";
                echo "<td>" . $wynik['nazwisko'] . "</td>";
                echo "<td colspan='5'>brak ukończonych okrążeń</td>";
                echo "</tr>";

                continue;
            }

            // Aktualizacja liczby wszystkich okrążeń
            $liczbaOkrążeńŁącznie += $wynik['liczbaOkrążeń'];

            // Aktualizacja najlepszego okrążenia zawodów
            if (
                $najlepszeOkrążenieZawodów === null ||
                $wynik['najlepszeSekundy'] < $najlepszeOkrążenieZawodów
            ) {
                $najlepszeOkrążenieZawodów = $wynik['najlepszeSekundy'];
            }

            // Liczenie zawodników elity
            if ($wynik['kategoria'] == "elita") {
                $liczbaElita++;
            }

            // Wyświetlenie wiersza
            echo "<tr class='" . $wynik['kategoria'] . "'>";

            echo "<td>" . $wynik['nazwisko'] . "</td>";
            echo "<td>" . $wynik['okrazenia'] . "</td>";
            echo "<td>" . $wynik['najlepsze'] . "</td>";
            echo "<td>" . $wynik['średnia'] . "</td>";
            echo "<td>" . $wynik['kategoria'] . "</td>";
            echo "<td>" . $wynik['uwagi'] . "</td>";

            echo "</tr>";
        }

        ?>

    </tbody>

    <tfoot>

        <tr>
            <td colspan="6">

                Zawodników: <?= $liczbaZawodnikow ?> |

                Elita: <?= $liczbaElita ?> |

                Najlepsze okrążenie zawodów:
                <?= formatujCzas($najlepszeOkrążenieZawodów) ?> |

                Okrążeń łącznie:
                <?= $liczbaOkrążeńŁącznie ?>

            </td>
        </tr>

    </tfoot>

</table>

</body>
</html>