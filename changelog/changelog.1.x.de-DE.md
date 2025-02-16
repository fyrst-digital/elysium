# Version 2.0.0

## Wichtiger Hinweis
Das **2.0 Update enthält Breaking Changes**. Bitte prüfe die Aktualisierung von Version 1.x auf 2.0 in einer Staging Umgebung um dauerhaften Datenverlust zu vermeiden.
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
- Feature: Slides kann ein 'Fokus Bild' hinzugefügt werden
- Feature: Es können verschiedene Slide Cover Bilder für die Smartphone, Tablet und Desktop Ansicht hinzugefügt werden
- Feature: Eine vielzahl von Slide Einstellungen ist nun Geräteabhängig
- Verbesserung: Slide Einstellungen wurde stark erweitert
- Verbesserung: Optimierung der Slide Cover Thumbnails im Frontend (Verbesserung der Lighthouse Performance Bewertung)
- Verbesserung: Das Löschen eines Slides ist jetzt auch auf der Bearbeitungsseite möglich.
- Änderung: Die Elysium Erlebniswelt Blöcke sind nun in der Block-Kategorie 'Elysium Slider und Banner' zu finden
- Änderung: Die Slide Bearbeitungsseite wurde umstrukturiert. Dies betrifft hauptsächlich die Code Qualität. Das Formular für Medien wurde in einen eigenen Tab ausgegliedert. Die Zusatzfelder Einstellungen sind nun im Tab "Erweitert" zu finden.
- Änderung: Slide Templates und Styles wurden umstrukturiert

# `1.5.6` 

## Changelog
- Bugfix: Ein Fehler in der Slide-Auswahl des Erlebniswelten Slider Elements wurde behoben. Bei fehlender Slide Überschrift konnten keine Slides ausgewählt werden und die Slide-Auswahl wurde nicht angezeigt. Nun sollte die gesamte Slide-Auswahl, auch ohne eine hinterlegte Slide Überschrift, wie erwartet funktionieren.

# `1.5.5` 

## Changelog
- Feature: Es ist nun möglich mehrere Slides pro Ansicht anzeigen zu lassen. Bisher war die Ansicht auf einen Slide beschränkt. Im Erlebniswelten Slider Element gibt es unter **Größen** die **Slide Verhalten** Einstellungen. Es kann festgelegt werden wie viel Slides pro Ansicht angezeigt werden sollen.

# `1.5.4` 

## Changelog
- Bugfix: Ein Fehler in der Slide-Auswahl des Erlebniswelten Slider Elements wurde behoben. Bei abweichenden Sprachen konnten keine Slides ausgewählt werden und die Slide-Auswahl wurde nicht angezeigt. Nun sollte die gesamte Slide-Auswahl, in jeder ausgewählten Sprache, wie erwartet funktionieren.

# `1.5.3` 

## Changelog
- Feature: Im Erlebniswelten Slider Element kann nun die innere Container Breite des Inhalts festgelegt werden. Mögliche Optionen sind "Breite des Seiteninhalts" oder "Volle Breite".

# `1.5.2` 

## Changelog
- Änderung: Übersetzungen im Admin wurden korrigiert
- Verbesserung: Die Darstellung des Sliders wurde optimiert. In den Slider-Einstellungen gibt es nun die Möglichkeit den Innenabstand zu konfigurieren
- Verbesserung: Die Slide Auswahl im Admin wurde optimiert. Die Drag and Drop Funktion der einzelnen Slides ist nun besser erkennbar

# `1.5.1` 2023-06-15

## Changelog
- Ein Fehler wurde behoben, bei dem der Slider fehlerhaft dargestellt wurde

# `1.5.0` 2023-06-15

## Update Notes

**Änderung und Erweiterung der Erlebniswelten Slider Einstellungen**  
Neben Fehlerbehebungen bezieht sich dieses Update auf die Einstellungen des Erlebniswelten Elements. Wir haben eine Anpassung der Admin-Oberfläche vorgenommen und Optionen hinzugefügt. 

**Wichtiger Hinweis**
Durch diese Anpassungen ergeben sich auch Änderungen an der Datenstruktur des Erlebniswelten Elements. **[Bitte lies unsere Update-Hinweise](https://elysium-slider.blurcreative.de/de/documentation/update-notes#version-1-5-0)** zur Version 1.5.0, bevor du die Erweiterung aktualisierst.

## Changelog
- Feature: Es ist nun möglich eine Slider Überschrift zu vergeben
- Feature: Für das Erlebniswelt Slider-Element wurden neue Einstellungen hinzugefügt
- Änderung: Die Oberfläche der Konfigurationn des Erlebniswelt Slider-Elements wurde angepasst

# `1.4.5` 2023-05-21

## Changelog
- Änderung: Eine Code-Ausgabe im Template wurde entfernt

# `1.4.4` 2023-05-19

## Changelog
- Änderung: Die Groß- und Kleinschreibung der Slide-Cover Medien Dateiendungen wird nun ignoriert

# `1.4.3` 2023-05-19

## Changelog
- Änderung: Versions-Kompatibilität zu Shopware 6.5.0

# `1.4.2` 2023-04-19

## Changelog
- Bugfix: Die Anzeige der Slide-Cover Hintergrundbilder funktioniert nun wieder wie erwartet

# `1.4.1` 2023-03-26

## Changelog
- Änderung: Die Slider-Overlay Option ist nun Standardgemäß inaktiv

# `1.4.0` 2023-03-25

## Update Notes

**Hinweis für Entwickler**  
Das Slide Template wurde refraktoriert. Templates für Slide Komponenten befinden sich nun unter `storefront/component/blur-elysium-slide/`.
Das Template für das gesamte CMS-Element befindet sich nach wie vor unter `storefront/element/cms-element-blur-elysium-slider.html.twig`.

## Changelog
- Feature: In den Slide-Einstellungen gibt es nun den "Erweitert" Tab. Dieser wird erweiterte Einstellungen eines Slides enthalten
- Feature: Pro Slide kann eine individuelle Twig-Template Datei definiert werden. Dies befindet sich im "Erweitert" Tab der Slide-Einstellungen (#44)
- Verbesserung: Optimierung der Slide-Auswahl Ansicht im Elysium Slider CMS-Element (#55)
- Verbesserung: Optimierung und Anpassung der ACL Rollenverteilung für Admin-Benutzer (#69)
- Änderung: Das Slide Template wurde refraktoriert
- Bugfix: Der 'keine Slides vorhanden' Dialog im Elysium Slider CMS-Element erscheint nun wie erwartet (#53)
- Bugfix: Behebung falscher Thumbnail-Reihenfolge im Frontend (#57)
- Bugfix: Der Slide Button wird nun ausgeblendet wenn die URL-Overlay Option aktiv ist (#63)

# `1.3.1` 2023-02-21

## Changelog
- Feature: In Slide Überschrift werden die HTML Tags br, i, u, b, strong und span akzeptiert (#50)
- Bugfix: Title Attribut im Slide URL Overlay Template wurde korrigiert (#51 - Danke an Alexander Pankow)
- Bugfix: Text-indent im Slide URL Overlay Template ist nun ein absoluter Wert (#51 - Danke an Alexander Pankow)

# `1.3.0` 2022-12-12

## Update Notes

**Neue Slide-Auswahl im Erlebniswelt-Element**  
Die Slide-Auswahl im Elysium Slider Erlebniswelt-Element wurde überarbeitet. Ziel ist dass Shop-Manager die Slides schneller und effektiver pflegen und anordnen können. 
So gibt es eine Übersicht der ausgewählten Slides, in der Slides neu positioniert, bearbeitet oder gelöscht werden können. Auch wurde die Nutzererfahrung durch hilfreiche Dialoge und Hinweise in der Slide-Auswahl verbessert.

**Untersützung von Videos als Slide-Cover**  
Im Slide-Cover können nun auch Videos verknüpft und hochgeladen werden. Dabei werden vorerst nur `.mp4` oder `.webm` Videos angezeigt. Im Slide-Cover für Portraits können nach wie vor nur Bilder verknüpft werden. Diese Anzeige wird ignoriert sobald im Slide-Cover ein Video verknüpft ist.

**Wichtiger Hinweis**  
Wenn Slides ohne HTML-Element oder Textfarbe für Uberschriften initial gesperichert wurden, konnten diese im Nachgang nicht gespeichert werden. Dieser Fehler wurde behoben.  
Dadurch kann es aber vorkommen dass bereits gepflegte Angaben (betrifft nur HTML-Element oder Textfarbe der Überschrift) in angelegten Slides entfernt werden.  
**Es sollten daher diese Angaben in bereits angelegten Slides überprüft werden**

## Changelog
- Feature: Neue Slide-Auswahl im Erlebniswelt-Element (#11)
- Feature: Untersützung von Videos als Slide-Cover (#9)
- Bugfix: Maskieren von CSS Funktionen in `Resources/app/storefront/src/scss/_elysium-slider.scss 115:26` (#40)
- Bugfix: Double Quotes im background-image inline CSS in `Resources/views/storefront/element/blur-elysium-slide-media.html.twig` hinzugefügt (#41)
- Bugfix: Kontext-Menü Aktionen im Medien-Menü hinzugefügt (#43)
- Bugfix: Speichern des Slide Überschriften-HTML-Elements sowie -Textfarbe im Nachgang möglich (#49)