#
# Uncomment following for Solaris compile (need gnu make for top level
# make file)
#
#OSFLAG = -DSOLARIS
#OSLIB = -lnsl -lsocket

CC =		gcc
#CFLAGS =	-O2 -Wall -ggdb -DVERSION=\"$(VERSION)\" ${OSFLAG}
CFLAGS =	-Wall -ggdb -DVERSION=\"$(VERSION)\" ${OSFLAG} -lmemcached
FTPPROXY =	main.o config.o ftp.o daemon.o ip-lib.o lib.o hashtab.o memcached.o
TARGETS =	ftpproxy

all: $(TARGETS)

$(FTPPROXY): %.o: %.c
	$(CC) -c $(CFLAGS) $< -o $@

ftpproxy:	$(FTPPROXY)
	$(CC) $(CFLAGS) -o $@ $(FTPPROXY) ${OSLIB}
	
clean:
	rm -f *.o cut out $(TARGETS) $(TAR).tgz
