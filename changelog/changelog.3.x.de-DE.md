# 3.4.1

## Changelog
- Ein Fehler in der Slide Einstellung (Anzeige) "Elemente untereinander anzeigen" wurde behoben. Die Option kann nur wieder korrekt angewählt werden und funktioniert wie erwartet
- Dem Fokusbild Element wurden CSS styles hinzugefügt die eine Überschneidung mit dem Container verhindern

# 3.4.0

## Changelog
- Das Produktbild eines Slides kann nun beim Verlinkungstyp 'Produkt' ausgeblendet werden
- Im CMS Banner Element kann nun eine maximale Höhe festgelegt werden

# 3.3.0

## Changelog
- Änderung: Aufgrund einer Änderung des State Managers ab Shopware 6.6.4, kam es beim einfügen von Elysium Blöcken im Erlebniswelten Layout Editor zu Fehlern. Dies wurde angepasst und das einfügen von Blöcken sollte wie erwartet funktionieren.

# 3.2.1

## Changelog
- Änderung: Die SQL Syntax der Datenbank Migration 1707906587 wurde geändert um ältere MySQL und MariaDB Versionen zu unterstützen. **Wichtiger Hinweis** Ab Version 4 werden ausnahmslos die von Shopware empholenen Datenbank Versionen unterstützt

# 3.2.0

## Changelog
- Verbesserung: In der Medienverwaltung wird nun die Information angezeigt, in welchem Elysium Slide ein Medium verwendet wird. Beim löschen eines verknüpften Mediums erscheint entsprechender Hinweis 
- Verbesserung: Rollen Berechtigungen wurden im Elysium Slides Mobul hinzugefügt
- Verbesserung: In den Slide Einstellung wurde die Option **Bild auf volle Breite strecken** dem Bildelement hinzugefügt
- Änderung: Die Einstellung **Auto-Wiedergabe Intervall** im CMS Slider hat nun einen Minimalwer von 200 statt 3000
- Änderung: Das Fokusbild wird nun standardmäßig in automatischer satt in voller Breite angezeigt
- Fehlerbehebung: Die HTML-Tags i, u, b, strong, br und span werden nun wieder wie erwartet im Frontend angezeigt
- Fehlerbehebung: Korrektur von CSS Klassennamen in CMS Blöcken. Daraus ergeben sich Fehlerbehebungen im Styling
- Fehlerbehebung: Korrektur des Seitenverhältnis. Wenn der Inhalt des Slides das Seitenverhätlis überschreiten, passt sich die Slide Höhe entsprechend dem Inhalt an. Somit wird der Inhalt nicht mehr abgeschnitten
- Fehlerbehebung: Textbausteine in der Administration wurden korrigiert

# Version 3.1.1

## Changelog
- Fehlerbehebung: Die UI-Icons in der Adminstration wurden angepasst. Diese werden auch ab Shopware 6.6.2 wieder korrekt angezeigt.

# Version 3.1.0

## Changelog
- Verbesserung: Beim speichern eines Slides wird nun der Cache aller Erlebniswelten, welche ein Elysium Element zugewiesen haben, invalidiert. Somit werden Änderungen am Slide im Storefront sofort sichtbar, ohne den Cache löschen zu müssen
- Fehlerbehebung: Ein Darstellungsfehler, bei dem es zum Überlauf der Boxen im Elysium Block 'Elysium Block — 2 Spalten' kommen konnte, wurde behoben

# Version 3.0.1

## Changelog
- Fehlerbehebung: Die Slide Beschreibung wird nun wie erwartet gespeichert
- Fehlerbehebung: Korrektur von Textbausteinen in der Administration

# Version 3.0.0

## Update Notes
Dieses Update stellt die Kompatibilität mit Shopware 6.6 her. Mit dieser Version ändert sich der Plugin Support. Version 3 erhält Funktionserweiterungen und Fehlerbehebungen. Version 2 erhält ausschließlich Fehlerbehebungen. Version 1 wird nicht mehr unterstützt und erhält keine weiteren Aktualisierungen.  

Es wurde der gesamte Code innherhalb der Administration angepasst. Wir haben dabei den Code minimiert und auf Verbessserung der Performance sowie der Nutzererfahrung geachtet. 

## Changelog
- Verbesserung: Aktualisierung und Anpassungen der Administrations Komponenten
- Verbesserung: Der JavaScript Code der Slider im Storefront wird nun dynamisch geladen