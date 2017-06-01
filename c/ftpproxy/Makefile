
VERSION =	1.2.3
DIR =		ftpproxy
TAR =		ftpproxy-$(VERSION)

export VERSION

TARGETS =	ftpproxy

INSPATH=$(DD)/usr/local/ftpproxy/


all:	$(TARGETS)
	cd src; make all 


install:	all
	mkdir -p $(INSPATH)/bin/
	cd src; strip $(TARGETS) && cp $(TARGETS) $(INSPATH)bin/


ftpproxy:
	cd src; make ftpproxy 


tar:		clean
	cd ..; tar cvzf $(TAR).tgz $(DIR)
	mv ../$(TAR).tgz .
	
clean:
	cd src; rm -f *.o cut out $(TARGETS)
	rm -f $(TAR).tgz
	rm -f src/tags
