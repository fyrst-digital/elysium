# 3.6.3

## Changelog
- Lazy loading der Sektionen styles wurde entfernt und durch statisches styling per SCSS ersetzt. Es sollte nun nicht mehr zu Darstellungsfehler in Sektionen kommen
- Ein Darstellungsfehler im Admin UI wurde behoben. Es wurden Kontextmenüs im Slide Builder Beschreibungs-Editor abgeschnitten. Es sollten nun alle Menüs erreichbar sein.

# 3.6.2

## Changelog
- Es wurden Fehler in der Slide Bearbeitung behoben. Alle Slide Einstellungen sind wieder sichtbar

# 3.6.1

## Changelog
- Blöcke in Standard Sektionen können nun wieder wie gewohnt ausgewählt werden

# 3.6.0

## Changelog
- Elysium Sektion wurde hinzugefügt. Diese ist in der Erlebniswelten Sektions-Auswahl verfügbar und erweitert die Erlebniswelt um dynamisch Skalierbare Blöcke, zusammenfügen von Block-Zeilen, Änderung der optischen Block-Reihenfolge und mehr. Alle Einstellungen können seperat für die Smartphone, Tablet und Desktop Ansicht festgelegt werden
- Codebasis wurde aufgeräumt und optimiert

# 3.5.4

## Changelog
- Die Werte in Zahlen-Eingabefeldern werden nun korrekt übernommen. Die entsprechenden Komponenten wurden angepasst und sollten auch ab Shopware 6.6.4 wie erwartet funktionieren
- Anpassung von Übersetzungen

# 3.5.3

## Changelog
- Eine CSS Angabe, welche negativen Einfluss auf das Slider-Verhalten haben konnte, wurde entfernt

# 3.5.2

## Changelog
- Änderung der Registrierung des elysium-slider JS von asynchron zu statisch, damit der Slider ab Shopware 6.6.7 wieder wie erwartet funktioniert

# 3.5.1

## Changelog
- Mit dem Wert `0` kann die maximale Begrenzung (Breite / Höhe) in geräteabhängigen Einstellungen zurückgesetzt werden.
- Manuelles Erstellen der neuesten JS-Dateien für das Composer-Paackage
- Übersetzungen in der Administration wurden korrigiert

# 3.5.0

## Changelog
- **Wechsel der geräteabhängigen Einstellungen zu Mobile First Ansatz.** Diese Einstellungen sind nun optional und erben den Wert der kleineren G
eräteansicht (Mobile First Ansatz). Wenn beispielsweise eine Einstellung nur in der mobilen Ansicht gesetzt ist, wird diese für Tablet und Desktop übernommen. Das gilt für Einstellungen der Slides und der Erlebniswelt Element Slider und Banner
- **Anpassung und Optimierung der Admin UI.** Die Admin UI der Elysium-Komponenten Slides-, Slider- und Banner-Einstellungen wurde überarbeitet.  
Das Icon in geräteabhängigen Eingaben kann nun angeklickt werden, um zwischen den Ansichten zu wechseln.  
Die Eingabemasken in allen Einstellungen wurden kompakter und übersichtlicher gestaltet, um eine effektivere Bearbeitung zu ermöglichen.
- Eine Lazy Loading Option wurde dem Banner-Element hinzugefügt.
- Unterschiedliche Slide-Höhen werden nun automatisch im Erlebniswelten Slider angeglichen.
- Outline Button-Varianten wurden den Slide-Verlinkungseinstellungen hinzugefügt.
- Button-Größen wurden den Slide-Verlinkungseinstellungen hinzugefügt.
- Verbesserung des Ladeverhaltens des Sliders zur Reduzierung des kumulativen Layoutverschiebung (CLS) und des Popup-Effekts von Slides

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