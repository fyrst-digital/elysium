# 4.5.0

**Changelog**
- Umfassende Überarbeitung des Slide Builder Editors
- Eine Vorschau wurde im Slide Builder Editor hinzugefügt

# 4.4.2

**Changelog**
- Aktualisierungsfehler aus Version 4.4.1 wurde behoben. Eine neue Migration wurde hinzugefügt.

# 4.4.1

**Changelog**
- Kompatibilität der Shopware 6.7 Migration für Produkt-Fremdschlüsselreferenzen behoben
- Composite Constraint Key zur Migration1706795531 für Neuinstallationen hinzugefügt
- product_version_id Feld zur ElysiumSlidesDefinition Entity hinzugefügt

# 4.4.0

**Changelog**
- Fading-Effekt zu den Slider Optionen hinzugefügt
- Sortierung der Namensspalte in der Slide-Übersicht korrigiert
- Leere Slide-Namen und Titel in der Administration-Suche behoben

# 4.3.3

**Changelog**
- Navigation im Balken-Stil wird nun korrekt dargestellt
- Leere Breakpoint-Werte im Slide-Cover wurden behoben

# 4.3.2

**Changelog**
- Ein Darstellungsproblem in der Erlebniswelten Sidebar wurde behoben

# 4.3.1

**Changelog**
- Vertikale Abstände im "Volle Breite"-Modus innerhalb von Elysium Sektionen wurden entfernt
- Slides können nun wieder ohne Probleme kopiert werden
- Elysium Blöcke in der Elerbniswelten Sidebar können wieder gescrolled werden

# 4.3.0

**Hinweis für Entwickler**
Wir haben größere Anpassungen am Slider-Template, sowie CSS-Styling vorgenommen. Geändert haben sich Template-Pfade, Twig Blöcke, CSS-Klassennamen und CSS Variablen. 
Im Template `src/Resources/views/storefront/element/cms-element-blur-elysium-slider.html.twig` sind die neuen Strukturen zu finden.
Unter `src/Resources/app/storefront/src/scss/_elysium-slider.scss` ist das neue CSS-Styliing zu finden

**Changelog**
- Neue Slider Option: Der HTML-Tag der Slider-Überschrift kann nun festgelegt werden. Neben 'DIV' sind 'H1' bis 'H6' Elemente möglich
- Neue Slider Option: Die Slider-Überschrift kann nun visuell aus- oder eingeblendet werden
- Neue Slider Option: Es kann nun festgelegt werden ob sich Slides in der Höhe angleichen sollen
- Neue Slider Option: In den Slider-Pfeilen kann nun der Außenabstand festgelegt werden
- Überarbeitung des Slider Templates, sowie Optimierung von A11y-Markup
- CSS Klassen und Variablen wurden geändert und angepasst
- Splide Slider-Bibliothek wurde durch Swiper ersetzt
- Ein Fehler während der Installation unter Shopware 6.7.1 wurde behoben

# 4.2.3

**Changelog**
- Es wurde ein Fehler behoben, der verhinderte, dass Elysium Slider Elementen unterschiedliche Slides zugewiesen werden konnten

# 4.2.2

**Changelog**
- Die Elysium Sektion und Block Einstellungen in der Erlebniswelten Sidebar können nun wieder angewählt werden.
- Das Icon in der Erlebniswelten Sidebar wurde angepasst um die Elysium Sektion bezogenen Einstellungen besser zu erkennen

# 4.2.1

**Changelog**
- Datenbankmigration 1744028421SetDefaultMediaFolderId wurde optimiert, welche zu einem Fehler beim Upgrade von Shopware 6.6 auf 6.7 führen konnte. 

# 4.2.0

**Changelog**
- Im Erlebniswelten Layot Editor wurde die Slide Vorschau verbessert und bilden nun besser das Resultat im Storefront ab.
- `ElysiumCmsSlidesResultEvent` und `ElysiumCmsSlidesCriteriaEvent` wurden hinzugefügt. Diese werden beim Aufruf der entsprechenden Erlebniswelt Elemente ausgelöst.
- Code- und Typescript-Optimierungen der Admin app. Eine Slide Preview Komponente wurde hinzugefügt

# 4.1.0

**Changelog**
- Alt und Titel Attribute wurden dem Slide Cover hinzugefügt.Zusätzlich können in den Slide Cover Einstellungen alternative Alt und Titel Texte vergeben werden
- Slider und Banner Elementen können nun CSS Klassen vergeben werden

# 4.0.0

## Release notes
Kompatibilitäts-Update zu Shopware 6.7

**Changelog**
- Vollständiger Wechsel von vuex zu pinia und zu meteor Komponenten im Admin 
- Vite Konfigurations-Anpassung
- Code clean-up, Fehlerbehebungen unnd Optimierungen

# 3.7.1

**Changelog**
- Ein Fehler im Admin wurde behoben, bei dem der Inhaltselement-Tab in den Slide Einstellungen nicht geöffnet werden konnte

# 3.7.0

**Changelog**
- Ein Fehler in der Slide Auswahl des Slider Elements wurde behoben. Die Slides können nun wieder hingefügt und entfernt werden
- Code Anpassungen und Optimierungen
  - Wechsel von vuex zu Pinia wurde vollständig umgesetzt
  - Optimierung der Komponenten Slide-Auswahl und Gerätewechsel 

# 3.6.3

**Changelog**
- Lazy loading der Sektionen styles wurde entfernt und durch statisches styling per SCSS ersetzt. Es sollte nun nicht mehr zu Darstellungsfehler in Sektionen kommen
- Ein Darstellungsfehler im Admin UI wurde behoben. Es wurden Kontextmenüs im Slide Builder Beschreibungs-Editor abgeschnitten. Es sollten nun alle Menüs erreichbar sein.

# 3.6.2

**Changelog**
- Es wurden Fehler in der Slide Bearbeitung behoben. Alle Slide Einstellungen sind wieder sichtbar

# 3.6.1

**Changelog**
- Blöcke in Standard Sektionen können nun wieder wie gewohnt ausgewählt werden

# 3.6.0

**Changelog**
- Elysium Sektion wurde hinzugefügt. Diese ist in der Erlebniswelten Sektions-Auswahl verfügbar und erweitert die Erlebniswelt um dynamisch Skalierbare Blöcke, zusammenfügen von Block-Zeilen, Änderung der optischen Block-Reihenfolge und mehr. Alle Einstellungen können seperat für die Smartphone, Tablet und Desktop Ansicht festgelegt werden
- Codebasis wurde aufgeräumt und optimiert

# 3.5.4

**Changelog**
- Die Werte in Zahlen-Eingabefeldern werden nun korrekt übernommen. Die entsprechenden Komponenten wurden angepasst und sollten auch ab Shopware 6.6.4 wie erwartet funktionieren
- Anpassung von Übersetzungen

# 3.5.3

**Changelog**
- Eine CSS Angabe, welche negativen Einfluss auf das Slider-Verhalten haben konnte, wurde entfernt

# 3.5.2

**Changelog**
- Änderung der Registrierung des elysium-slider JS von asynchron zu statisch, damit der Slider ab Shopware 6.6.7 wieder wie erwartet funktioniert

# 3.5.1

**Changelog**
- Mit dem Wert `0` kann die maximale Begrenzung (Breite / Höhe) in geräteabhängigen Einstellungen zurückgesetzt werden.
- Manuelles Erstellen der neuesten JS-Dateien für das Composer-Paackage
- Übersetzungen in der Administration wurden korrigiert

# 3.5.0

**Changelog**
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

**Changelog**
- Ein Fehler in der Slide Einstellung (Anzeige) "Elemente untereinander anzeigen" wurde behoben. Die Option kann nur wieder korrekt angewählt werden und funktioniert wie erwartet
- Dem Fokusbild Element wurden CSS styles hinzugefügt die eine Überschneidung mit dem Container verhindern

# 3.4.0

**Changelog**
- Das Produktbild eines Slides kann nun beim Verlinkungstyp 'Produkt' ausgeblendet werden
- Im CMS Banner Element kann nun eine maximale Höhe festgelegt werden

# 3.3.0

**Changelog**
- Änderung: Aufgrund einer Änderung des State Managers ab Shopware 6.6.4, kam es beim einfügen von Elysium Blöcken im Erlebniswelten Layout Editor zu Fehlern. Dies wurde angepasst und das einfügen von Blöcken sollte wie erwartet funktionieren.

# 3.2.1

**Changelog**
- Änderung: Die SQL Syntax der Datenbank Migration 1707906587 wurde geändert um ältere MySQL und MariaDB Versionen zu unterstützen. **Wichtiger Hinweis** Ab Version 4 werden ausnahmslos die von Shopware empholenen Datenbank Versionen unterstützt

# 3.2.0

**Changelog**
- Verbesserung: In der Medienverwaltung wird nun die Information angezeigt, in welchem Elysium Slide ein Medium verwendet wird. Beim löschen eines verknüpften Mediums erscheint entsprechender Hinweis 
- Verbesserung: Rollen Berechtigungen wurden im Elysium Slides Mobul hinzugefügt
- Verbesserung: In den Slide Einstellung wurde die Option **Bild auf volle Breite strecken** dem Bildelement hinzugefügt
- Änderung: Die Einstellung **Auto-Wiedergabe Intervall** im CMS Slider hat nun einen Minimalwer von 200 statt 3000
- Änderung: Das Fokusbild wird nun standardmäßig in automatischer satt in voller Breite angezeigt
- Fehlerbehebung: Die HTML-Tags i, u, b, strong, br und span werden nun wieder wie erwartet im Frontend angezeigt
- Fehlerbehebung: Korrektur von CSS Klassennamen in CMS Blöcken. Daraus ergeben sich Fehlerbehebungen im Styling
- Fehlerbehebung: Korrektur des Seitenverhältnis. Wenn der Inhalt des Slides das Seitenverhätlis überschreiten, passt sich die Slide Höhe entsprechend dem Inhalt an. Somit wird der Inhalt nicht mehr abgeschnitten
- Fehlerbehebung: Textbausteine in der Administration wurden korrigiert

# 3.1.1

**Changelog**
- Fehlerbehebung: Die UI-Icons in der Adminstration wurden angepasst. Diese werden auch ab Shopware 6.6.2 wieder korrekt angezeigt.

# 3.1.0

**Changelog**
- Verbesserung: Beim speichern eines Slides wird nun der Cache aller Erlebniswelten, welche ein Elysium Element zugewiesen haben, invalidiert. Somit werden Änderungen am Slide im Storefront sofort sichtbar, ohne den Cache löschen zu müssen
- Fehlerbehebung: Ein Darstellungsfehler, bei dem es zum Überlauf der Boxen im Elysium Block 'Elysium Block — 2 Spalten' kommen konnte, wurde behoben

# 3.0.1

**Changelog**
- Fehlerbehebung: Die Slide Beschreibung wird nun wie erwartet gespeichert
- Fehlerbehebung: Korrektur von Textbausteinen in der Administration

# 3.0.0

## Update Notes
Dieses Update stellt die Kompatibilität mit Shopware 6.6 her. Mit dieser Version ändert sich der Plugin Support. Version 3 erhält Funktionserweiterungen und Fehlerbehebungen. Version 2 erhält ausschließlich Fehlerbehebungen. Version 1 wird nicht mehr unterstützt und erhält keine weiteren Aktualisierungen.  

Es wurde der gesamte Code innherhalb der Administration angepasst. Wir haben dabei den Code minimiert und auf Verbessserung der Performance sowie der Nutzererfahrung geachtet. 

**Changelog**
- Verbesserung: Aktualisierung und Anpassungen der Administrations Komponenten
- Verbesserung: Der JavaScript Code der Slider im Storefront wird nun dynamisch geladen

# 2.1.0

**Changelog**

- Feature: Eine Post Update Konvertierung der Slide und Slider Einstellungen wurde hinzugefügt. Bei der Aktualisierung von Version 1.5 auf 2.1 werden somit Slide und Slider Einstellungen automatisch übernommen. **Hinweis**: Daten aus Versionen kleiner als 1.5 werden **nicht übernommen**. Wir empfehlen außerdem dringend **vor einem Update eine Datenbank Sicherung** anzulegen
- Fehlerbehebung: Fehler im Slide Template wurden behoben und das allgemeine Styling optimiert

# 2.0.0

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

**Changelog**
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

# 2.0.0

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

**Changelog**
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

# 1.5.6 

**Changelog**
- Bugfix: Ein Fehler in der Slide-Auswahl des Erlebniswelten Slider Elements wurde behoben. Bei fehlender Slide Überschrift konnten keine Slides ausgewählt werden und die Slide-Auswahl wurde nicht angezeigt. Nun sollte die gesamte Slide-Auswahl, auch ohne eine hinterlegte Slide Überschrift, wie erwartet funktionieren.

# 1.5.5 

**Changelog**
- Feature: Es ist nun möglich mehrere Slides pro Ansicht anzeigen zu lassen. Bisher war die Ansicht auf einen Slide beschränkt. Im Erlebniswelten Slider Element gibt es unter **Größen** die **Slide Verhalten** Einstellungen. Es kann festgelegt werden wie viel Slides pro Ansicht angezeigt werden sollen.

# 1.5.4 

**Changelog**
- Bugfix: Ein Fehler in der Slide-Auswahl des Erlebniswelten Slider Elements wurde behoben. Bei abweichenden Sprachen konnten keine Slides ausgewählt werden und die Slide-Auswahl wurde nicht angezeigt. Nun sollte die gesamte Slide-Auswahl, in jeder ausgewählten Sprache, wie erwartet funktionieren.

# 1.5.3 

**Changelog**
- Feature: Im Erlebniswelten Slider Element kann nun die innere Container Breite des Inhalts festgelegt werden. Mögliche Optionen sind "Breite des Seiteninhalts" oder "Volle Breite".

# 1.5.2 

**Changelog**
- Änderung: Übersetzungen im Admin wurden korrigiert
- Verbesserung: Die Darstellung des Sliders wurde optimiert. In den Slider-Einstellungen gibt es nun die Möglichkeit den Innenabstand zu konfigurieren
- Verbesserung: Die Slide Auswahl im Admin wurde optimiert. Die Drag and Drop Funktion der einzelnen Slides ist nun besser erkennbar

# 1.5.1

**Changelog**
- Ein Fehler wurde behoben, bei dem der Slider fehlerhaft dargestellt wurde

# 1.5.0

## Update Notes

**Änderung und Erweiterung der Erlebniswelten Slider Einstellungen**  
Neben Fehlerbehebungen bezieht sich dieses Update auf die Einstellungen des Erlebniswelten Elements. Wir haben eine Anpassung der Admin-Oberfläche vorgenommen und Optionen hinzugefügt. 

**Wichtiger Hinweis**
Durch diese Anpassungen ergeben sich auch Änderungen an der Datenstruktur des Erlebniswelten Elements. **[Bitte lies unsere Update-Hinweise](https://elysium-slider.blurcreative.de/de/documentation/update-notes#version-1-5-0)** zur Version 1.5.0, bevor du die Erweiterung aktualisierst.

**Changelog**
- Feature: Es ist nun möglich eine Slider Überschrift zu vergeben
- Feature: Für das Erlebniswelt Slider-Element wurden neue Einstellungen hinzugefügt
- Änderung: Die Oberfläche der Konfigurationn des Erlebniswelt Slider-Elements wurde angepasst

# 1.4.5

**Changelog**
- Änderung: Eine Code-Ausgabe im Template wurde entfernt

# 1.4.4

**Changelog**
- Änderung: Die Groß- und Kleinschreibung der Slide-Cover Medien Dateiendungen wird nun ignoriert

# 1.4.3

**Changelog**
- Änderung: Versions-Kompatibilität zu Shopware 6.5.0

# 1.4.2

**Changelog**
- Bugfix: Die Anzeige der Slide-Cover Hintergrundbilder funktioniert nun wieder wie erwartet

# 1.4.1

**Changelog**
- Änderung: Die Slider-Overlay Option ist nun Standardgemäß inaktiv

# 1.4.0

## Update Notes

**Hinweis für Entwickler**  
Das Slide Template wurde refraktoriert. Templates für Slide Komponenten befinden sich nun unter `storefront/component/blur-elysium-slide/`.
Das Template für das gesamte CMS-Element befindet sich nach wie vor unter `storefront/element/cms-element-blur-elysium-slider.html.twig`.

**Changelog**
- Feature: In den Slide-Einstellungen gibt es nun den "Erweitert" Tab. Dieser wird erweiterte Einstellungen eines Slides enthalten
- Feature: Pro Slide kann eine individuelle Twig-Template Datei definiert werden. Dies befindet sich im "Erweitert" Tab der Slide-Einstellungen (#44)
- Verbesserung: Optimierung der Slide-Auswahl Ansicht im Elysium Slider CMS-Element (#55)
- Verbesserung: Optimierung und Anpassung der ACL Rollenverteilung für Admin-Benutzer (#69)
- Änderung: Das Slide Template wurde refraktoriert
- Bugfix: Der 'keine Slides vorhanden' Dialog im Elysium Slider CMS-Element erscheint nun wie erwartet (#53)
- Bugfix: Behebung falscher Thumbnail-Reihenfolge im Frontend (#57)
- Bugfix: Der Slide Button wird nun ausgeblendet wenn die URL-Overlay Option aktiv ist (#63)

# 1.3.1

**Changelog**
- Feature: In Slide Überschrift werden die HTML Tags br, i, u, b, strong und span akzeptiert (#50)
- Bugfix: Title Attribut im Slide URL Overlay Template wurde korrigiert (#51 - Danke an Alexander Pankow)
- Bugfix: Text-indent im Slide URL Overlay Template ist nun ein absoluter Wert (#51 - Danke an Alexander Pankow)

# 1.3.0

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

**Changelog**
- Feature: Neue Slide-Auswahl im Erlebniswelt-Element (#11)
- Feature: Untersützung von Videos als Slide-Cover (#9)
- Bugfix: Maskieren von CSS Funktionen in `Resources/app/storefront/src/scss/_elysium-slider.scss 115:26` (#40)
- Bugfix: Double Quotes im background-image inline CSS in `Resources/views/storefront/element/blur-elysium-slide-media.html.twig` hinzugefügt (#41)
- Bugfix: Kontext-Menü Aktionen im Medien-Menü hinzugefügt (#43)
- Bugfix: Speichern des Slide Überschriften-HTML-Elements sowie -Textfarbe im Nachgang möglich (#49)