CC = gcc
#CFLAGS = -O2 -Wall -ggdb
CFLAGS = -Wall -ggdb
OBJS = main.o rbtree.o
TARGETS = rbtree

all: $(TARGETS)

rbtree: $(OBJS)
	$(CC) -o $@ $(OBJS)

clean:
	rm -f *.o $(TARGETS)
