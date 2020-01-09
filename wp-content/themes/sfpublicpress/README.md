# SFPP child theme

Navigation modifications:

- [x] disable global nav via the constant `SHOW_GLOBAL_NAV`
- [x] new main nav partial
- [x] dequeue Largo nav js
- [x] dequeue Largo sticky nav partial
- [ ] custom nav js borrowing from Largo that collapses the overflow behind a "Menu" button that reveals all
	- [x] stickyNavEl 
	- [x] stickyNavWrapper
	- NOPE not gonna put the close button in .nav-right
	- [ ] Remove show/hide logic while scrolling
	- isMobile is predicated on the visibiliy of the button
	- [ ] remove caretwidth
	- [ ] remove rightwidth
	- [ ] remove creation of overflow menu item
	- [ ] add class "overflowed" to parent
	- [ ] add class "overflow" to menu item
	- [ ] .overflowed .overflow { display: none }
	- [ ] .overflowed.open .overflow { display: block }