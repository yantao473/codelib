CC = gcc
CFLAGS = -O0 -Wall -ggdb
OBJS = main.o rbtree.o
TARGETS = rbtree

all: $(TARGETS)

rbtree: $(OBJS)
	$(CC) $(CFLAGS) -o $@ $(OBJS)
.c.o:
	$(CC) $(CFLAGS) -c $<

clean:
	rm -f *.o $(TARGETS)
