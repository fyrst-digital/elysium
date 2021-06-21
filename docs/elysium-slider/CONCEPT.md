# App Anatomy and Concept

## Entity: Elysium Slides
- Neue Entität `elysium_slides`
- Dort werden einzelne Slides und dessen Inhalt / Aussehen festgelegt
- Diese Entität wird im Admin unter `Inhalt` -> `Elysium Slides` angezeigt
- Anzeige der einzelnen Slides als Liste wie z.b. unter Produkte mit Möglichkeit der Bearbeitung des Slides (Bearbeitungsseite)

### To-Dos
- Erstellung der Entität `elysium_slides`
- Erstellung einer Admin Komponente für Slides Liste
- Erstellung einer Admin Komponente für Slide Detail Bearbeitung

## CMS Element: Elysium Slider
- Neues CMS Element `Elysium Slider`
- In diesem Element werden hauptsächlich die einzelnen Slides ausgewählt. Durch Enitiy Multi-Selection wie unter Pluugin Konfiguration im IrishTheme. Im Prinzip gleiche Vue Komponente
- Optional: Einstellung für Items pro Slide