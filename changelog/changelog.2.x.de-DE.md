# unreleased - Version 2.1.0

## Changelog

- Verbesserung: Eine Post Update Konvertierung der Slide und Slider Einstellungen wurde hinzugefügt. Bei der Aktualisierung von Version 1.5 auf 2.1 werden somit Slide und Slider Einstellungen automatisch übernommen.
Hinweis: Daten aus Versionen kleiner als 1.5 werden **nicht übernommen**. Wir empfehlen außerdem in jedem Fall **vor dem Update eine Datenbank Sicherung** anzulegen

# Version 2.0.0

## Wichtiger Hinweis
Das **2.0 Update enthält Breaking Changes**. Bitte prüfe die Aktualisierung von Version 1 auf 2.0 in einer Staging Umgebung um dauerhaften Datenverlust zu vermeiden.
Diese Version enthält tiefgreifende, strukturelle Veränderungen. Diese Änderungen sind im Hinblick auf eine effiziente und zukunftssichere Weiterentwicklung der Elysium Erweiterung unvermeidbar gewesen.

## Update Notes

### Banner Erlebniswelt-Element wurde hinzugefügt  
Slides können nun in einem Banner Erlebniswelt Element aufbereitet und einzeln ausgespielt werden. Zusätzlich wurden zwei weitere Block Elemente hinzugefügt. Diese befinden sich in der neuen Block Kategorie **Elysium Slider und Banner**.  

Der **Elysium Banner** Block ist für die Darstellung eines einzelnen Banner gedacht.  
Der **Elysium Block — 2 Spalten** ist für die Darstellung von zwei Bannern optimiert. Es können aber auch andere Erlebniswelt Elemente in diesem Block verwendet werden. Das besondere an diesem Block sind erweiterte Darstellungsoptionen für die Smartphone, Tablet und Desktop Ansicht. Diese Block-Einstellungen finden sich in der Sidebar des Erlebniswelten Layout Designers.  

### Erweiterung der Elysium Slides Konfiguration
Die Konfiguration der Elysium Slides wurde grundlegend neu strukturiert und erweitert. Neben vielen neuen Optionen zur Darstellung kann nun auch ein **Fokus Bild** verwendet werden. Dieses Fokus Bild wird neben den Inhaltsbereich und losgelöst vom Slide Cover angezeigt.  
Auch wurden die Slide Cover Bilder verbessert. Es können für die Smartphone, Tablet oder Desktop Ansicht verschiedene Bilder festgelegt werden.  

Zudem kann, neben der individuellen Verlinkung, nun auch ein Produkt verknüpft werden. Es werden dann automatisch die Produktinformationen wie Bezeichnung, Beschreibung und Bild angezeigt. Diese Informationen können aber optional vom Slide überschrieben werden, indem man im Slide zum Beispiel die Slide Überschrift oder das Fokus Bild einsetzt.

### Konsistente Einstellungen für Smartphone, Tablet und Desktop
In den Einstellugnen von Slides, Slider- und Banner- Elementen findet sich nun eine einheitliche Konfiguration für Smartphone, Tablet und Desktop Ansicht.  
Erkennbar an den entsprechenden Geräte-Icons. Mit Klick auf ein Geräte-Icon kann die Konfiguration für diese Ansicht speziell optimiert werden. Welche Einstellungen Geräteabhängig sind, erkennst du an einem entsprechenden Indikator unterhalb einer Option.  

Weiter können die Gerätegrößen, also ab welcher Bildschirmbreite welche Ansicht zum tragen kommt, vom Nutzer angepasst werden. Die Gerätegrößen können unter 'Einstellungen → Erweiterungen → Elysium Slider' eingestellt werden.

### Optimierung der Slide Templates und Styles
Die Template Struktur sowie CSS Styles von Slides wurde überarbeitet und logischer gegliedert. Falls du eigene Templates verwendest, prüfe diese auf entsprechende Änderungen.

## Changelog
- Feature: Banner Erlebniswelt Element wurde hinzugefügt
- Feature: Erlebniswelt Block 'Elysium Banner' wurde hinzugefügt
- Feature: Erlebniswelt Block 'Elysium Block — 2 Spalten' wurde hinzugefügt
- Feature: Geräteabhängige Einstellungen wurden den Erlebniswelt Elementen 'Slider' und 'Banner' hinzugefügt
- Feature: Geräteabhängige Einstellungen wurden den Slide Einstellungen hinzugefügt
- Feature: Slides können nun kopiert werden
- Feature: Slides können ein 'Fokus Bild' hinzugefügt werden
- Feature: Es können verschiedene Slide Cover Bilder für die Smartphone, Tablet und Desktop Ansicht hinzugefügt werden
- Feature: Eine vielzahl von Slide Einstellungen ist nun Geräteabhängig
- Verbesserung: Slide Einstellungen wurden stark erweitert
- Verbesserung: Optimierung der Slide Cover Thumbnails im Frontend (Verbesserung der Lighthouse Performance Bewertung)
- Verbesserung: Das Löschen eines Slides ist jetzt auch auf der Bearbeitungsseite möglich.
- Änderung: Die Elysium Erlebniswelt Blöcke sind nun in der Block-Kategorie 'Elysium Slider und Banner' zu finden
- Änderung: Die Slide Bearbeitungsseite wurde umstrukturiert. Dies betrifft hauptsächlich die Code Qualität. Das Formular für Medien wurde in einen eigenen Tab ausgegliedert. Die Zusatzfelder Einstellungen sind nun im Tab "Erweitert" zu finden.
- Änderung: Slide Templates und Styles wurden umstrukturiert