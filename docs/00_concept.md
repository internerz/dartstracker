# Darts Tracker
Darts Tracker ist eine Web-App, die es einem ermöglicht, Dart-Spiele zu notieren und Statistiken über seine Spiele zu verfolgen. 

## Zielplattformen
Als Web-App läuft der Darts Tracker im Browser und damit auf allen Geräten, die einen grafischen Browser besitzen. Hauptsächlich ist es für User gedacht, die Smartphone oder Tablet Stift und Zettel vorziehen. Entsprechend ist die Web-App für Toucheingaben und kleine Displays optimiert. 

## Spielmodi
### 501/301
Klassischerweise wird 501 (oder 301) gedartet, in dem man von 501 Punkten herunterspielt. Welcher Spieler zuerst 0 Punkte erreicht, gewinnt. Besonderheiten sind dabei die Spielvarianten. 

- Straight Out: zum Beenden darf ein beliebiges Feld getroffen werden.
- Double Out: zum Beenden muss ein Double-Feld getroffen werden.
- Master Out: wie Double Out, zusätzlich darf auch mit einem Wurf in ein Triple-Feld beendet werden.
- Double In: bei Beginn des Spiels muss ein beliebiges Double-Feld getroffen werden, erst ab dann zählen die geworfenen Punkte (inklusive des geworfenen Doubles).
- Triple In: bei Beginn des Spiels muss ein beliebiges Triple-Feld getroffen werden, erst ab dann zählen die geworfenen Punkte (inklusive des geworfenen Triples).

Siehe https://de.wikipedia.org/wiki/Darts#301.2F501

### Cricket
Als weiterer Spielmodus wird Cricket unterstützt. Hier gilt es, die Zahlen von 20 bis 15 sowie das Bullseye drei Mal zu treffen. 

## Features
Über die Darstellung des Dartboards kann ausgewählt werden, welche Punkte geworfen wurden. 

![Dartboard](https://upload.wikimedia.org/wikipedia/commons/4/42/Dartboard.svg)

Dartboard (https://commons.wikimedia.org/wiki/File:Dartboard.svg)

### Spieler / Teams
Beim Öffnen der App kann ausgewählt werden, wie viele Spieler an einer Partie teilnehmen und ob sie in Teams aufgeteilt sind. Normalerweise spielen 2 Spieler gegeneinander, es können aber auch mehrere Spieler ausgewählt werden. 

### Statistiken
Es können sich diverse Statistiken angeschaut werden: 
- Durchschnittspunkte pro Runde
- Höchstpunkte in einem Leg
- mehr als 100 Punkte mit 3 Darts
- mehr als 140 Punkte mit 3 Darts
- Anzahl 180 Punkte mit 3 Darts

## Umsetzung
Die Web-App inkl. der dahinter liegenden Website soll mit einem **MVC-Framework** umgesetzt werden. Das Arbeiten mit dem MVC Pattern hat den Vorteil, dass Code sauber getrennt werden kann. Außerdem kann so auch Backend und Frontend für die Zusammenarbeit bzw. Aufteilung in der Gruppe klarer voneinadner getrennt werden. 

### Framework
Zum Einsatz kommen soll das PHP Framework **Laravel 5.3**. Dieses ist neben Zend und Symfony am weitesten verbreitet, bietet aber im Vergleich zu Zend moderner und soll nicht so komplex wie Symfony sein. 

### Template Engine
Laravel arbeitet mit der Template Engine **Blade**, mit der man die Views erstellen kann, die dann durch die Controller befüllt werden. 

### CSS-Präprozessor
Um effiktiver CSS zu schreiben, soll der CSS-Präprozessor Sass mit der SCSS-Syntax zum Einsatz kommen. Dadurch können Styles in einzelne Dateien ausgelagert, einfache Kontrollstrukturen, Funktionen und Variablen genutzt werden. Die einzelnen Dateien werden dann zu einer optimierten CSS-Datei gerendert.
