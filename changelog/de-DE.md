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